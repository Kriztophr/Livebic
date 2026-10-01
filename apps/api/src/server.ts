import { loadConfig } from "./config";
import { createContext } from "./context";
import { buildApp } from "./app";
import { runRankingJob } from "./jobs/ranking";
import { sandboxPartners } from "./partners/sandbox";
import { seed } from "./seed";
import { loadBundle, readBundle } from "./migrate/bundle";
import { join } from "node:path";
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
const bundleDir = process.env.LIVEBIC_IMPORT_BUNDLE;
if (bundleDir) {
  const loaded = await loadBundle(ctx, await readBundle(bundleDir), join(bundleDir, "media"));
  console.log("Loaded legacy import:", loaded);
} else if (process.env.SEED !== "false") {
  await seed(ctx);
}
await runRankingJob(ctx);
setInterval(() => void runRankingJob(ctx).catch((e) => console.error("ranking job failed", e)), config.rankingIntervalMs).unref();

const app = await buildApp(ctx, { logger: true });
await app.listen({ port: config.port, host: "0.0.0.0" });
