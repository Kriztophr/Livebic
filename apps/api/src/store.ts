import type { Currency, EngagementEvent, ReviewFlag, SignedReceipt, SplitShare, SupportKind } from "@livebic/core";

/**
 * In-memory store used in sandbox mode and tests. Shapes mirror db/schema.sql, which is the
 * Postgres source of truth for production; a Postgres-backed store implements the same fields.
 */

export type Role = "fan" | "artist" | "admin";

export interface User {
  id: string;
  email: string;
  name: string;
  role: Role;
  createdAt: Date;
  /** Email confirmed (sandbox: immediately). Feeds ranking's verified-account check. */
  verified: boolean;
  walletId: string;
  walletAddress: string;
  artistId: string | null;
  /** Lowercase login name carried over from the legacy app. */
  username?: string;
  /** bcrypt hash carried over from the legacy app so migrated users keep their password. */
  passwordHash?: string | null;
}

export type VerificationStatus = "unverified" | "pending" | "verified" | "rejected";

export interface Artist {
  id: string;
  ownerUserId: string;
  handle: string;
  displayName: string;
  bio: string;
  links: string[];
  createdAt: Date;
  verification: VerificationStatus;
  verificationRequest: { idDocumentRef: string; socialProofUrls: string[]; submittedAt: Date } | null;
  membershipPriceKobo: number | null;
  payout: { method: "bank" | "stablecoin"; destination: string } | null;
  showWalletAddress: boolean;
}

export type Access = "public" | "supporters" | "paid";

export interface Release {
  id: string;
  artistId: string;
  title: string;
  description: string;
  lyrics: string;
  access: Access;
  /** One-off unlock price for paid releases. */
  priceKobo: number | null;
  splits: SplitShare[];
  edition: { size: number; priceKobo: number; sold: number } | null;
  audio: { objectKey: string; contentType: string; sha256: string; registryTx: string; bytes: number } | null;
  cover?: { objectKey: string; contentType: string } | null;
  /** Legacy track id, so old /track/<id> links keep working. */
  legacyAudioId?: string;
  status: "draft" | "published" | "removed";
  rightsWarrantedAt: Date;
  createdAt: Date;
  publishedAt: Date | null;
}

export type OrderStatus = "pending" | "paid" | "failed" | "refund_required";

export interface Order {
  id: string;
  fanId: string;
  artistId: string;
  releaseId: string | null;
  kind: SupportKind;
  amountKobo: number;
  chargeMinor: number;
  currency: Currency;
  status: OrderStatus;
  checkoutRef: string;
  createdAt: Date;
  paidAt: Date | null;
  settlementRef: string | null;
  editionNumber: number | null;
  receipt: (SignedReceipt & { registryTx: string }) | null;
  membershipEndsAt: Date | null;
  /** Set on purchases imported from the legacy app; already settled there, so never credited to the ledger. */
  legacy?: { source: "deepsound"; currency: string; amount: number };
}

export interface LedgerEntry {
  id: string;
  userId: string;
  amountKobo: number;
  reason: "sale" | "platform_fee" | "payout" | "payout_reversal";
  orderId: string | null;
  payoutId: string | null;
  at: Date;
}

export interface Payout {
  id: string;
  userId: string;
  amountKobo: number;
  method: "bank" | "stablecoin";
  destination: string;
  status: "pending_approval" | "submitted" | "rejected";
  partnerRef: string | null;
  createdAt: Date;
  decidedBy: string | null;
}

export interface Report {
  id: string;
  reporterId: string;
  releaseId: string;
  reason: string;
  status: "open" | "actioned" | "dismissed";
  createdAt: Date;
}

export interface IdempotencyRecord {
  fingerprint: string;
  statusCode: number;
  body: unknown;
}

export class MemoryStore {
  users = new Map<string, User>();
  sessions = new Map<string, string>();
  artists = new Map<string, Artist>();
  releases = new Map<string, Release>();
  orders = new Map<string, Order>();
  ledger: LedgerEntry[] = [];
  payouts = new Map<string, Payout>();
  events: EngagementEvent[] = [];
  follows = new Map<string, Set<string>>();
  reports = new Map<string, Report>();
  flags: (ReviewFlag & { id: string; raisedAt: Date; status: "open" | "cleared" })[] = [];
  idempotency = new Map<string, IdempotencyRecord>();

  userByLogin(identifier: string): User | undefined {
    const id = identifier.trim().toLowerCase();
    for (const u of this.users.values()) if (u.email === id || u.username === id) return u;
    return undefined;
  }

  releaseByLegacyAudioId(audioId: string): Release | undefined {
    for (const r of this.releases.values()) if (r.legacyAudioId === audioId) return r;
    return undefined;
  }

  userByEmail(email: string): User | undefined {
    const e = email.toLowerCase();
    for (const u of this.users.values()) if (u.email === e) return u;
    return undefined;
  }

  artistByHandle(handle: string): Artist | undefined {
    const h = handle.toLowerCase();
    for (const a of this.artists.values()) if (a.handle === h) return a;
    return undefined;
  }

  balance(userId: string): number {
    return this.ledger.reduce((s, e) => (e.userId === userId ? s + e.amountKobo : s), 0);
  }

  paidOrders(filter: (o: Order) => boolean = () => true): Order[] {
    return [...this.orders.values()].filter((o) => o.status === "paid" && filter(o));
  }

  /** Artists a fan follows or has paid. */
  followedArtistIds(userId: string): Set<string> {
    const ids = new Set(this.follows.get(userId) ?? []);
    for (const o of this.paidOrders((o) => o.fanId === userId)) ids.add(o.artistId);
    return ids;
  }

  isSupporter(userId: string, artistId: string): boolean {
    return this.paidOrders((o) => o.fanId === userId && o.artistId === artistId).length > 0;
  }
}
