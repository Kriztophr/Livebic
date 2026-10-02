import Fastify, { type FastifyInstance } from "fastify";
import cors from "@fastify/cors";
import { ZodError } from "zod";
import type { AppContext } from "./context";
import { HttpError } from "./errors";
import { registerAdmin } from "./modules/admin";
import { registerContent } from "./modules/content";
import { registerIdentity } from "./modules/identity";
import { registerPayments } from "./modules/payments";
import { registerRanking } from "./modules/ranking";
import { registerSupporters } from "./modules/supporters";
import { registerVideo } from "./modules/video";

/**
 * Modular monolith (spec: Core services): Identity, Content, Payments, Ranking and Export
 * modules behind one gateway. Each module only talks to the others through exported functions.
 */
export async function buildApp(ctx: AppContext, opts: { logger?: boolean } = {}): Promise<FastifyInstance> {
  const app = Fastify({ logger: opts.logger ?? false, trustProxy: true });
  await app.register(cors, { origin: ctx.config.webUrl, credentials: true });

  app.setErrorHandler((err, _req, reply) => {
    if (err instanceof HttpError) return reply.code(err.statusCode).send({ error: err.code, message: err.message });
    if (err instanceof ZodError) {
      return reply.code(400).send({ error: "invalid_request", message: "Check the highlighted fields", issues: err.issues });
    }
    const status = (err as { statusCode?: number }).statusCode;
    if (status && status < 500) return reply.code(status).send({ error: "invalid_request", message: (err as Error).message });
    app.log.error(err);
    return reply.code(500).send({ error: "internal", message: "Something went wrong" });
  });

  app.get("/health", async () => ({ ok: true, sandbox: ctx.config.sandbox }));

  await registerIdentity(app, ctx);
  await registerContent(app, ctx);
  await registerPayments(app, ctx);
  await registerRanking(app, ctx);
  await registerSupporters(app, ctx);
  await registerVideo(app, ctx);
  await registerAdmin(app, ctx);
  return app;
}
