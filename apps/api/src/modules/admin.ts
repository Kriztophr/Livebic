import type { FastifyInstance } from "fastify";
import { DEFAULT_WEIGHTS, type RankingWeights } from "@livebic/core";
import { z } from "zod";
import { requireAdmin, type AppContext } from "../context";
import { badRequest, conflict, notFound } from "../errors";
import { newId } from "../ids";
import { runRankingJob } from "../jobs/ranking";
import { submitPayout } from "./payments";

export async function registerAdmin(app: FastifyInstance, ctx: AppContext) {
  const { store } = ctx;

  app.get("/v1/admin/verifications", async (req) => {
    requireAdmin(ctx, req);
    return [...store.artists.values()].filter((a) => a.verification === "pending");
  });

  app.post<{ Params: { artistId: string } }>("/v1/admin/verifications/:artistId", async (req) => {
    requireAdmin(ctx, req);
    const { decision } = z.object({ decision: z.enum(["verified", "rejected"]) }).parse(req.body);
    const artist = store.artists.get(req.params.artistId);
    if (!artist) throw notFound("Artist");
    if (artist.verification !== "pending") throw conflict("not_pending", "No pending verification");
    artist.verification = decision;
    return { id: artist.id, verification: artist.verification };
  });

  app.get("/v1/admin/payouts", async (req) => {
    requireAdmin(ctx, req);
    return [...store.payouts.values()].filter((p) => p.status === "pending_approval");
  });

  app.post<{ Params: { id: string } }>("/v1/admin/payouts/:id", async (req) => {
    const admin = requireAdmin(ctx, req);
    const { decision } = z.object({ decision: z.enum(["approve", "reject"]) }).parse(req.body);
    const payout = store.payouts.get(req.params.id);
    if (!payout) throw notFound("Payout");
    if (payout.status !== "pending_approval") throw conflict("not_pending", "Payout already decided");
    payout.decidedBy = admin.id;
    if (decision === "approve") {
      await submitPayout(ctx, payout);
    } else {
      payout.status = "rejected";
      store.ledger.push({ id: newId("led"), userId: payout.userId, amountKobo: payout.amountKobo, reason: "payout_reversal", orderId: null, payoutId: payout.id, at: ctx.now() });
    }
    return payout;
  });

  app.get("/v1/admin/moderation", async (req) => {
    requireAdmin(ctx, req);
    return [...store.reports.values()].filter((r) => r.status === "open");
  });

  app.post<{ Params: { id: string } }>("/v1/admin/moderation/:id", async (req) => {
    requireAdmin(ctx, req);
    const { decision } = z.object({ decision: z.enum(["takedown", "dismiss"]) }).parse(req.body);
    const report = store.reports.get(req.params.id);
    if (!report) throw notFound("Report");
    if (decision === "takedown") {
      const release = store.releases.get(report.releaseId);
      if (release) release.status = "removed";
      report.status = "actioned";
    } else {
      report.status = "dismissed";
    }
    return report;
  });

  app.get("/v1/admin/fraud-flags", async (req) => {
    requireAdmin(ctx, req);
    return store.flags.filter((f) => f.status === "open");
  });

  app.post<{ Params: { id: string } }>("/v1/admin/fraud-flags/:id/clear", async (req) => {
    requireAdmin(ctx, req);
    const flag = store.flags.find((f) => f.id === req.params.id);
    if (!flag) throw notFound("Flag");
    flag.status = "cleared";
    return flag;
  });

  app.post("/v1/admin/ranking/changes", async (req, reply) => {
    const admin = requireAdmin(ctx, req);
    const body = z
      .object({
        summary: z.string().min(3).max(500),
        reason: z.string().min(3).max(2000),
        effectiveFrom: z.string().datetime(),
        weights: z.record(z.unknown()),
      })
      .parse(req.body);
    const now = ctx.now();
    const current = ctx.rules.changelog()[0]?.weights ?? DEFAULT_WEIGHTS;
    const weights = deepMerge(current, body.weights) as RankingWeights;
    try {
      const version = ctx.rules.propose({
        weights,
        summary: body.summary,
        effectiveFrom: new Date(body.effectiveFrom),
        change: { id: newId("chg"), author: admin.id, reason: body.reason, createdAt: now.toISOString() },
        now,
      });
      return reply.code(201).send(version);
    } catch (e) {
      throw badRequest("invalid_ranking_change", (e as Error).message);
    }
  });

  app.post("/v1/admin/ranking/recompute", async (req) => {
    requireAdmin(ctx, req);
    return runRankingJob(ctx);
  });
}

function deepMerge(base: unknown, patch: unknown): unknown {
  if (typeof base !== "object" || base === null || typeof patch !== "object" || patch === null) return patch ?? base;
  const out: Record<string, unknown> = { ...(base as Record<string, unknown>) };
  for (const [k, v] of Object.entries(patch as Record<string, unknown>)) {
    if (!(k in out)) throw badRequest("unknown_weight", `Unknown ranking weight "${k}"`);
    out[k] = deepMerge(out[k], v);
  }
  return out;
}
