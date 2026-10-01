import { beforeEach, describe, expect, it } from "vitest";
import type { FastifyInstance } from "fastify";
import { findForbiddenTerms, naira } from "@livebic/core";
import { buildApp } from "../src/app";
import { loadConfig } from "../src/config";
import { createContext, type AppContext } from "../src/context";
import { runRankingJob } from "../src/jobs/ranking";
import { sandboxPartners, signSandboxWebhook, type SandboxPartners } from "../src/partners/sandbox";
import { MemoryStore } from "../src/store";

let app: FastifyInstance;
let ctx: AppContext;
let partners: SandboxPartners;
let clock = new Date("2026-10-01T12:00:00Z");
let keySeq = 0;

beforeEach(async () => {
  clock = new Date("2026-10-01T12:00:00Z");
  const config = loadConfig({ ADMIN_EMAILS: "admin@livebic.test" });
  partners = sandboxPartners({ publicApiUrl: config.publicApiUrl, webhookSecret: config.processorWebhookSecret });
  ctx = createContext({ config, store: new MemoryStore(), partners, now: () => clock });
  app = await buildApp(ctx);
});

const auth = (token: string) => ({ authorization: `Bearer ${token}` });

async function signup(email: string, role: "fan" | "artist" = "fan", handle?: string) {
  const res = await app.inject({ method: "POST", url: "/v1/auth/signup", payload: { email, name: email.split("@")[0], role, handle } });
  expect(res.statusCode).toBe(201);
  return res.json() as { token: string; user: { id: string; artistId: string | null } };
}

async function publishRelease(token: string, payload: Record<string, unknown> = {}) {
  const created = await app.inject({ method: "POST", url: "/v1/releases", headers: auth(token), payload: { title: "Danfo Driver", rightsWarranty: true, ...payload } });
  expect(created.statusCode).toBe(201);
  const id = created.json().id as string;
  const up = await app.inject({ method: "PUT", url: `/v1/releases/${id}/audio`, headers: { ...auth(token), "content-type": "audio/mpeg" }, payload: Buffer.from("ID3-fake-mp3") });
  expect(up.statusCode).toBe(200);
  const pub = await app.inject({ method: "POST", url: `/v1/releases/${id}/publish`, headers: auth(token) });
  expect(pub.statusCode).toBe(200);
  return id;
}

async function pay(token: string, payload: Record<string, unknown>, outcome: "success" | "failed" = "success") {
  const res = await app.inject({ method: "POST", url: "/v1/orders", headers: { ...auth(token), "idempotency-key": `key-${++keySeq}-abcdef` }, payload });
  expect(res.statusCode, res.body).toBe(201);
  const { order, checkoutUrl } = res.json();
  const reference = checkoutUrl.split("/").pop();
  const raw = JSON.stringify({ reference, status: outcome });
  const hook = await app.inject({
    method: "POST",
    url: "/v1/webhooks/processor",
    headers: { "content-type": "application/json", "x-processor-signature": signSandboxWebhook(raw, ctx.config.processorWebhookSecret) },
    payload: raw,
  });
  expect(hook.statusCode).toBe(200);
  return { orderId: order.id as string, reference: reference as string, raw };
}

describe("accounts", () => {
  it("creates a wallet silently and never exposes its address by default", async () => {
    const fan = await signup("fan@example.com");
    const me = await app.inject({ method: "GET", url: "/v1/me", headers: auth(fan.token) });
    expect(JSON.stringify(me.json())).not.toMatch(/0x[0-9a-f]{40}/);
    expect(ctx.store.users.get(fan.user.id)!.walletAddress).toMatch(/^0x/);
  });
});

describe("support and payouts", () => {
  it("tips an artist end to end: split, receipt, supporter list, ranking event", async () => {
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const producer = await signup("prod@example.com");
    const releaseId = await publishRelease(artist.token, { collaborators: [{ userId: producer.user.id, bps: 2_000 }] });
    const fan = await signup("ada@example.com");

    const { orderId } = await pay(fan.token, { kind: "tip", artistId: artist.user.artistId, releaseId, amountKobo: naira(1000) });
    const order = (await app.inject({ method: "GET", url: `/v1/orders/${orderId}`, headers: auth(fan.token) })).json();
    expect(order.status).toBe("paid");

    // ₦1,000: 8% platform fee, then 80/20.
    expect(ctx.store.balance(artist.user.id)).toBe(naira(736));
    expect(ctx.store.balance(producer.user.id)).toBe(naira(184));
    expect(ctx.store.balance("platform")).toBe(naira(80));

    const verify = (await app.inject({ method: "GET", url: `/v1/receipts/${order.receipt.receiptId}/verify` })).json();
    expect(verify.valid).toBe(true);
    expect(partners.registry.map((r) => r.kind)).toEqual(["content", "receipt"]);

    expect(partners.sentMail).toHaveLength(1);
    expect(findForbiddenTerms(partners.sentMail[0]!.subject + partners.sentMail[0]!.text)).toEqual([]);

    const csv = await app.inject({ method: "GET", url: "/v1/artists/me/supporters.csv", headers: auth(artist.token) });
    expect(csv.headers["content-type"]).toContain("text/csv");
    expect(csv.body).toContain("ada,ada@example.com,supporter,1000.00,1");

    expect(ctx.store.events.at(-1)).toMatchObject({ type: "tip", itemId: releaseId });
  });

  it("is idempotent for order creation and webhook replays", async () => {
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const fan = await signup("ada@example.com");
    const payload = { kind: "tip", artistId: artist.user.artistId, amountKobo: naira(500) };
    const headers = { ...auth(fan.token), "idempotency-key": "same-key-123" };
    const a = await app.inject({ method: "POST", url: "/v1/orders", headers, payload });
    const b = await app.inject({ method: "POST", url: "/v1/orders", headers, payload });
    expect(b.json().order.id).toBe(a.json().order.id);
    const c = await app.inject({ method: "POST", url: "/v1/orders", headers, payload: { ...payload, amountKobo: naira(1000) } });
    expect(c.statusCode).toBe(422);

    const { raw } = await pay(fan.token, payload);
    const replay = await app.inject({
      method: "POST",
      url: "/v1/webhooks/processor",
      headers: { "content-type": "application/json", "x-processor-signature": signSandboxWebhook(raw, ctx.config.processorWebhookSecret) },
      payload: raw,
    });
    expect(replay.statusCode).toBe(200);
    expect(ctx.store.ledger.filter((e) => e.reason === "sale")).toHaveLength(1);
  });

  it("rejects unsigned webhooks, off-menu tips and self-support", async () => {
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const fan = await signup("ada@example.com");
    const bad = await app.inject({ method: "POST", url: "/v1/webhooks/processor", headers: { "content-type": "application/json", "x-processor-signature": "nope" }, payload: "{}" });
    expect(bad.statusCode).toBe(401);
    const odd = await app.inject({ method: "POST", url: "/v1/orders", headers: { ...auth(fan.token), "idempotency-key": "k-odd-tip-1" }, payload: { kind: "tip", artistId: artist.user.artistId, amountKobo: 123 } });
    expect(odd.statusCode).toBe(400);
    const self = await app.inject({ method: "POST", url: "/v1/orders", headers: { ...auth(artist.token), "idempotency-key": "k-self-tip-1" }, payload: { kind: "tip", artistId: artist.user.artistId, amountKobo: naira(500) } });
    expect(self.statusCode).toBe(403);
  });

  it("gates paid content and limited editions", async () => {
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const releaseId = await publishRelease(artist.token, { access: "paid", priceKobo: naira(800), edition: { size: 1, priceKobo: naira(5000) } });
    const fan = await signup("ada@example.com");
    const fan2 = await signup("bola@example.com");

    expect((await app.inject({ method: "GET", url: `/v1/releases/${releaseId}/stream`, headers: auth(fan.token) })).statusCode).toBe(402);
    await pay(fan.token, { kind: "unlock", artistId: artist.user.artistId, releaseId });
    const stream = await app.inject({ method: "GET", url: `/v1/releases/${releaseId}/stream`, headers: auth(fan.token) });
    expect(stream.statusCode).toBe(200);
    const media = await app.inject({ method: "GET", url: new URL(stream.json().url).pathname + new URL(stream.json().url).search });
    expect(media.body).toBe("ID3-fake-mp3");

    const { orderId } = await pay(fan.token, { kind: "drop", artistId: artist.user.artistId, releaseId });
    expect((await app.inject({ method: "GET", url: `/v1/orders/${orderId}`, headers: auth(fan.token) })).json().editionNumber).toBe(1);
    const soldOut = await app.inject({ method: "POST", url: "/v1/orders", headers: { ...auth(fan2.token), "idempotency-key": "k-sold-out-1" }, payload: { kind: "drop", artistId: artist.user.artistId, releaseId } });
    expect(soldOut.statusCode).toBe(409);
  });

  it("requires verification and admin approval for large payouts", async () => {
    const admin = await signup("admin@livebic.test");
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const fan = await signup("ada@example.com");
    for (let i = 0; i < 3; i++) await pay(fan.token, { kind: "tip", artistId: artist.user.artistId, amountKobo: naira(10000) });
    await app.inject({ method: "PATCH", url: "/v1/artists/me", headers: auth(artist.token), payload: { payout: { method: "bank", destination: "GTBank 0123456789" } } });

    const payout = (amountKobo: number, key: string) =>
      app.inject({ method: "POST", url: "/v1/payouts", headers: { ...auth(artist.token), "idempotency-key": key }, payload: { amountKobo } });

    expect((await payout(naira(5000), "payout-key-1")).statusCode).toBe(403);
    await app.inject({ method: "POST", url: "/v1/artists/me/verification", headers: auth(artist.token), payload: { idDocumentRef: "nin-123456", socialProofUrls: ["https://instagram.com/tobi"] } });
    await app.inject({ method: "POST", url: `/v1/admin/verifications/${artist.user.artistId}`, headers: auth(admin.token), payload: { decision: "verified" } });

    const small = await payout(naira(5000), "payout-key-2");
    expect(small.json().status).toBe("submitted");

    ctx.config.payoutApprovalThresholdKobo = naira(10000);
    const big = await payout(naira(20000), "payout-key-3");
    expect(big.json().status).toBe("pending_approval");
    expect((await payout(naira(20000), "payout-key-4")).statusCode).toBe(400); // already held
    const rejected = await app.inject({ method: "POST", url: `/v1/admin/payouts/${big.json().id}`, headers: auth(admin.token), payload: { decision: "reject" } });
    expect(rejected.json().status).toBe("rejected");
    expect(ctx.store.balance(artist.user.id)).toBe(naira(27600) - naira(5000));
    expect(partners.payoutsSent).toHaveLength(1);
  });
});

describe("feeds and transparency", () => {
  it("serves ranked feeds with explanations and published rules", async () => {
    const artist = await signup("tobi@example.com", "artist", "tobi");
    const releaseId = await publishRelease(artist.token);
    const fan = await signup("ada@example.com");
    ctx.store.users.get(fan.user.id)!.createdAt = new Date("2026-01-01");
    await pay(fan.token, { kind: "tip", artistId: artist.user.artistId, releaseId, amountKobo: naira(2000) });
    await runRankingJob(ctx);

    const feed = (await app.inject({ method: "GET", url: "/v1/feeds/most-supported" })).json();
    expect(feed.items[0].release.id).toBe(releaseId);
    expect(feed.items[0].why.length).toBeGreaterThan(0);

    const why = (await app.inject({ method: "GET", url: `/v1/feeds/rising/items/${releaseId}/why` })).json();
    expect(why.topFactors.length).toBeLessThanOrEqual(3);

    const following = await app.inject({ method: "GET", url: "/v1/feeds/following", headers: auth(fan.token) });
    expect(following.json().items.map((i: { release: { id: string } }) => i.release.id)).toEqual([releaseId]);
    expect((await app.inject({ method: "GET", url: "/v1/feeds/following" })).statusCode).toBe(401);

    const rules = (await app.inject({ method: "GET", url: "/v1/ranking/rules" })).json();
    expect(rules.feeds).toHaveLength(4);
    expect(findForbiddenTerms(JSON.stringify(rules.feeds))).toEqual([]);
  });

  it("schedules weight changes through the changelog", async () => {
    const admin = await signup("admin@livebic.test");
    const tooSoon = await app.inject({
      method: "POST",
      url: "/v1/admin/ranking/changes",
      headers: auth(admin.token),
      payload: { summary: "More weight on unique supporters", reason: "Reward breadth", effectiveFrom: clock.toISOString(), weights: { paid: { perUniqueSupporter: 4 } } },
    });
    expect(tooSoon.statusCode).toBe(400);
    const ok = await app.inject({
      method: "POST",
      url: "/v1/admin/ranking/changes",
      headers: auth(admin.token),
      payload: { summary: "More weight on unique supporters", reason: "Reward breadth", effectiveFrom: new Date(clock.getTime() + 2 * 86_400_000).toISOString(), weights: { paid: { perUniqueSupporter: 4 } } },
    });
    expect(ok.statusCode).toBe(201);
    const rules = (await app.inject({ method: "GET", url: "/v1/ranking/rules" })).json();
    expect(rules.version).toBe(1);
    expect(rules.changelog[0].version).toBe(2);
    clock = new Date(clock.getTime() + 3 * 86_400_000);
    expect((await app.inject({ method: "GET", url: "/v1/ranking/rules" })).json().weights.paid.perUniqueSupporter).toBe(4);
  });
});
