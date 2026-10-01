import type { Currency, SplitAllocation } from "@livebic/core";

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

export interface Partners {
  processor: PaymentProcessor;
  payouts: PayoutPartner;
  wallets: WalletProvider;
  chain: ChainRegistry;
  storage: ObjectStorage;
  mailer: Mailer;
  fx: FxRates;
}
