import { naira } from "@livebic/core";

export interface Config {
  port: number;
  publicApiUrl: string;
  webUrl: string;
  /** Sandbox mode wires mock partners and enables the simulated checkout page. */
  sandbox: boolean;
  platformFeeBps: number;
  payoutApprovalThresholdKobo: number;
  membershipDays: number;
  processorWebhookSecret: string;
  signedUrlSecret: string;
  receiptPrivateKeyPem?: string;
  adminEmails: string[];
  rankingIntervalMs: number;
}

export function loadConfig(env: NodeJS.ProcessEnv = process.env): Config {
  const port = Number(env.PORT ?? 4000);
  return {
    port,
    publicApiUrl: env.PUBLIC_API_URL ?? `http://localhost:${port}`,
    webUrl: env.WEB_URL ?? "http://localhost:3000",
    sandbox: (env.LIVEBIC_SANDBOX ?? "true") === "true",
    platformFeeBps: Number(env.PLATFORM_FEE_BPS ?? 800),
    payoutApprovalThresholdKobo: Number(env.PAYOUT_APPROVAL_THRESHOLD_KOBO ?? naira(500_000)),
    membershipDays: 30,
    processorWebhookSecret: env.PROCESSOR_WEBHOOK_SECRET ?? "sandbox-webhook-secret",
    signedUrlSecret: env.SIGNED_URL_SECRET ?? "sandbox-signed-url-secret",
    receiptPrivateKeyPem: env.RECEIPT_PRIVATE_KEY_PEM,
    adminEmails: (env.ADMIN_EMAILS ?? "admin@livebic.test").split(",").map((s) => s.trim().toLowerCase()).filter(Boolean),
    rankingIntervalMs: 15 * 60 * 1000,
  };
}
