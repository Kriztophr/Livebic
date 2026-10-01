import { readFile } from "node:fs/promises";
import { join } from "node:path";
import type { EngagementEvent, SplitShare, SupportKind } from "@livebic/core";
import type { AppContext } from "../context";
import type { Access, Artist, Order, Release, User, VerificationStatus } from "../store";

/**
 * Store-agnostic snapshot produced by a one-off legacy import. Dates are ISO strings and media
 * files sit next to bundle.json under media/<sha256>. Loading it is idempotent: records whose
 * ids already exist are skipped, so an import can be re-run after fixing data.
 */
export interface MigrationBundle {
  format: 1;
  source: "deepsound";
  createdAt: string;
  currency: { legacy: string; ngnPerUnit: number };
  users: BundleUser[];
  artists: BundleArtist[];
  releases: BundleRelease[];
  follows: { userId: string; artistId: string; at: string }[];
  events: (Omit<EngagementEvent, "at"> & { at: string })[];
  orders: BundleOrder[];
}

export interface BundleUser {
  id: string;
  email: string;
  username: string;
  name: string;
  role: User["role"];
  createdAt: string;
  verified: boolean;
  passwordHash: string | null;
  artistId: string | null;
}

export interface BundleArtist {
  id: string;
  ownerUserId: string;
  handle: string;
  displayName: string;
  bio: string;
  links: string[];
  createdAt: string;
  verification: VerificationStatus;
}

export interface BundleMedia {
  /** Path in the legacy upload store, e.g. upload/audio/2021/03/abc.mp3 */
  legacyPath: string;
  contentType: string;
  /** Filled in once the file has been copied into the bundle. */
  sha256: string | null;
  bytes: number | null;
}

export interface BundleRelease {
  id: string;
  artistId: string;
  legacyAudioId: string;
  title: string;
  description: string;
  lyrics: string;
  access: Access;
  priceKobo: number | null;
  splits: SplitShare[];
  status: "draft" | "published";
  createdAt: string;
  publishedAt: string | null;
  audio: BundleMedia | null;
  cover: BundleMedia | null;
}

export interface BundleOrder {
  id: string;
  fanId: string;
  artistId: string;
  releaseId: string;
  kind: SupportKind;
  amountKobo: number;
  paidAt: string;
  legacy: { source: "deepsound"; currency: string; amount: number };
}

export async function readBundle(dir: string): Promise<MigrationBundle> {
  const bundle = JSON.parse(await readFile(join(dir, "bundle.json"), "utf8")) as MigrationBundle;
  if (bundle.format !== 1) throw new Error(`Unsupported bundle format ${bundle.format}`);
  return bundle;
}

export interface LoadResult {
  users: number;
  artists: number;
  releases: number;
  releasesWithoutAudio: number;
  follows: number;
  events: number;
  orders: number;
}

/** Load a bundle into the running store. Audio is fingerprinted into the registry as it loads. */
export async function loadBundle(ctx: AppContext, bundle: MigrationBundle, mediaDir: string): Promise<LoadResult> {
  const { store, partners } = ctx;
  const result: LoadResult = { users: 0, artists: 0, releases: 0, releasesWithoutAudio: 0, follows: 0, events: 0, orders: 0 };

  for (const u of bundle.users) {
    if (store.users.has(u.id) || store.userByEmail(u.email)) continue;
    // Each migrated account gets a fresh embedded wallet, same as a new sign-up.
    const wallet = await partners.wallets.createWallet(u.id);
    store.users.set(u.id, {
      id: u.id,
      email: u.email,
      username: u.username,
      name: u.name,
      role: u.role,
      createdAt: new Date(u.createdAt),
      verified: u.verified,
      walletId: wallet.walletId,
      walletAddress: wallet.address,
      artistId: u.artistId,
      passwordHash: u.passwordHash,
    });
    result.users++;
  }

  for (const a of bundle.artists) {
    if (store.artists.has(a.id) || store.artistByHandle(a.handle) || !store.users.has(a.ownerUserId)) continue;
    const artist: Artist = {
      ...a,
      createdAt: new Date(a.createdAt),
      verificationRequest: null,
      membershipPriceKobo: null,
      // Payout details are not carried over: artists re-enter them and pass the partner's KYC.
      payout: null,
      showWalletAddress: false,
    };
    store.artists.set(a.id, artist);
    result.artists++;
  }

  for (const r of bundle.releases) {
    if (store.releases.has(r.id) || !store.artists.has(r.artistId)) continue;
    let audio: Release["audio"] = null;
    if (r.audio?.sha256) {
      const body = await readFile(join(mediaDir, r.audio.sha256));
      const objectKey = `audio/${r.id}/${r.audio.sha256}`;
      await partners.storage.put(objectKey, body, r.audio.contentType);
      const { txRef } = await partners.chain.recordContentHash({ releaseId: r.id, sha256: r.audio.sha256 });
      audio = { objectKey, contentType: r.audio.contentType, sha256: r.audio.sha256, registryTx: txRef, bytes: body.length };
    }
    let cover: Release["cover"] = null;
    if (r.cover?.sha256) {
      const objectKey = `cover/${r.id}/${r.cover.sha256}`;
      await partners.storage.put(objectKey, await readFile(join(mediaDir, r.cover.sha256)), r.cover.contentType);
      cover = { objectKey, contentType: r.cover.contentType };
    }
    // A release can't be live without its audio; those come in as drafts for the artist to fix.
    const status = r.status === "published" && audio ? "published" : "draft";
    if (!audio) result.releasesWithoutAudio++;
    store.releases.set(r.id, {
      id: r.id,
      artistId: r.artistId,
      legacyAudioId: r.legacyAudioId,
      title: r.title,
      description: r.description,
      lyrics: r.lyrics,
      access: r.access,
      priceKobo: r.priceKobo,
      splits: r.splits,
      edition: null,
      audio,
      cover,
      status,
      rightsWarrantedAt: new Date(r.createdAt),
      createdAt: new Date(r.createdAt),
      publishedAt: status === "published" && r.publishedAt ? new Date(r.publishedAt) : null,
    });
    result.releases++;
  }

  for (const f of bundle.follows) {
    if (!store.users.has(f.userId) || !store.artists.has(f.artistId)) continue;
    const set = store.follows.get(f.userId) ?? new Set<string>();
    if (set.has(f.artistId)) continue;
    set.add(f.artistId);
    store.follows.set(f.userId, set);
    result.follows++;
  }

  const seenEvents = new Set(store.events.map((e) => e.id));
  for (const e of bundle.events) {
    if (seenEvents.has(e.id) || !store.artists.has(e.artistId)) continue;
    if (e.itemId && !store.releases.has(e.itemId)) continue;
    store.events.push({ ...e, at: new Date(e.at) });
    result.events++;
  }

  for (const o of bundle.orders) {
    if (store.orders.has(o.id) || !store.users.has(o.fanId) || !store.releases.has(o.releaseId)) continue;
    const paidAt = new Date(o.paidAt);
    const order: Order = {
      id: o.id,
      fanId: o.fanId,
      artistId: o.artistId,
      releaseId: o.releaseId,
      kind: o.kind,
      amountKobo: o.amountKobo,
      chargeMinor: o.amountKobo,
      currency: "NGN",
      status: "paid",
      checkoutRef: `legacy_${o.id}`,
      createdAt: paidAt,
      paidAt,
      settlementRef: null,
      editionNumber: null,
      receipt: null,
      membershipEndsAt: null,
      legacy: o.legacy,
    };
    store.orders.set(order.id, order);
    result.orders++;
  }
  return result;
}
