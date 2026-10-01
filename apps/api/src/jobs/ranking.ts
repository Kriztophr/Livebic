import { computeFeeds, type RankActor, type RankArtist, type RankItem } from "@livebic/core";
import type { AppContext } from "../context";
import { newId } from "../ids";

/** Scheduled worker (spec: Ranking job). Reads events, applies the active published weights, writes the cache. */
export async function runRankingJob(ctx: AppContext): Promise<{ rulesVersion: number; flagged: number }> {
  const { store } = ctx;
  const now = ctx.now();
  const rules = ctx.rules.active(now);

  const actors = new Map<string, RankActor>();
  for (const u of store.users.values()) actors.set(u.id, { id: u.id, verified: u.verified, createdAt: u.createdAt, artistId: u.artistId });
  const artists = new Map<string, RankArtist>();
  for (const a of store.artists.values()) artists.set(a.id, { id: a.id, createdAt: a.createdAt });
  const items: RankItem[] = [];
  for (const r of store.releases.values()) {
    if (r.status === "published" && r.publishedAt) items.push({ id: r.id, artistId: r.artistId, publishedAt: r.publishedAt });
  }

  const out = computeFeeds({ events: store.events, actors, artists, items, weights: rules.weights, rulesVersion: rules.version, now });
  await Promise.all(Object.values(out.snapshots).map((s) => ctx.cache.set(s)));

  let flagged = 0;
  for (const f of out.flags) {
    if (store.flags.some((x) => x.status === "open" && x.itemId === f.itemId)) continue;
    store.flags.push({ ...f, id: newId("flg"), raisedAt: now, status: "open" });
    flagged++;
  }
  return { rulesVersion: rules.version, flagged };
}
