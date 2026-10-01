import { loadConfig } from "./config";
import { createContext } from "./context";
import { buildApp } from "./app";
import { runRankingJob } from "./jobs/ranking";
import { sandboxPartners } from "./partners/sandbox";
import { seed } from "./seed";
import { MemoryStore } from "./store";

const config = loadConfig();
if (!config.sandbox) {
  throw new Error("Only sandbox mode is implemented. Wire real partners in src/partners before setting LIVEBIC_SANDBOX=false.");
}

const ctx = createContext({
  config,
  store: new MemoryStore(),
  partners: sandboxPartners({ publicApiUrl: config.publicApiUrl, webhookSecret: config.processorWebhookSecret }),
});
if (process.env.SEED !== "false") await seed(ctx);
await runRankingJob(ctx);
setInterval(() => void runRankingJob(ctx).catch((e) => console.error("ranking job failed", e)), config.rankingIntervalMs).unref();

const app = await buildApp(ctx, { logger: true });
await app.listen({ port: config.port, host: "0.0.0.0" });
