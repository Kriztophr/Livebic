import type { FastifyInstance } from "fastify";
import {
  TIP_AMOUNTS_KOBO,
  allocateSale,
  formatNaira,
  signReceipt,
  tierFor,
  verifyReceipt,
  type Currency,
  type SplitShare,
} from "@livebic/core";
import { z } from "zod";
import { requireUser, withIdempotency, type AppContext } from "../context";
import { badRequest, conflict, forbidden, HttpError, notFound } from "../errors";
import { newId } from "../ids";
import { signSandboxWebhook } from "../partners/sandbox";
import type { Order, Payout } from "../store";

const DAY_MS = 86_400_000;
const MIN_PAYOUT_KOBO = 100_000;

const orderSchema = z.object({
  kind: z.enum(["tip", "unlock", "membership", "drop", "fund"]),
  artistId: z.string(),
  releaseId: z.string().nullable().default(null),
  videoProjectId: z.string().optional(),
  amountKobo: z.number().int().optional(),
  currency: z.enum(["NGN", "USD", "GBP"]).default("NGN"),
});

const webhookSchema = z.object({ reference: z.string(), status: z.enum(["success", "failed"]) });

export function publicOrder(o: Order) {
  return {
    id: o.id,
    kind: o.kind,
    artistId: o.artistId,
    releaseId: o.releaseId,
    amountKobo: o.amountKobo,
    charge: { amountMinor: o.chargeMinor, currency: o.currency },
    status: o.status,
    editionNumber: o.editionNumber,
    membershipEndsAt: o.membershipEndsAt,
    videoProjectId: o.videoProjectId ?? null,
    createdAt: o.createdAt,
    paidAt: o.paidAt,
    receipt: o.receipt ? { hash: o.receipt.hash, signature: o.receipt.signature, registryRef: o.receipt.registryTx, ...o.receipt.receipt } : null,
  };
}

function splitsFor(ctx: AppContext, order: Order): SplitShare[] {
  const release = order.releaseId ? ctx.store.releases.get(order.releaseId) : undefined;
  if (release) return release.splits;
  const artist = ctx.store.artists.get(order.artistId)!;
  return [{ payeeId: artist.ownerUserId, role: "creator", bps: 10_000 }];
}

/** Applies a confirmed (or failed) payment from the processor. Safe to call more than once. */
export async function applyPaymentResult(ctx: AppContext, reference: string, status: "success" | "failed"): Promise<Order> {
  const { store } = ctx;
  const order = [...store.orders.values()].find((o) => o.checkoutRef === reference);
  if (!order) throw notFound("Order");
  if (order.status !== "pending") return order;
  if (status === "failed") {
    order.status = "failed";
    return order;
  }

  const now = ctx.now();
  const release = order.releaseId ? store.releases.get(order.releaseId) : undefined;
  if (order.kind === "drop") {
    if (!release?.edition || release.edition.sold >= release.edition.size) {
      // Sold out between checkout and payment: hand to the refund flow, no settlement.
      order.status = "refund_required";
      return order;
    }
    release.edition.sold += 1;
    order.editionNumber = release.edition.sold;
  }

  // Mark paid before any await so a concurrent webhook replay cannot double-settle.
  order.status = "paid";
  order.paidAt = now;
  if (order.kind === "membership") order.membershipEndsAt = new Date(now.getTime() + ctx.config.membershipDays * DAY_MS);

  const allocations = allocateSale(order.amountKobo, splitsFor(ctx, order), ctx.config.platformFeeBps);
  const { settlementRef } = await ctx.partners.payouts.settleSale({ orderId: order.id, amountKobo: order.amountKobo, allocations });
  order.settlementRef = settlementRef;
  for (const a of allocations) {
    if (a.amount === 0) continue;
    store.ledger.push({
      id: newId("led"),
      userId: a.payeeId,
      amountKobo: a.amount,
      reason: a.role === "platform" ? "platform_fee" : "sale",
      orderId: order.id,
      payoutId: null,
      at: now,
    });
  }

  const signed = signReceipt(
    {
      receiptId: `rcp_${order.id}`,
      fanId: order.fanId,
      artistId: order.artistId,
      releaseId: order.releaseId,
      kind: order.kind,
      tier: tierFor(order.kind),
      amountKobo: order.amountKobo,
      editionNumber: order.editionNumber,
      issuedAt: now.toISOString(),
    },
    ctx.receiptKeys.privateKeyPem,
  );
  const { txRef } = await ctx.partners.chain.recordReceiptHash({ receiptId: signed.receipt.receiptId, hash: signed.hash });
  order.receipt = { ...signed, registryTx: txRef };

  store.events.push({
    id: newId("evt"),
    type: order.kind,
    actorId: order.fanId,
    artistId: order.artistId,
    itemId: order.releaseId,
    at: now,
    amountKobo: order.amountKobo,
  });

  const fan = store.users.get(order.fanId);
  const artist = store.artists.get(order.artistId);
  if (fan && artist) {
    const what =
      order.kind === "drop"
        ? `You own #${order.editionNumber} of "${release?.title}"`
        : order.kind === "membership"
          ? `You're a member of ${artist.displayName} until ${order.membershipEndsAt?.toDateString()}`
          : order.kind === "unlock"
            ? `You unlocked "${release?.title}"`
            : order.kind === "fund"
              ? `You backed the video for "${store.videoProjects.get(order.videoProjectId ?? "")?.id ? release?.title ?? artist.displayName : artist.displayName}". You'll be credited when it's out`
              : `You tipped ${artist.displayName}`;
    await ctx.partners.mailer.send({
      to: fan.email,
      subject: `Receipt: ${formatNaira(order.amountKobo)} to ${artist.displayName}`,
      text: `${what}.\nAmount: ${formatNaira(order.amountKobo)}\nReceipt: ${order.receipt.receipt.receiptId}\nThank you for supporting artists directly on Livebic.`,
    });
  }
  return order;
}

export async function registerPayments(app: FastifyInstance, ctx: AppContext) {
  const { store, config } = ctx;

  app.post("/v1/orders", async (req, reply) => {
    const user = requireUser(ctx, req);
    const res = await withIdempotency(ctx, req, "orders", user.id, async () => {
      const body = orderSchema.parse(req.body);
      const artist = store.artists.get(body.artistId);
      if (!artist) throw notFound("Artist");
      if (artist.ownerUserId === user.id) throw forbidden("You can't support your own page");
      const release = body.releaseId ? store.releases.get(body.releaseId) : undefined;
      if (body.releaseId && (!release || release.artistId !== artist.id || release.status !== "published")) throw notFound("Release");

      let amountKobo: number;
      let videoProjectId: string | undefined;
      switch (body.kind) {
        case "fund": {
          const project = body.videoProjectId ? store.videoProjects.get(body.videoProjectId) : undefined;
          if (!project || project.artistId !== artist.id || !project.funding || project.funding.closedAt !== null) {
            throw badRequest("no_campaign", "This video isn't raising funds right now");
          }
          if (body.amountKobo === undefined || !TIP_AMOUNTS_KOBO.includes(body.amountKobo)) {
            throw badRequest("invalid_amount", `Back a video with ${TIP_AMOUNTS_KOBO.map(formatNaira).join(", ")}`);
          }
          amountKobo = body.amountKobo;
          videoProjectId = project.id;
          break;
        }
        case "tip":
          if (body.amountKobo === undefined || !TIP_AMOUNTS_KOBO.includes(body.amountKobo)) {
            throw badRequest("invalid_tip", `Tips are ${TIP_AMOUNTS_KOBO.map(formatNaira).join(", ")}`);
          }
          amountKobo = body.amountKobo;
          break;
        case "unlock":
          if (!release || release.access !== "paid" || release.priceKobo === null) throw badRequest("not_unlockable", "This release isn't for sale");
          if (store.paidOrders((o) => o.fanId === user.id && o.releaseId === release.id && o.kind === "unlock").length) {
            throw conflict("already_unlocked", "You already unlocked this");
          }
          amountKobo = release.priceKobo;
          break;
        case "membership":
          if (artist.membershipPriceKobo === null) throw badRequest("no_membership", "This artist doesn't offer memberships yet");
          amountKobo = artist.membershipPriceKobo;
          break;
        case "drop":
          if (!release?.edition) throw badRequest("no_drop", "This release has no limited edition");
          if (release.edition.sold >= release.edition.size) throw conflict("sold_out", "Sold out");
          amountKobo = release.edition.priceKobo;
          break;
      }

      const currency: Currency = body.currency;
      const chargeMinor = currency === "NGN" ? amountKobo : Math.ceil(amountKobo / (await ctx.partners.fx.ngnPer(currency)));
      const orderId = newId("ord");
      const checkout = await ctx.partners.processor.createCheckout({
        orderId,
        amountMinor: chargeMinor,
        currency,
        email: user.email,
        description: `${body.kind} for ${artist.displayName}`,
        returnUrl: `${config.webUrl}/orders/${orderId}`,
      });
      const order: Order = {
        id: orderId,
        fanId: user.id,
        artistId: artist.id,
        releaseId: release?.id ?? null,
        kind: body.kind,
        amountKobo,
        chargeMinor,
        currency,
        status: "pending",
        checkoutRef: checkout.reference,
        createdAt: ctx.now(),
        paidAt: null,
        settlementRef: null,
        editionNumber: null,
        receipt: null,
        membershipEndsAt: null,
        ...(videoProjectId ? { videoProjectId } : {}),
      };
      store.orders.set(order.id, order);
      return { statusCode: 201, body: { order: publicOrder(order), checkoutUrl: checkout.checkoutUrl } };
    });
    return reply.code(res.statusCode).send(res.body);
  });

  app.get<{ Params: { id: string } }>("/v1/orders/:id", async (req) => {
    const user = requireUser(ctx, req);
    const order = store.orders.get(req.params.id);
    if (!order || (order.fanId !== user.id && user.role !== "admin")) throw notFound("Order");
    return publicOrder(order);
  });

  app.get("/v1/me/orders", async (req) => {
    const user = requireUser(ctx, req);
    return [...store.orders.values()].filter((o) => o.fanId === user.id).map(publicOrder);
  });

  app.get<{ Params: { id: string } }>("/v1/receipts/:id/verify", async (req) => {
    const order = [...store.orders.values()].find((o) => o.receipt?.receipt.receiptId === req.params.id);
    if (!order?.receipt) throw notFound("Receipt");
    return {
      valid: verifyReceipt(order.receipt, ctx.receiptKeys.publicKeyPem),
      receipt: order.receipt.receipt,
      hash: order.receipt.hash,
      registryRef: order.receipt.registryTx,
    };
  });

  // Processor webhooks are verified against the raw body.
  app.register(async (scope) => {
    scope.addContentTypeParser("application/json", { parseAs: "string" }, (_req, body, done) => done(null, body));
    scope.post("/v1/webhooks/processor", async (req, reply) => {
      const raw = req.body as string;
      const sig = req.headers["x-processor-signature"];
      if (!ctx.partners.processor.verifyWebhook(raw, typeof sig === "string" ? sig : undefined)) {
        throw new HttpError(401, "bad_signature", "Invalid webhook signature");
      }
      const event = webhookSchema.parse(JSON.parse(raw));
      const order = await applyPaymentResult(ctx, event.reference, event.status);
      return reply.send({ received: true, orderStatus: order.status });
    });
  });

  app.get("/v1/me/ledger", async (req) => {
    const user = requireUser(ctx, req);
    return {
      balanceKobo: store.balance(user.id),
      entries: store.ledger.filter((e) => e.userId === user.id).reverse(),
      payouts: [...store.payouts.values()].filter((p) => p.userId === user.id).reverse(),
    };
  });

  app.post("/v1/payouts", async (req, reply) => {
    const user = requireUser(ctx, req);
    const res = await withIdempotency(ctx, req, "payouts", user.id, async () => {
      const body = z
        .object({
          amountKobo: z.number().int().min(MIN_PAYOUT_KOBO),
          method: z.enum(["bank", "stablecoin"]).optional(),
          destination: z.string().min(4).max(200).optional(),
        })
        .parse(req.body);
      const artist = user.artistId ? store.artists.get(user.artistId) : undefined;
      if (artist && artist.verification !== "verified") throw forbidden("Verify your artist profile before your first payout");
      const method = body.method ?? artist?.payout?.method;
      const destination = body.destination ?? artist?.payout?.destination;
      if (!method || !destination) throw badRequest("payout_destination_required", "Add a bank account or payout address first");
      if ((await ctx.partners.payouts.kycStatus(user.id)) !== "approved") {
        throw new HttpError(403, "kyc_required", "Complete identity checks with our payout partner first");
      }
      if (store.balance(user.id) < body.amountKobo) throw badRequest("insufficient_balance", "That's more than your available balance");

      const payout: Payout = {
        id: newId("pay"),
        userId: user.id,
        amountKobo: body.amountKobo,
        method,
        destination,
        status: body.amountKobo > config.payoutApprovalThresholdKobo ? "pending_approval" : "submitted",
        partnerRef: null,
        createdAt: ctx.now(),
        decidedBy: null,
      };
      // Debit now so the same balance can't be requested twice while awaiting approval.
      store.payouts.set(payout.id, payout);
      store.ledger.push({ id: newId("led"), userId: user.id, amountKobo: -payout.amountKobo, reason: "payout", orderId: null, payoutId: payout.id, at: payout.createdAt });
      if (payout.status === "submitted") await submitPayout(ctx, payout);
      return { statusCode: 201, body: payout };
    });
    return reply.code(res.statusCode).send(res.body);
  });

  if (config.sandbox) registerSandboxCheckout(app, ctx);
}

export async function submitPayout(ctx: AppContext, payout: Payout): Promise<void> {
  const { partnerRef } = await ctx.partners.payouts.sendPayout({
    payoutId: payout.id,
    userId: payout.userId,
    amountKobo: payout.amountKobo,
    method: payout.method,
    destination: payout.destination,
  });
  payout.status = "submitted";
  payout.partnerRef = partnerRef;
}

/** Stand-in for the processor's hosted checkout page. Only mounted in sandbox mode. */
function registerSandboxCheckout(app: FastifyInstance, ctx: AppContext) {
  const findOrder = (ref: string) => [...ctx.store.orders.values()].find((o) => o.checkoutRef === ref);

  app.get<{ Params: { ref: string } }>("/sandbox/checkout/:ref", async (req, reply) => {
    const order = findOrder(req.params.ref);
    if (!order) throw notFound("Checkout");
    const amount = order.currency === "NGN" ? formatNaira(order.amountKobo) : `${order.currency} ${(order.chargeMinor / 100).toFixed(2)}`;
    return reply.type("text/html").send(`<!doctype html><meta name="viewport" content="width=device-width">
<title>Sandbox checkout</title>
<body style="font-family:system-ui;max-width:420px;margin:40px auto;padding:0 16px">
<p style="color:#b45309">Sandbox payment — no money moves.</p>
<h1>Pay ${amount}</h1><p>${order.kind} · order ${order.id}</p>
<form method="post"><button name="outcome" value="success" style="padding:12px 20px">Pay</button>
<button name="outcome" value="failed" style="padding:12px 20px">Decline</button></form></body>`);
  });

  app.register(async (scope) => {
    scope.addContentTypeParser("application/x-www-form-urlencoded", { parseAs: "string" }, (_req, body, done) =>
      done(null, Object.fromEntries(new URLSearchParams(body as string))),
    );
    scope.post<{ Params: { ref: string }; Body: { outcome?: string } }>("/sandbox/checkout/:ref", async (req, reply) => {
      const order = findOrder(req.params.ref);
      if (!order) throw notFound("Checkout");
      const status = req.body?.outcome === "success" ? "success" : "failed";
      // Exercise the same signed-webhook path the real processor uses.
      const raw = JSON.stringify({ reference: req.params.ref, status });
      const res = await app.inject({
        method: "POST",
        url: "/v1/webhooks/processor",
        headers: { "content-type": "application/json", "x-processor-signature": signSandboxWebhook(raw, ctx.config.processorWebhookSecret) },
        payload: raw,
      });
      if (res.statusCode !== 200) throw new HttpError(502, "webhook_failed", res.body);
      return reply.redirect(`${ctx.config.webUrl}/orders/${order.id}`);
    });
  });
}
