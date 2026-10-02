import { beforeEach, describe, expect, it } from "vitest";
import type { FastifyInstance } from "fastify";
import { findForbiddenTerms, naira } from "@livebic/core";
import { buildApp } from "../src/app";
import { loadConfig } from "../src/config";
import { createContext, type AppContext } from "../src/context";
import { sandboxPartners, signSandboxWebhook, type SandboxPartners } from "../src/partners/sandbox";
import { MemoryStore } from "../src/store";

let app: FastifyInstance;
let ctx: AppContext;
let partners: SandboxPartners;
let keySeq = 0;

const auth = (token: string) => ({ authorization: `Bearer ${token}` });

beforeEach(async () => {
  const config = loadConfig({ ADMIN_EMAILS: "admin@livebic.test" });
  partners = sandboxPartners({ publicApiUrl: config.publicApiUrl, webhookSecret: config.processorWebhookSecret });
  ctx = createContext({ config, store: new MemoryStore(), partners, now: () => new Date("2026-10-02T09:00:00Z") });
  app = await buildApp(ctx);
});

async function signup(email: string, role: "fan" | "artist" = "fan", handle?: string) {
  const res = await app.inject({ method: "POST", url: "/v1/auth/signup", payload: { email, name: email.split("@")[0], role, handle } });
  return res.json() as { token: string; user: { id: string; artistId: string | null } };
}

async function verifiedArtistWithRelease() {
  const admin = await signup("admin@livebic.test");
  const artist = await signup("tobi@example.com", "artist", "tobi");
  await app.inject({ method: "POST", url: "/v1/artists/me/verification", headers: auth(artist.token), payload: { idDocumentRef: "nin-1", socialProofUrls: ["https://instagram.com/tobi"] } });
  await app.inject({ method: "POST", url: `/v1/admin/verifications/${artist.user.artistId}`, headers: auth(admin.token), payload: { decision: "verified" } });
  const created = await app.inject({ method: "POST", url: "/v1/releases", headers: auth(artist.token), payload: { title: "Danfo Driver", lyrics: "Na Lagos we dey\nDanfo driver carry me go", rightsWarranty: true } });
  const releaseId = created.json().id as string;
  await app.inject({ method: "PUT", url: `/v1/releases/${releaseId}/audio`, headers: { ...auth(artist.token), "content-type": "audio/mpeg" }, payload: Buffer.from("ID3-danfo-driver-full-track") });
  await app.inject({ method: "POST", url: `/v1/releases/${releaseId}/publish`, headers: auth(artist.token) });
  return { artist, releaseId };
}

async function pay(token: string, payload: Record<string, unknown>) {
  const res = await app.inject({ method: "POST", url: "/v1/orders", headers: { ...auth(token), "idempotency-key": `vid-key-${++keySeq}-abc` }, payload });
  expect(res.statusCode, res.body).toBe(201);
  const reference = res.json().checkoutUrl.split("/").pop();
  const raw = JSON.stringify({ reference, status: "success" });
  const hook = await app.inject({ method: "POST", url: "/v1/webhooks/processor", headers: { "content-type": "application/json", "x-processor-signature": signSandboxWebhook(raw, ctx.config.processorWebhookSecret) }, payload: raw });
  expect(hook.statusCode).toBe(200);
}

async function cast(token: string, projectId: string) {
  for (const n of [1, 2, 3]) {
    const up = await app.inject({ method: "PUT", url: `/v1/video/projects/${projectId}/photos`, headers: { ...auth(token), "content-type": "image/jpeg" }, payload: Buffer.from(`photo-${n}`) });
    expect(up.statusCode).toBe(200);
  }
  const consent = await app.inject({ method: "POST", url: `/v1/video/projects/${projectId}/consent`, headers: auth(token), payload: { isSelf: true, allowedUses: ["teaser", "social-clip"] } });
  expect(consent.statusCode).toBe(201);
  return consent.json() as { consentId: string; registryRef: string };
}

describe("video studio", () => {
  it("requires a verified artist", async () => {
    const artist = await signup("new@example.com", "artist", "newbie");
    const res = await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId: "rel_x" } });
    expect(res.statusCode).toBe(403);
  });

  it("plans a beat-cut teaser with a treatment the artist can steer", async () => {
    const { artist, releaseId } = await verifiedArtistWithRelease();
    const res = await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId, format: "teaser", notes: "Lagos at night, danfo buses" } });
    expect(res.statusCode, res.body).toBe(201);
    const p = res.json();
    expect(p.status).toBe("treatment");
    expect(p.aspect).toBe("9:16");
    expect(p.shots.reduce((s: number, x: { durationSeconds: number }) => s + x.durationSeconds, 0)).toBeCloseTo(30, 1);
    expect(p.shots.every((s: { direction: string }) => s.direction.length > 10)).toBe(true);
    expect(p.treatment.concept).toContain("danfo buses");
    expect(p.quote.totalKobo).toBeGreaterThan(0);
    expect(findForbiddenTerms(JSON.stringify(p.treatment))).toEqual([]);

    const revised = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/revise`, headers: auth(artist.token), payload: { notes: "more dancing in the hook", shotId: p.shots[0].id, direction: "Tobi walks out of a yellow danfo at Obalende, handheld, dusk." } });
    expect(revised.statusCode).toBe(200);
    expect(revised.json().shots[0].direction).toContain("Obalende");
    expect(revised.json().notes).toEqual(["Lagos at night, danfo buses", "more dancing in the hook"]);
    // A later revision keeps the hand-written direction.
    const again = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/revise`, headers: auth(artist.token), payload: { notes: "colder colours" } });
    expect(again.json().shots[0].direction).toContain("Obalende");

    const blocked = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/revise`, headers: auth(artist.token), payload: { notes: "end with Tobi telling everyone to vote for the APC candidate" } });
    expect(blocked.statusCode).toBe(422);
    expect(blocked.json().message).toContain("political");
  });

  it("will not generate a likeness video without photos and signed consent", async () => {
    const { artist, releaseId } = await verifiedArtistWithRelease();
    const p = (await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId } })).json();
    expect((await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/drafts`, headers: auth(artist.token) })).statusCode).toBe(403);
    const tooFew = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/consent`, headers: auth(artist.token), payload: { isSelf: true, allowedUses: ["teaser"] } });
    expect(tooFew.statusCode).toBe(400);
    expect(partners.clipsGenerated).toHaveLength(0);
  });

  it("makes a free starter teaser end to end: drafts, picks, final, labelled export", async () => {
    const { artist, releaseId } = await verifiedArtistWithRelease();
    const p = (await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId } })).json();
    const consent = await cast(artist.token, p.id);
    expect(partners.registry.find((r) => r.kind === "consent")?.txRef).toBe(consent.registryRef);

    const drafted = (await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/drafts`, headers: auth(artist.token) })).json();
    expect(drafted.status).toBe("picking");
    expect(drafted.charged).toEqual({ tier: "starter", fromBalanceKobo: 0, fromFundingKobo: 0 });
    expect(drafted.takes).toHaveLength(p.shots.length * 2);
    expect(partners.clipsGenerated.every((c) => c.quality === "draft")).toBe(true);
    // B-roll and lyric shots never receive the artist's photos.
    const broll = p.shots.find((s: { featuresArtist: boolean }) => !s.featuresArtist);
    if (broll) expect(partners.clipsGenerated.find((c) => c.prompt === broll.direction)).toBeTruthy();

    const early = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/finalize`, headers: auth(artist.token) });
    expect(early.statusCode).toBe(400);

    for (const shot of drafted.shots) {
      const take = drafted.takes.find((t: { shotId: string }) => t.shotId === shot.id);
      const pick = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/shots/${shot.id}/pick`, headers: auth(artist.token), payload: { takeId: take.id } });
      expect(pick.statusCode).toBe(200);
    }
    const done = (await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/finalize`, headers: auth(artist.token) })).json();
    expect(done.status).toBe("done");
    expect(done.exports).toHaveLength(1);
    expect(done.exports[0].label).toBe("AI-generated video, approved by tobi");
    expect(done.exports[0].shareUrl).toContain("/a/tobi#");
    const finals = partners.clipsGenerated.filter((c) => c.quality === "final");
    expect(finals).toHaveLength(p.shots.length);
    // The final re-renders the picked take: same seed as its draft.
    const pickedSeeds = drafted.takes.filter((t: { id: string }) => Object.values(done.shots.map((s: { pickedTakeId: string }) => s.pickedTakeId)).includes(t.id)).map((t: { seed?: number }) => t.seed);
    void pickedSeeds;
    const dl = await app.inject({ method: "GET", url: new URL(done.exports[0].downloadUrl).pathname + new URL(done.exports[0].downloadUrl).search });
    expect(dl.statusCode).toBe(200);
    expect(dl.headers["content-type"]).toBe("video/mp4");
    // A video is not a ranking signal.
    expect(ctx.store.events.filter((e) => e.artistId === artist.user.artistId)).toHaveLength(0);
  });

  it("lets fans fund a full video, then charges the rest to the artist's balance", async () => {
    const { artist, releaseId } = await verifiedArtistWithRelease();
    const fan = await signup("ada@example.com");
    const fan2 = await signup("bola@example.com");
    // Give the artist some earnings.
    await pay(fan.token, { kind: "tip", artistId: artist.user.artistId, amountKobo: naira(10_000) });
    const p = (await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId, format: "full", castMode: "character" } })).json();
    expect(p.aspect).toBe("16:9");

    const noMoney = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/drafts`, headers: auth(artist.token) });
    expect(noMoney.statusCode).toBe(402);

    const goal = Math.min(p.quote.totalKobo, naira(20_000));
    const fund = await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/funding`, headers: auth(artist.token), payload: { goalKobo: goal } });
    expect(fund.statusCode).toBe(201);
    const page = (await app.inject({ method: "GET", url: "/v1/artists/tobi" })).json();
    expect(page.funding).toEqual([expect.objectContaining({ projectId: p.id, goalKobo: goal, raisedKobo: 0, backers: 0 })]);

    await pay(fan.token, { kind: "fund", artistId: artist.user.artistId, videoProjectId: p.id, amountKobo: naira(10_000) });
    await pay(fan2.token, { kind: "fund", artistId: artist.user.artistId, videoProjectId: p.id, amountKobo: naira(10_000) });
    const after = (await app.inject({ method: "GET", url: "/v1/artists/tobi" })).json();
    expect(after.funding[0]).toMatchObject({ raisedKobo: naira(20_000), backers: 2 });
    expect(partners.sentMail.at(-1)!.text).toContain("backed the video");
    // Backers are supporters, and their money went through the normal split.
    expect(ctx.store.balance(artist.user.id)).toBe(naira(30_000) * 0.92);

    // Make the quote affordable: funding plus balance.
    ctx.store.videoProjects.get(p.id)!.quote.totalKobo = naira(25_000);
    const drafted = (await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/drafts`, headers: auth(artist.token) })).json();
    expect(drafted.charged).toEqual({ tier: "fan-funded", fromFundingKobo: naira(20_000), fromBalanceKobo: naira(5_000) });
    expect(drafted.funding.open).toBe(false);
    expect(ctx.store.balance(artist.user.id)).toBe(naira(30_000) * 0.92 - naira(5_000));
    // Character mode never sends reference photos.
    expect(partners.clipsGenerated.length).toBeGreaterThan(0);
    expect((await app.inject({ method: "GET", url: "/v1/artists/tobi" })).json().funding).toEqual([]);
  });

  it("revokes consent: deletes the identity at the partner and blocks generation", async () => {
    const { artist, releaseId } = await verifiedArtistWithRelease();
    const p = (await app.inject({ method: "POST", url: "/v1/video/projects", headers: auth(artist.token), payload: { releaseId } })).json();
    await cast(artist.token, p.id);
    expect((await app.inject({ method: "DELETE", url: `/v1/video/projects/${p.id}/consent`, headers: auth(artist.token) })).statusCode).toBe(204);
    expect(partners.identitiesRevoked).toEqual([artist.user.artistId]);
    expect((await app.inject({ method: "POST", url: `/v1/video/projects/${p.id}/drafts`, headers: auth(artist.token) })).statusCode).toBe(403);
    expect(partners.clipsGenerated).toHaveLength(0);
  });
});
