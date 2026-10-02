export type FeedName = "following" | "rising" | "most-supported" | "newest";
export const FEED_NAMES: readonly FeedName[] = ["following", "rising", "most-supported", "newest"];

export type FreeEventType = "view" | "play" | "like" | "follow";
export type PaidEventType = "tip" | "unlock" | "membership" | "drop" | "fund";
export type EngagementType = FreeEventType | PaidEventType;

export const PAID_EVENT_TYPES: readonly PaidEventType[] = ["tip", "unlock", "membership", "drop", "fund"];

export function isPaid(type: EngagementType): type is PaidEventType {
  return (PAID_EVENT_TYPES as readonly string[]).includes(type);
}

export interface EngagementEvent {
  id: string;
  type: EngagementType;
  actorId: string;
  artistId: string;
  /** Null for artist-level events such as a follow or a general tip. */
  itemId: string | null;
  at: Date;
  amountKobo?: number;
}

export interface RankActor {
  id: string;
  verified: boolean;
  createdAt: Date;
  /** Artist profile this user runs, if any. Used for self-support and circular tipping checks. */
  artistId: string | null;
}

export interface RankArtist {
  id: string;
  createdAt: Date;
}

export interface RankItem {
  id: string;
  artistId: string;
  publishedAt: Date;
}

export interface ScoreFactor {
  key: string;
  /** Plain-language label shown in "Why this?". */
  label: string;
  points: number;
}

export interface FeedEntry {
  itemId: string;
  artistId: string;
  score: number;
  factors: ScoreFactor[];
}

export interface FeedSnapshot {
  feed: FeedName;
  rulesVersion: number;
  computedAt: string;
  entries: FeedEntry[];
}

export interface ReviewFlag {
  kind: "velocity_spike";
  itemId: string;
  artistId: string;
  eventsInWindow: number;
  baselinePerWindow: number;
}
