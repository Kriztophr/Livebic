import type { FastifyInstance } from "fastify";
import { createHash } from "node:crypto";
import { FEED_NAMES, describeFeeds, explain, followingFeed, type FeedName, type FeedSnapshot } from "@livebic/core";
import { z } from "zod";
import { currentUser, requireUser, type AppContext } from "../context";
import { notFound } from "../errors";
import { newId } from "../ids";
import { runRankingJob } from "../jobs/ranking";
import { releaseView } from "./content";

export async function registerRanking(app: FastifyInstance, ctx: AppContext) {
  const { store } = ctx;

  async function snapshotFor(feed: FeedName, req: Parameters<typeof currentUser>[1]): Promise<FeedSnapshot> {
    const base = feed === "following" ? "newest" : feed;
    let snap = await ctx.cache.get(base);
    if (!snap) {
      await runRankingJob(ctx);
      snap = (await ctx.cache.get(base))!;
    }
    if (feed !== "following") return snap;
    const user = requireUser(ctx, req);
    return followingFeed(snap, store.followedArtistIds(user.id));
  }

  app.get<{ Params: { feed: string }; Querystring: { limit?: string; offset?: string } }>("/v1/feeds/:feed", async (req) => {
    const feed = z.enum(FEED_NAMES as [FeedName, ...FeedName[]]).parse(req.params.feed);
    const limit = Math.min(50, Math.max(1, Number(req.query.limit ?? 20) || 20));
    const offset = Math.max(0, Number(req.query.offset ?? 0) || 0);
    const snap = await snapshotFor(feed, req);
    const viewer = currentUser(ctx, req);
    const items = snap.entries
      .slice(offset, offset + limit)
      .map((e, i) => {
        const release = store.releases.get(e.itemId);
        if (!release || release.status !== "published") return null;
        return { rank: offset + i + 1, release: releaseView(ctx, release, viewer), why: explain(e) };
      })
      .filter((x) => x !== null);
    return { feed, rulesVersion: snap.rulesVersion, computedAt: snap.computedAt, total: snap.entries.length, items };
  });

  app.get<{ Params: { feed: string; itemId: string } }>("/v1/feeds/:feed/items/:itemId/why", async (req) => {
    const feed = z.enum(FEED_NAMES as [FeedName, ...FeedName[]]).parse(req.params.feed);
    const snap = await snapshotFor(feed, req);
    const index = snap.entries.findIndex((e) => e.itemId === req.params.itemId);
    if (index < 0) throw notFound("Item in this feed");
    const entry = snap.entries[index]!;
    return {
      feed,
      rank: index + 1,
      score: entry.score,
      topFactors: explain(entry, 3),
      allFactors: entry.factors,
      rulesVersion: snap.rulesVersion,
      computedAt: snap.computedAt,
      rulesUrl: "/how-ranking-works",
    };
  });

  app.get("/v1/ranking/rules", async () => {
    const active = ctx.rules.active(ctx.now());
    return {
      version: active.version,
      effectiveFrom: active.effectiveFrom,
      feeds: describeFeeds(active.weights),
      weights: active.weights,
      refreshMinutes: ctx.config.rankingIntervalMs / 60_000,
      changelog: ctx.rules.changelog().map((v) => ({
        version: v.version,
        effectiveFrom: v.effectiveFrom,
        summary: v.summary,
        reason: v.change.reason,
        announcedAt: v.change.createdAt,
        weights: v.weights,
      })),
    };
  });

  app.post("/v1/events", async (req, reply) => {
    const body = z.object({ type: z.enum(["view", "play", "like"]), itemId: z.string() }).parse(req.body);
    const release = store.releases.get(body.itemId);
    if (!release || release.status !== "published") throw notFound("Release");
    const user = currentUser(ctx, req);
    if (body.type === "like" && !user) requireUser(ctx, req);
    // Anonymous plays still count, at the unknown-account discount.
    const actorId = user?.id ?? `anon_${createHash("sha256").update(req.ip).digest("hex").slice(0, 16)}`;
    store.events.push({ id: newId("evt"), type: body.type, actorId, artistId: release.artistId, itemId: release.id, at: ctx.now() });
    return reply.code(202).send();
  });
}
