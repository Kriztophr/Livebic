import type { FeedName } from "./types";

/** Every tunable number the ranking job uses. Published verbatim on the "How ranking works" page. */
export interface RankingWeights {
  halfLifeDays: number;
  lookbackDays: number;
  free: { view: number; play: number; like: number; follow: number };
  paid: { perAction: number; perLog10Naira: number; perUniqueSupporter: number };
  discounts: { unverified: number; newAccount: number; newAccountDays: number };
  rising: {
    maxArtistAgeDays: number;
    growthWindowDays: number;
    supporterGrowth: number;
    paidPerView: number;
    freeEngagement: number;
  };
  diversity: { topSlots: number; maxPerArtist: number };
  velocity: { windowHours: number; minEvents: number; multiplier: number };
}

export const DEFAULT_WEIGHTS: RankingWeights = {
  halfLifeDays: 7,
  lookbackDays: 28,
  free: { view: 0, play: 0.1, like: 0.3, follow: 0.5 },
  paid: { perAction: 5, perLog10Naira: 2, perUniqueSupporter: 3 },
  discounts: { unverified: 0.25, newAccount: 0.5, newAccountDays: 7 },
  rising: { maxArtistAgeDays: 90, growthWindowDays: 7, supporterGrowth: 20, paidPerView: 50, freeEngagement: 1 },
  diversity: { topSlots: 20, maxPerArtist: 2 },
  velocity: { windowHours: 1, minEvents: 20, multiplier: 10 },
};

export interface ChangeRecord {
  id: string;
  author: string;
  reason: string;
  createdAt: string;
}

export interface RulesVersion {
  version: number;
  effectiveFrom: string;
  summary: string;
  weights: RankingWeights;
  change: ChangeRecord;
}

/** Minimum time a weight change sits in the public changelog before it takes effect. */
export const MIN_NOTICE_MS = 24 * 60 * 60 * 1000;

/**
 * Versioned ranking rules. Changes are append-only, need an admin change record, and are
 * published in the changelog before their effective date (spec: Transparency).
 */
export class RulesRegistry {
  private readonly versions: RulesVersion[];

  constructor(initial?: RulesVersion[]) {
    this.versions = initial?.length
      ? [...initial].sort((a, b) => a.version - b.version)
      : [
          {
            version: 1,
            effectiveFrom: new Date(0).toISOString(),
            summary: "Launch weights.",
            weights: DEFAULT_WEIGHTS,
            change: { id: "chg_launch", author: "livebic", reason: "Initial published rules", createdAt: new Date(0).toISOString() },
          },
        ];
  }

  active(now: Date = new Date()): RulesVersion {
    const live = this.versions.filter((v) => new Date(v.effectiveFrom) <= now);
    const current = live[live.length - 1];
    if (!current) throw new Error("No ranking rules in effect");
    return current;
  }

  /** Full changelog, newest first, including scheduled changes not yet in effect. */
  changelog(): RulesVersion[] {
    return [...this.versions].reverse();
  }

  propose(input: {
    weights: RankingWeights;
    summary: string;
    change: ChangeRecord;
    effectiveFrom: Date;
    now?: Date;
  }): RulesVersion {
    const now = input.now ?? new Date();
    if (!input.change.reason.trim()) throw new Error("A ranking change needs a reason");
    if (input.effectiveFrom.getTime() - now.getTime() < MIN_NOTICE_MS) {
      throw new Error("Ranking changes must be announced at least 24 hours before they take effect");
    }
    const latest = this.versions[this.versions.length - 1]!;
    if (input.effectiveFrom <= new Date(latest.effectiveFrom)) {
      throw new Error("A new version must take effect after the latest scheduled version");
    }
    validateWeights(input.weights);
    const next: RulesVersion = {
      version: latest.version + 1,
      effectiveFrom: input.effectiveFrom.toISOString(),
      summary: input.summary,
      weights: input.weights,
      change: input.change,
    };
    this.versions.push(next);
    return next;
  }
}

export function validateWeights(w: RankingWeights): void {
  const check = (ok: boolean, msg: string) => {
    if (!ok) throw new Error(`Invalid ranking weights: ${msg}`);
  };
  check(w.halfLifeDays > 0, "halfLifeDays must be positive");
  check(w.lookbackDays > 0, "lookbackDays must be positive");
  const maxFree = Math.max(...Object.values(w.free));
  check(w.paid.perAction > maxFree, "a paid action must outweigh any free action");
  check(w.discounts.unverified >= 0 && w.discounts.unverified <= 1, "unverified discount must be 0–1");
  check(w.discounts.newAccount >= 0 && w.discounts.newAccount <= 1, "new-account discount must be 0–1");
  check(w.diversity.maxPerArtist >= 1 && w.diversity.topSlots >= 1, "diversity cap must allow at least one slot");
}

export interface PublishedFeedRules {
  feed: FeedName;
  title: string;
  purpose: string;
  signals: string[];
}

/** Human-readable rules for the "How ranking works" page, generated from the live weights. */
export function describeFeeds(w: RankingWeights): PublishedFeedRules[] {
  const common = [
    `Signals fade with a ${w.halfLifeDays}-day half-life, so feeds refresh weekly.`,
    `Support from unverified accounts counts ×${w.discounts.unverified}; from accounts under ${w.discounts.newAccountDays} days old ×${w.discounts.newAccount}.`,
    "Supporting yourself and back-and-forth tipping between artists are not counted.",
    `No artist holds more than ${w.diversity.maxPerArtist} of the top ${w.diversity.topSlots} places.`,
  ];
  return [
    {
      feed: "following",
      title: "Following",
      purpose: "Posts from artists you support or follow.",
      signals: ["Newest first. Nothing else."],
    },
    {
      feed: "rising",
      title: "Rising",
      purpose: "New and small artists gaining real support.",
      signals: [
        `Only artists who joined in the last ${w.rising.maxArtistAgeDays} days.`,
        `New supporters in the last ${w.rising.growthWindowDays} days compared with supporters before that: ${w.rising.supporterGrowth} points per 100% growth.`,
        `Paid support per play: ${w.rising.paidPerView} points for every paid action per play.`,
        `Plays, likes and follows add a little: play ${w.free.play}, like ${w.free.like}, follow ${w.free.follow} points each.`,
        ...common,
      ],
    },
    {
      feed: "most-supported",
      title: "Most supported",
      purpose: "What the community is paying for this week.",
      signals: [
        `Each paid action (tip, unlock, membership, own-a-piece): ${w.paid.perAction} points.`,
        `Amount paid: ${w.paid.perLog10Naira} points for every tenfold step in naira (₦10 → 2, ₦100 → 4, ₦1,000 → 6 …).`,
        `Each different supporter: ${w.paid.perUniqueSupporter} points.`,
        ...common,
      ],
    },
    {
      feed: "newest",
      title: "Newest",
      purpose: "Everything on Livebic, latest first.",
      signals: ["Newest first. Nothing else."],
    },
  ];
}
