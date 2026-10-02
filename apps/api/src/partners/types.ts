import type { Aspect, Currency, Quality, SongAnalysis, SplitAllocation } from "@livebic/core";

/**
 * Licensed partners. Livebic never holds funds or keys itself (spec: Payments, wallets and
 * ownership model). Each interface is implemented by a sandbox mock today and by the chosen
 * partner's SDK once the open partner/chain decisions are made.
 */

export interface CheckoutRequest {
  orderId: string;
  amountMinor: number;
  currency: Currency;
  email: string;
  description: string;
  returnUrl: string;
}

export interface CheckoutSession {
  reference: string;
  checkoutUrl: string;
}

/** Licensed Nigerian processor: card, bank transfer, USSD, mobile wallets; USD/GBP cards for diaspora. */
export interface PaymentProcessor {
  createCheckout(req: CheckoutRequest): Promise<CheckoutSession>;
  /** Verify a webhook body against its signature header. */
  verifyWebhook(rawBody: string, signature: string | undefined): boolean;
}

export interface SettlementResult {
  settlementRef: string;
}

/** Payout partner with stablecoin rails: holds balances, runs split contracts, converts, does KYC. */
export interface PayoutPartner {
  settleSale(input: { orderId: string; amountKobo: number; allocations: SplitAllocation[] }): Promise<SettlementResult>;
  kycStatus(userId: string): Promise<"approved" | "pending" | "required">;
  sendPayout(input: {
    payoutId: string;
    userId: string;
    amountKobo: number;
    method: "bank" | "stablecoin";
    destination: string;
  }): Promise<{ partnerRef: string }>;
}

/** Embedded-wallet SDK: passkey-secured wallet created silently at sign-up. */
export interface WalletProvider {
  createWallet(userId: string): Promise<{ walletId: string; address: string }>;
}

/** Content-hash and receipt registry on the settlement chain; fees are sponsored. */
export interface ChainRegistry {
  recordContentHash(input: { releaseId: string; sha256: string }): Promise<{ txRef: string }>;
  recordReceiptHash(input: { receiptId: string; hash: string }): Promise<{ txRef: string }>;
  /** Consent and licence record for an artist's likeness (spec: digital twins, creation flow step 2). */
  recordConsentHash(input: { consentId: string; hash: string }): Promise<{ txRef: string }>;
}

export interface ObjectStorage {
  put(key: string, body: Buffer, contentType: string): Promise<void>;
  get(key: string): Promise<{ body: Buffer; contentType: string } | null>;
}

export interface Mailer {
  send(input: { to: string; subject: string; text: string }): Promise<void>;
}

/** Naira per unit of foreign currency, used to price diaspora card payments. */
export interface FxRates {
  ngnPer(currency: Exclude<Currency, "NGN">): Promise<number>;
}

export interface ClipRequest {
  /** Plain-language direction for one shot. */
  prompt: string;
  durationSeconds: number;
  aspect: Aspect;
  quality: Quality;
  /** Reference photos of the artist (likeness mode) or a character sheet. */
  referenceImages: { body: Buffer; contentType: string }[];
  /** The slice of the song this shot covers, for lip-sync and beat matching. */
  audio: { body: Buffer; contentType: string; offsetSeconds: number } | null;
  /** Same seed across draft and final keeps the same take. */
  seed: number;
}

export interface ClipJob {
  jobId: string;
  status: "queued" | "running" | "done" | "failed";
  /** Set when done. */
  output?: { body: Buffer; contentType: string; thumbnail: { body: Buffer; contentType: string } | null };
  error?: string;
}

/**
 * Video generation partner (Higgsfield; spec: Phase 3 digital twins). Drafts map to the model's
 * cheap low-resolution mode, finals to 1080p, and the same seed re-renders the chosen take.
 */
export interface VideoGenerator {
  generateClip(req: ClipRequest): Promise<ClipJob>;
  getJob(jobId: string): Promise<ClipJob>;
  /** Delete a trained identity/reference set when the artist revokes consent. */
  revokeIdentity(artistId: string): Promise<void>;
}

/** Tempo, structure and energy of an uploaded track, for cutting on the beat. */
export interface AudioAnalyzer {
  analyze(audio: { body: Buffer; contentType: string }): Promise<SongAnalysis>;
}

/** Joins finished clips over the song slice, burns in the watermark and label, exports per aspect. */
export interface VideoRenderer {
  assemble(input: {
    clips: { body: Buffer; contentType: string; startSeconds: number; durationSeconds: number }[];
    audio: { body: Buffer; contentType: string; offsetSeconds: number; durationSeconds: number };
    aspect: Aspect;
    watermarkText: string;
    label: string;
  }): Promise<{ body: Buffer; contentType: string }>;
}

export interface Partners {
  video: VideoGenerator;
  audioAnalyzer: AudioAnalyzer;
  renderer: VideoRenderer;
  processor: PaymentProcessor;
  payouts: PayoutPartner;
  wallets: WalletProvider;
  chain: ChainRegistry;
  storage: ObjectStorage;
  mailer: Mailer;
  fx: FxRates;
}
