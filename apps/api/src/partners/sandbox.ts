import { createHmac, timingSafeEqual } from "node:crypto";
import { sha256Hex } from "@livebic/core";
import { newId } from "../ids";
import type { Partners } from "./types";

export function signSandboxWebhook(rawBody: string, secret: string): string {
  return createHmac("sha256", secret).update(rawBody).digest("hex");
}

export interface SandboxPartners extends Partners {
  sentMail: { to: string; subject: string; text: string }[];
  settlements: { orderId: string; amountKobo: number }[];
  payoutsSent: { payoutId: string; amountKobo: number; method: string }[];
  registry: { kind: "content" | "receipt"; id: string; hash: string; txRef: string }[];
}

/** In-memory partner stand-ins for local development and tests. No real money moves. */
export function sandboxPartners(opts: { publicApiUrl: string; webhookSecret: string }): SandboxPartners {
  const objects = new Map<string, { body: Buffer; contentType: string }>();
  const p: SandboxPartners = {
    sentMail: [],
    settlements: [],
    payoutsSent: [],
    registry: [],
    processor: {
      async createCheckout(req) {
        const reference = newId("chk");
        return { reference, checkoutUrl: `${opts.publicApiUrl}/sandbox/checkout/${reference}` };
      },
      verifyWebhook(rawBody, signature) {
        if (!signature) return false;
        const expected = Buffer.from(signSandboxWebhook(rawBody, opts.webhookSecret));
        const given = Buffer.from(signature);
        return expected.length === given.length && timingSafeEqual(expected, given);
      },
    },
    payouts: {
      async settleSale(input) {
        p.settlements.push({ orderId: input.orderId, amountKobo: input.amountKobo });
        return { settlementRef: `stl_${sha256Hex(input.orderId).slice(0, 16)}` };
      },
      async kycStatus() {
        return "approved";
      },
      async sendPayout(input) {
        p.payoutsSent.push({ payoutId: input.payoutId, amountKobo: input.amountKobo, method: input.method });
        return { partnerRef: newId("po") };
      },
    },
    wallets: {
      async createWallet(userId) {
        return { walletId: newId("wal"), address: `0x${sha256Hex(userId).slice(0, 40)}` };
      },
    },
    chain: {
      async recordContentHash({ releaseId, sha256 }) {
        const txRef = `0x${sha256Hex(`content:${releaseId}:${sha256}`)}`;
        p.registry.push({ kind: "content", id: releaseId, hash: sha256, txRef });
        return { txRef };
      },
      async recordReceiptHash({ receiptId, hash }) {
        const txRef = `0x${sha256Hex(`receipt:${receiptId}:${hash}`)}`;
        p.registry.push({ kind: "receipt", id: receiptId, hash, txRef });
        return { txRef };
      },
    },
    storage: {
      async put(key, body, contentType) {
        objects.set(key, { body, contentType });
      },
      async get(key) {
        return objects.get(key) ?? null;
      },
    },
    mailer: {
      async send(mail) {
        p.sentMail.push(mail);
      },
    },
    fx: {
      async ngnPer(currency) {
        return currency === "USD" ? 1_550 : 2_050;
      },
    },
  };
  return p;
}
