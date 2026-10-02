import { createHmac, timingSafeEqual } from "node:crypto";
import { sha256Hex, type SongAnalysis, type SongSection } from "@livebic/core";
import { newId } from "../ids";
import type { Partners } from "./types";

export function signSandboxWebhook(rawBody: string, secret: string): string {
  return createHmac("sha256", secret).update(rawBody).digest("hex");
}

export interface SandboxPartners extends Partners {
  sentMail: { to: string; subject: string; text: string }[];
  settlements: { orderId: string; amountKobo: number }[];
  payoutsSent: { payoutId: string; amountKobo: number; method: string }[];
  registry: { kind: "content" | "receipt" | "consent"; id: string; hash: string; txRef: string }[];
  clipsGenerated: { quality: string; durationSeconds: number; prompt: string; seed: number }[];
  identitiesRevoked: string[];
}

/** A tiny valid-looking MP4-ish payload; enough for storage, hashing and download tests. */
function fakeVideo(tag: string): Buffer {
  return Buffer.concat([Buffer.from("\x00\x00\x00\x18ftypmp42", "binary"), Buffer.from(tag)]);
}

const SECTION_PLAN: [SongSection["kind"], number][] = [
  ["intro", 8], ["verse", 32], ["chorus", 32], ["verse", 32], ["chorus", 32], ["bridge", 16], ["chorus", 24], ["outro", 4],
];

/** Deterministic stand-in for tempo/structure analysis: derived from the file hash, stable per upload. */
export function fakeAnalysis(body: Buffer): SongAnalysis {
  const h = parseInt(sha256Hex(body).slice(0, 8), 16);
  const bpm = 92 + (h % 44);
  const durationSeconds = 150 + (h % 60);
  const scale = durationSeconds / 180;
  let t = 0;
  const sections: SongSection[] = SECTION_PLAN.map(([kind, len]) => {
    const s = { kind, startSeconds: Math.round(t * scale), endSeconds: Math.round((t + len) * scale) };
    t += len;
    return s;
  });
  sections[sections.length - 1]!.endSeconds = durationSeconds;
  return { durationSeconds, bpm, energy: bpm > 120 ? "high" : bpm > 105 ? "medium" : "low", sections };
}

/** In-memory partner stand-ins for local development and tests. No real money moves. */
export function sandboxPartners(opts: { publicApiUrl: string; webhookSecret: string }): SandboxPartners {
  const objects = new Map<string, { body: Buffer; contentType: string }>();
  const p: SandboxPartners = {
    sentMail: [],
    settlements: [],
    payoutsSent: [],
    registry: [],
    clipsGenerated: [],
    identitiesRevoked: [],
    video: {
      async generateClip(req) {
        p.clipsGenerated.push({ quality: req.quality, durationSeconds: req.durationSeconds, prompt: req.prompt, seed: req.seed });
        const tag = `clip:${req.quality}:${req.seed}:${req.durationSeconds}`;
        return {
          jobId: newId("job"),
          status: "done",
          output: { body: fakeVideo(tag), contentType: "video/mp4", thumbnail: { body: Buffer.from(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 160"><rect width="90" height="160" fill="#${sha256Hex(tag).slice(0, 6)}"/></svg>`), contentType: "image/svg+xml" } },
        };
      },
      async getJob(jobId) {
        return { jobId, status: "done" };
      },
      async revokeIdentity(artistId) {
        p.identitiesRevoked.push(artistId);
      },
    },
    audioAnalyzer: {
      async analyze(audio) {
        return fakeAnalysis(audio.body);
      },
    },
    renderer: {
      async assemble(input) {
        return { body: fakeVideo(`render:${input.aspect}:${input.clips.length}:${input.label}`), contentType: "video/mp4" };
      },
    },
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
      async recordConsentHash({ consentId, hash }) {
        const txRef = `0x${sha256Hex(`consent:${consentId}:${hash}`)}`;
        p.registry.push({ kind: "consent", id: consentId, hash, txRef });
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
