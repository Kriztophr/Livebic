import { createHmac, timingSafeEqual } from "node:crypto";
import type { FastifyInstance } from "fastify";
import { sha256Hex, validateSplits, type SplitShare } from "@livebic/core";
import { z } from "zod";
import { currentUser, requireUser, type AppContext } from "../context";
import { badRequest, conflict, forbidden, HttpError, notFound } from "../errors";
import { newId } from "../ids";
import type { Release, User } from "../store";

const AUDIO_TYPES = ["audio/mpeg", "audio/mp3", "audio/wav", "audio/x-wav", "audio/wave"];
const MAX_AUDIO_BYTES = 100 * 1024 * 1024;
const SIGNED_URL_TTL_S = 15 * 60;

const releaseSchema = z.object({
  title: z.string().min(1).max(120),
  description: z.string().max(5000).default(""),
  lyrics: z.string().max(20000).default(""),
  access: z.enum(["public", "supporters", "paid"]).default("public"),
  priceKobo: z.number().int().min(10_000).nullable().default(null),
  collaborators: z
    .array(z.object({ userId: z.string(), bps: z.number().int().positive() }))
    .max(10)
    .default([]),
  edition: z.object({ size: z.number().int().min(1).max(10_000), priceKobo: z.number().int().min(10_000) }).nullable().default(null),
  /** Creator warrants they own or have licensed the work (spec: Compliance — Content). */
  rightsWarranty: z.literal(true),
});

export function signMediaUrl(ctx: AppContext, key: string, nowMs: number): string {
  const exp = Math.floor(nowMs / 1000) + SIGNED_URL_TTL_S;
  const sig = createHmac("sha256", ctx.config.signedUrlSecret).update(`${key}:${exp}`).digest("hex");
  return `${ctx.config.publicApiUrl}/media/${encodeURIComponent(key)}?exp=${exp}&sig=${sig}`;
}

function verifyMediaSig(ctx: AppContext, key: string, exp: string, sig: string, nowMs: number): boolean {
  if (!/^\d+$/.test(exp) || Number(exp) * 1000 < nowMs) return false;
  const expected = Buffer.from(createHmac("sha256", ctx.config.signedUrlSecret).update(`${key}:${exp}`).digest("hex"));
  const given = Buffer.from(sig);
  return expected.length === given.length && timingSafeEqual(expected, given);
}

export function canListen(ctx: AppContext, release: Release, viewer: User | null): boolean {
  if (release.access === "public") return true;
  if (!viewer) return false;
  const artist = ctx.store.artists.get(release.artistId);
  if (viewer.role === "admin" || artist?.ownerUserId === viewer.id) return true;
  const now = ctx.now();
  const paid = ctx.store.paidOrders((o) => o.fanId === viewer.id && o.artistId === release.artistId);
  if (release.access === "supporters") return paid.length > 0;
  return paid.some(
    (o) =>
      ((o.kind === "unlock" || o.kind === "drop") && o.releaseId === release.id) ||
      (o.kind === "membership" && o.membershipEndsAt !== null && o.membershipEndsAt > now),
  );
}

export function releaseView(ctx: AppContext, r: Release, viewer: User | null) {
  const artist = ctx.store.artists.get(r.artistId);
  return {
    id: r.id,
    title: r.title,
    description: r.description,
    lyrics: canListen(ctx, r, viewer) ? r.lyrics : null,
    access: r.access,
    priceKobo: r.priceKobo,
    edition: r.edition ? { size: r.edition.size, remaining: r.edition.size - r.edition.sold, priceKobo: r.edition.priceKobo } : null,
    status: r.status,
    publishedAt: r.publishedAt,
    artist: artist ? { id: artist.id, handle: artist.handle, displayName: artist.displayName } : null,
    // Proof of authorship: the fingerprint and its registry record, shown as "Authorship record".
    authorship: r.audio ? { sha256: r.audio.sha256, registryRef: r.audio.registryTx } : null,
    canListen: canListen(ctx, r, viewer),
  };
}

function ownRelease(ctx: AppContext, user: User, id: string): Release {
  const r = ctx.store.releases.get(id);
  if (!r) throw notFound("Release");
  if (ctx.store.artists.get(r.artistId)?.ownerUserId !== user.id) throw forbidden();
  return r;
}

export async function registerContent(app: FastifyInstance, ctx: AppContext) {
  const { store } = ctx;

  app.post("/v1/releases", async (req, reply) => {
    const user = requireUser(ctx, req, "artist");
    if (!user.artistId) throw forbidden("Only artists can publish");
    const body = releaseSchema.parse(req.body);
    if (body.access === "paid" && body.priceKobo === null) throw badRequest("price_required", "Paid releases need a price");

    const collaboratorBps = body.collaborators.reduce((s, c) => s + c.bps, 0);
    if (collaboratorBps >= 10_000) throw badRequest("invalid_splits", "Collaborator shares must leave a share for you");
    for (const c of body.collaborators) {
      if (!store.users.has(c.userId)) throw badRequest("unknown_collaborator", `No Livebic account ${c.userId}`);
    }
    const splits: SplitShare[] = [
      { payeeId: user.id, role: "creator", bps: 10_000 - collaboratorBps },
      ...body.collaborators.map((c) => ({ payeeId: c.userId, role: "collaborator" as const, bps: c.bps })),
    ];
    try {
      validateSplits(splits);
    } catch (e) {
      throw badRequest("invalid_splits", (e as Error).message);
    }

    const now = ctx.now();
    const release: Release = {
      id: newId("rel"),
      artistId: user.artistId,
      title: body.title,
      description: body.description,
      lyrics: body.lyrics,
      access: body.access,
      priceKobo: body.priceKobo,
      splits,
      edition: body.edition ? { ...body.edition, sold: 0 } : null,
      audio: null,
      status: "draft",
      rightsWarrantedAt: now,
      createdAt: now,
      publishedAt: null,
    };
    store.releases.set(release.id, release);
    return reply.code(201).send({ ...releaseView(ctx, release, user), splits });
  });

  app.register(async (scope) => {
    scope.addContentTypeParser(AUDIO_TYPES, { parseAs: "buffer", bodyLimit: MAX_AUDIO_BYTES }, (_req, body, done) => done(null, body));
    scope.put<{ Params: { id: string } }>("/v1/releases/:id/audio", { bodyLimit: MAX_AUDIO_BYTES }, async (req) => {
      const user = requireUser(ctx, req, "artist");
      const release = ownRelease(ctx, user, req.params.id);
      if (release.status !== "draft") throw conflict("already_published", "Audio can't change after publishing");
      const contentType = String(req.headers["content-type"]).split(";")[0]!;
      const body = req.body as Buffer;
      if (!Buffer.isBuffer(body) || body.length === 0) throw badRequest("empty_upload", "Upload an MP3 or WAV file");

      const fingerprint = sha256Hex(body);
      const objectKey = `audio/${release.id}/${fingerprint}`;
      await ctx.partners.storage.put(objectKey, body, contentType);
      const { txRef } = await ctx.partners.chain.recordContentHash({ releaseId: release.id, sha256: fingerprint });
      release.audio = { objectKey, contentType, sha256: fingerprint, registryTx: txRef, bytes: body.length };
      // Transcoding to streaming renditions runs as a pipeline job in production.
      return { sha256: fingerprint, registryRef: txRef, bytes: body.length };
    });
  });

  app.post<{ Params: { id: string } }>("/v1/releases/:id/publish", async (req) => {
    const user = requireUser(ctx, req, "artist");
    const release = ownRelease(ctx, user, req.params.id);
    if (!release.audio) throw badRequest("audio_required", "Upload audio before publishing");
    if (release.status === "removed") throw forbidden("This release was removed by moderation");
    if (release.status === "draft") {
      release.status = "published";
      release.publishedAt = ctx.now();
    }
    return releaseView(ctx, release, user);
  });

  app.get<{ Params: { id: string } }>("/v1/releases/:id", async (req) => {
    const r = store.releases.get(req.params.id);
    const viewer = currentUser(ctx, req);
    if (!r || (r.status !== "published" && store.artists.get(r.artistId)?.ownerUserId !== viewer?.id)) throw notFound("Release");
    return releaseView(ctx, r, viewer);
  });

  app.get<{ Params: { id: string } }>("/v1/releases/:id/stream", async (req) => {
    const r = store.releases.get(req.params.id);
    if (!r || r.status !== "published" || !r.audio) throw notFound("Release");
    const viewer = currentUser(ctx, req);
    if (!canListen(ctx, r, viewer)) throw new HttpError(402, "support_required", "Support this artist to listen");
    return { url: signMediaUrl(ctx, r.audio.objectKey, ctx.now().getTime()), expiresInSeconds: SIGNED_URL_TTL_S };
  });

  app.get<{ Params: { "*": string }; Querystring: { exp?: string; sig?: string } }>("/media/*", async (req, reply) => {
    const key = decodeURIComponent(req.params["*"]);
    if (!verifyMediaSig(ctx, key, req.query.exp ?? "", req.query.sig ?? "", ctx.now().getTime())) {
      throw new HttpError(403, "link_expired", "This link has expired");
    }
    const obj = await ctx.partners.storage.get(key);
    if (!obj) throw notFound("File");
    return reply.header("content-type", obj.contentType).header("cache-control", "private, max-age=900").send(obj.body);
  });

  app.post("/v1/reports", async (req, reply) => {
    const user = requireUser(ctx, req);
    const body = z.object({ releaseId: z.string(), reason: z.string().min(3).max(1000) }).parse(req.body);
    if (!store.releases.has(body.releaseId)) throw notFound("Release");
    const id = newId("rpt");
    store.reports.set(id, { id, reporterId: user.id, releaseId: body.releaseId, reason: body.reason, status: "open", createdAt: ctx.now() });
    return reply.code(201).send({ id });
  });
}
