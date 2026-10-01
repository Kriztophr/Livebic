import { randomBytes } from "node:crypto";
import type { FastifyInstance } from "fastify";
import { TIP_AMOUNTS_KOBO } from "@livebic/core";
import { z } from "zod";
import { currentUser, requireUser, type AppContext } from "../context";
import { badRequest, conflict, HttpError, notFound } from "../errors";
import { newId } from "../ids";
import type { Artist, User } from "../store";

const handleSchema = z
  .string()
  .min(2)
  .max(30)
  .regex(/^[a-z0-9_]+$/, "Use lowercase letters, numbers and underscores");

const signupSchema = z.object({
  email: z.string().email(),
  name: z.string().min(1).max(80),
  role: z.enum(["fan", "artist"]).default("fan"),
  handle: handleSchema.optional(),
});

export function publicUser(u: User) {
  return { id: u.id, name: u.name, email: u.email, role: u.role, artistId: u.artistId, createdAt: u.createdAt };
}

export function publicArtist(ctx: AppContext, a: Artist) {
  const supporters = new Set(ctx.store.paidOrders((o) => o.artistId === a.id).map((o) => o.fanId));
  let followers = 0;
  for (const set of ctx.store.follows.values()) if (set.has(a.id)) followers++;
  return {
    id: a.id,
    handle: a.handle,
    displayName: a.displayName,
    bio: a.bio,
    links: a.links,
    verified: a.verification === "verified",
    supporterCount: supporters.size,
    followerCount: followers,
    membershipPriceKobo: a.membershipPriceKobo,
    shortLink: `${ctx.config.webUrl}/@${a.handle}`,
    createdAt: a.createdAt,
  };
}

function issueSession(ctx: AppContext, userId: string): string {
  const token = randomBytes(24).toString("base64url");
  ctx.store.sessions.set(token, userId);
  return token;
}

export async function registerIdentity(app: FastifyInstance, ctx: AppContext) {
  const { store } = ctx;

  app.post("/v1/auth/signup", async (req, reply) => {
    const body = signupSchema.parse(req.body);
    const email = body.email.toLowerCase();
    if (store.userByEmail(email)) throw conflict("email_taken", "An account with this email already exists");
    if (body.role === "artist") {
      if (!body.handle) throw badRequest("handle_required", "Choose a handle for your artist page");
      if (store.artistByHandle(body.handle)) throw conflict("handle_taken", "That handle is taken");
    }

    const userId = newId("usr");
    // The wallet is created in the background of sign-up with no user action (principle 1).
    const wallet = await ctx.partners.wallets.createWallet(userId);
    const now = ctx.now();
    const user: User = {
      id: userId,
      email,
      name: body.name,
      role: ctx.config.adminEmails.includes(email) ? "admin" : body.role,
      createdAt: now,
      verified: ctx.config.sandbox,
      walletId: wallet.walletId,
      walletAddress: wallet.address,
      artistId: null,
    };
    if (body.role === "artist" && body.handle) {
      const artist: Artist = {
        id: newId("art"),
        ownerUserId: userId,
        handle: body.handle,
        displayName: body.name,
        bio: "",
        links: [],
        createdAt: now,
        verification: "unverified",
        verificationRequest: null,
        membershipPriceKobo: null,
        payout: null,
        showWalletAddress: false,
      };
      store.artists.set(artist.id, artist);
      user.artistId = artist.id;
    }
    store.users.set(user.id, user);
    return reply.code(201).send({ token: issueSession(ctx, user.id), user: publicUser(user) });
  });

  app.post("/v1/auth/login", async (req) => {
    const { email } = z.object({ email: z.string().email() }).parse(req.body);
    if (!ctx.config.sandbox) {
      // Production sign-in is a passkey (WebAuthn) or an emailed one-time link.
      throw new HttpError(501, "not_implemented", "Passkey and email-link sign-in are not wired yet");
    }
    const user = store.userByEmail(email);
    if (!user) throw notFound("Account");
    return { token: issueSession(ctx, user.id), user: publicUser(user) };
  });

  app.get("/v1/me", async (req) => {
    const user = requireUser(ctx, req);
    const artist = user.artistId ? store.artists.get(user.artistId) : undefined;
    return {
      user: publicUser(user),
      artist: artist
        ? {
            ...publicArtist(ctx, artist),
            verification: artist.verification,
            payout: artist.payout,
            showWalletAddress: artist.showWalletAddress,
            // Hidden unless the creator opts in under "advanced".
            walletAddress: artist.showWalletAddress ? user.walletAddress : null,
          }
        : null,
      balanceKobo: store.balance(user.id),
    };
  });

  const profileSchema = z.object({
    displayName: z.string().min(1).max(80).optional(),
    bio: z.string().max(2000).optional(),
    links: z.array(z.string().url()).max(10).optional(),
    membershipPriceKobo: z.number().int().min(10_000).nullable().optional(),
    payout: z.object({ method: z.enum(["bank", "stablecoin"]), destination: z.string().min(4).max(200) }).nullable().optional(),
    showWalletAddress: z.boolean().optional(),
  });

  app.patch("/v1/artists/me", async (req) => {
    const user = requireUser(ctx, req, "artist");
    const artist = user.artistId ? store.artists.get(user.artistId) : undefined;
    if (!artist) throw notFound("Artist profile");
    Object.assign(artist, profileSchema.parse(req.body));
    return publicArtist(ctx, artist);
  });

  app.post("/v1/artists/me/verification", async (req, reply) => {
    const user = requireUser(ctx, req, "artist");
    const artist = user.artistId ? store.artists.get(user.artistId) : undefined;
    if (!artist) throw notFound("Artist profile");
    const body = z
      .object({ idDocumentRef: z.string().min(4), socialProofUrls: z.array(z.string().url()).min(1).max(5) })
      .parse(req.body);
    if (artist.verification === "verified") throw conflict("already_verified", "Already verified");
    artist.verification = "pending";
    artist.verificationRequest = { ...body, submittedAt: ctx.now() };
    return reply.code(202).send({ verification: artist.verification });
  });

  app.get<{ Params: { handle: string } }>("/v1/artists/:handle", async (req) => {
    const artist = store.artistByHandle(req.params.handle);
    if (!artist) throw notFound("Artist");
    const viewer = currentUser(ctx, req);
    const releases = [...store.releases.values()]
      .filter((r) => r.artistId === artist.id && r.status === "published")
      .sort((a, b) => (b.publishedAt?.getTime() ?? 0) - (a.publishedAt?.getTime() ?? 0))
      .map((r) => ({
        id: r.id,
        title: r.title,
        access: r.access,
        priceKobo: r.priceKobo,
        edition: r.edition ? { size: r.edition.size, remaining: r.edition.size - r.edition.sold, priceKobo: r.edition.priceKobo } : null,
        publishedAt: r.publishedAt,
      }));
    return {
      artist: publicArtist(ctx, artist),
      releases,
      tipAmountsKobo: TIP_AMOUNTS_KOBO,
      viewer: viewer
        ? { following: store.follows.get(viewer.id)?.has(artist.id) ?? false, supporter: store.isSupporter(viewer.id, artist.id) }
        : null,
    };
  });

  app.post<{ Params: { id: string } }>("/v1/artists/:id/follow", async (req, reply) => {
    const user = requireUser(ctx, req);
    const artist = store.artists.get(req.params.id);
    if (!artist) throw notFound("Artist");
    const set = store.follows.get(user.id) ?? new Set<string>();
    if (!set.has(artist.id)) {
      set.add(artist.id);
      store.follows.set(user.id, set);
      store.events.push({ id: newId("evt"), type: "follow", actorId: user.id, artistId: artist.id, itemId: null, at: ctx.now() });
    }
    return reply.code(204).send();
  });

  app.delete<{ Params: { id: string } }>("/v1/artists/:id/follow", async (req, reply) => {
    const user = requireUser(ctx, req);
    store.follows.get(user.id)?.delete(req.params.id);
    return reply.code(204).send();
  });
}
