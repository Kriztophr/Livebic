import type { RankingWeights } from "./rules";
import {
  isPaid,
  type EngagementEvent,
  type FeedEntry,
  type FeedName,
  type FeedSnapshot,
  type RankActor,
  type RankArtist,
  type RankItem,
  type ReviewFlag,
  type ScoreFactor,
} from "./types";

const DAY_MS = 24 * 60 * 60 * 1000;
const HOUR_MS = 60 * 60 * 1000;

export interface RankingInput {
  events: readonly EngagementEvent[];
  actors: ReadonlyMap<string, RankActor>;
  artists: ReadonlyMap<string, RankArtist>;
  items: readonly RankItem[];
  weights: RankingWeights;
  rulesVersion: number;
  now: Date;
}

export interface RankingOutput {
  snapshots: Record<Exclude<FeedName, "following">, FeedSnapshot>;
  flags: ReviewFlag[];
  excludedEventIds: string[];
}

/** Multiplier for an actor's signals: unverified and brand-new accounts count for less. */
export function actorDiscount(actor: RankActor | undefined, w: RankingWeights, now: Date): number {
  if (!actor) return w.discounts.unverified * w.discounts.newAccount;
  let m = 1;
  if (!actor.verified) m *= w.discounts.unverified;
  if (now.getTime() - actor.createdAt.getTime() < w.discounts.newAccountDays * DAY_MS) m *= w.discounts.newAccount;
  return m;
}

export function decay(at: Date, now: Date, halfLifeDays: number): number {
  const ageDays = Math.max(0, now.getTime() - at.getTime()) / DAY_MS;
  return Math.pow(0.5, ageDays / halfLifeDays);
}

/**
 * Anti-gaming exclusions (spec: Scoring rules):
 *  - self-support: an artist's own account engaging with their own work;
 *  - circular tipping: two artists' accounts paying each other inside the lookback window.
 */
export function findExcludedEvents(
  events: readonly EngagementEvent[],
  actors: ReadonlyMap<string, RankActor>,
): Set<string> {
  const excluded = new Set<string>();
  const paidPairs = new Set<string>();
  for (const e of events) {
    const actorArtist = actors.get(e.actorId)?.artistId;
    if (actorArtist && actorArtist === e.artistId) excluded.add(e.id);
    else if (actorArtist && isPaid(e.type)) paidPairs.add(`${actorArtist}>${e.artistId}`);
  }
  for (const e of events) {
    const actorArtist = actors.get(e.actorId)?.artistId;
    if (!actorArtist || !isPaid(e.type) || actorArtist === e.artistId) continue;
    if (paidPairs.has(`${e.artistId}>${actorArtist}`)) excluded.add(e.id);
  }
  return excluded;
}

/**
 * Artist-level events (a tip on the profile, a follow) are credited to the artist's
 * latest item published at the time of the event.
 */
function resolveItemId(e: EngagementEvent, itemsByArtist: Map<string, RankItem[]>): string | null {
  if (e.itemId) return e.itemId;
  const items = itemsByArtist.get(e.artistId);
  if (!items) return null;
  let best: RankItem | null = null;
  for (const it of items) if (it.publishedAt <= e.at && (!best || it.publishedAt > best.publishedAt)) best = it;
  return best?.id ?? null;
}

function round(n: number): number {
  return Math.round(n * 10_000) / 10_000;
}

function entry(item: RankItem, factors: ScoreFactor[]): FeedEntry {
  const kept = factors.filter((f) => f.points > 0).map((f) => ({ ...f, points: round(f.points) }));
  return { itemId: item.id, artistId: item.artistId, score: round(kept.reduce((s, f) => s + f.points, 0)), factors: kept };
}

/** No artist takes more than `maxPerArtist` of the first `topSlots` places; bumped items keep their order below. */
export function applyDiversityCap<T extends { artistId: string }>(sorted: readonly T[], topSlots: number, maxPerArtist: number): T[] {
  const top: T[] = [];
  const bumped: T[] = [];
  const perArtist = new Map<string, number>();
  let i = 0;
  for (; i < sorted.length && top.length < topSlots; i++) {
    const e = sorted[i]!;
    const n = perArtist.get(e.artistId) ?? 0;
    if (n >= maxPerArtist) {
      bumped.push(e);
      continue;
    }
    perArtist.set(e.artistId, n + 1);
    top.push(e);
  }
  return [...top, ...bumped, ...sorted.slice(i)];
}

function byScore(a: FeedEntry, b: FeedEntry, publishedAt: Map<string, number>): number {
  return b.score - a.score || (publishedAt.get(b.itemId) ?? 0) - (publishedAt.get(a.itemId) ?? 0) || a.itemId.localeCompare(b.itemId);
}

export function computeFeeds(input: RankingInput): RankingOutput {
  const { weights: w, now, actors } = input;
  const nowMs = now.getTime();
  const lookbackStart = nowMs - w.lookbackDays * DAY_MS;
  const growthStart = nowMs - w.rising.growthWindowDays * DAY_MS;

  const items = input.items.filter((i) => i.publishedAt.getTime() <= nowMs);
  const itemById = new Map(items.map((i) => [i.id, i]));
  const itemsByArtist = new Map<string, RankItem[]>();
  for (const it of items) itemsByArtist.set(it.artistId, [...(itemsByArtist.get(it.artistId) ?? []), it]);
  const publishedAt = new Map(items.map((i) => [i.id, i.publishedAt.getTime()]));

  const pastEvents = input.events.filter((e) => e.at.getTime() <= nowMs);
  const excluded = findExcludedEvents(
    pastEvents.filter((e) => e.at.getTime() > lookbackStart),
    actors,
  );
  const eligible = pastEvents.filter((e) => !excluded.has(e.id));

  // Per-item accumulators.
  type Acc = {
    paidActions: number;
    paidValue: number;
    supporters: Map<string, number>;
    free: number;
    growthPaid: number;
    growthViews: number;
  };
  const acc = new Map<string, Acc>();
  const get = (id: string): Acc => {
    let a = acc.get(id);
    if (!a) acc.set(id, (a = { paidActions: 0, paidValue: 0, supporters: new Map(), free: 0, growthPaid: 0, growthViews: 0 }));
    return a;
  };

  // First eligible paid support per (artist, fan), across all history passed in.
  const firstSupport = new Map<string, Map<string, EngagementEvent>>();

  for (const e of eligible) {
    const t = e.at.getTime();
    const discount = actorDiscount(actors.get(e.actorId), w, now);

    if (isPaid(e.type)) {
      const fans = firstSupport.get(e.artistId) ?? new Map<string, EngagementEvent>();
      firstSupport.set(e.artistId, fans);
      const prev = fans.get(e.actorId);
      if (!prev || prev.at > e.at) fans.set(e.actorId, e);
    }
    if (t <= lookbackStart) continue;

    const itemId = resolveItemId(e, itemsByArtist);
    if (!itemId || !itemById.has(itemId)) continue;
    const a = get(itemId);
    const weight = decay(e.at, now, w.halfLifeDays) * discount;

    if (isPaid(e.type)) {
      const nairaPaid = (e.amountKobo ?? 0) / 100;
      a.paidActions += weight * w.paid.perAction;
      a.paidValue += weight * w.paid.perLog10Naira * Math.log10(1 + nairaPaid);
      a.supporters.set(e.actorId, Math.max(a.supporters.get(e.actorId) ?? 0, weight));
      if (t > growthStart) a.growthPaid += discount;
    } else {
      a.free += weight * w.free[e.type];
      if (t > growthStart && (e.type === "play" || e.type === "view")) a.growthViews += 1;
    }
  }

  // Artist-level supporter growth for Rising.
  const growth = new Map<string, { fresh: number; prior: number }>();
  for (const [artistId, fans] of firstSupport) {
    let fresh = 0;
    let prior = 0;
    for (const first of fans.values()) {
      if (first.at.getTime() > growthStart) fresh += actorDiscount(actors.get(first.actorId), w, now);
      else prior += 1;
    }
    growth.set(artistId, { fresh, prior });
  }

  const mostSupported: FeedEntry[] = [];
  const rising: FeedEntry[] = [];
  for (const item of items) {
    const a = acc.get(item.id);
    if (a && a.paidActions > 0) {
      const uniqueSupport = [...a.supporters.values()].reduce((s, v) => s + v, 0) * w.paid.perUniqueSupporter;
      mostSupported.push(
        entry(item, [
          { key: "paid_actions", label: "Paid support from fans", points: a.paidActions },
          { key: "paid_value", label: "Amount fans paid", points: a.paidValue },
          { key: "unique_supporters", label: `${a.supporters.size} different supporter${a.supporters.size === 1 ? "" : "s"}`, points: uniqueSupport },
        ]),
      );
    }

    const artist = input.artists.get(item.artistId);
    const isNewArtist = artist && nowMs - artist.createdAt.getTime() < w.rising.maxArtistAgeDays * DAY_MS;
    if (isNewArtist) {
      const g = growth.get(item.artistId);
      const growthRate = g ? g.fresh / (g.prior + 1) : 0;
      const conversion = a ? a.growthPaid / Math.max(1, a.growthViews, Math.ceil(a.growthPaid)) : 0;
      const e = entry(item, [
        { key: "supporter_growth", label: "Gaining new supporters this week", points: growthRate * w.rising.supporterGrowth },
        { key: "paid_per_play", label: "Listeners who go on to pay", points: conversion * w.rising.paidPerView },
        { key: "free_engagement", label: "Plays, likes and follows", points: (a?.free ?? 0) * w.rising.freeEngagement },
      ]);
      if (e.score > 0) rising.push(e);
    }
  }

  mostSupported.sort((x, y) => byScore(x, y, publishedAt));
  rising.sort((x, y) => byScore(x, y, publishedAt));

  const newest: FeedEntry[] = [...items]
    .sort((x, y) => y.publishedAt.getTime() - x.publishedAt.getTime() || x.id.localeCompare(y.id))
    .map((it) => ({ itemId: it.id, artistId: it.artistId, score: it.publishedAt.getTime(), factors: [{ key: "recency", label: "Newest first", points: 1 }] }));

  const snap = (feed: Exclude<FeedName, "following">, entries: FeedEntry[]): FeedSnapshot => ({
    feed,
    rulesVersion: input.rulesVersion,
    computedAt: now.toISOString(),
    entries,
  });

  return {
    snapshots: {
      rising: snap("rising", applyDiversityCap(rising, w.diversity.topSlots, w.diversity.maxPerArtist)),
      "most-supported": snap("most-supported", applyDiversityCap(mostSupported, w.diversity.topSlots, w.diversity.maxPerArtist)),
      newest: snap("newest", newest),
    },
    flags: detectVelocitySpikes(pastEvents, itemsByArtist, w, now),
    excludedEventIds: [...excluded],
  };
}

/** Flags items whose activity in the last window is far above their usual rate (spec: velocity spikes trigger review). */
export function detectVelocitySpikes(
  events: readonly EngagementEvent[],
  itemsByArtist: Map<string, RankItem[]>,
  w: RankingWeights,
  now: Date,
): ReviewFlag[] {
  const windowMs = w.velocity.windowHours * HOUR_MS;
  const windowStart = now.getTime() - windowMs;
  const lookbackStart = now.getTime() - w.lookbackDays * DAY_MS;
  const windowsInBaseline = (windowStart - lookbackStart) / windowMs;
  const counts = new Map<string, { artistId: string; recent: number; baseline: number }>();
  for (const e of events) {
    const t = e.at.getTime();
    if (t <= lookbackStart || t > now.getTime()) continue;
    const itemId = resolveItemId(e, itemsByArtist);
    if (!itemId) continue;
    const c = counts.get(itemId) ?? { artistId: e.artistId, recent: 0, baseline: 0 };
    if (t > windowStart) c.recent += 1;
    else c.baseline += 1;
    counts.set(itemId, c);
  }
  const flags: ReviewFlag[] = [];
  for (const [itemId, c] of counts) {
    const baselinePerWindow = c.baseline / windowsInBaseline;
    if (c.recent >= w.velocity.minEvents && c.recent >= w.velocity.multiplier * baselinePerWindow) {
      flags.push({ kind: "velocity_spike", itemId, artistId: c.artistId, eventsInWindow: c.recent, baselinePerWindow: round(baselinePerWindow) });
    }
  }
  return flags;
}

/** Following feed: chronological posts from artists the fan follows or supports. */
export function followingFeed(newest: FeedSnapshot, followedArtistIds: ReadonlySet<string>): FeedSnapshot {
  return {
    ...newest,
    feed: "following",
    entries: newest.entries
      .filter((e) => followedArtistIds.has(e.artistId))
      .map((e) => ({ ...e, factors: [{ key: "following", label: "From an artist you follow or support", points: 1 }] })),
  };
}

/** Top scoring factors for "Why this?" on a feed item. */
export function explain(entry: FeedEntry, limit = 3): ScoreFactor[] {
  return [...entry.factors].sort((a, b) => b.points - a.points).slice(0, limit);
}
