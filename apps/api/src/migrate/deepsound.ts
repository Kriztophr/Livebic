import type { MigrationBundle, BundleArtist, BundleMedia, BundleOrder, BundleRelease, BundleUser } from "./bundle";

/**
 * DeepSound (the 2022 PHP app in Script/) → Livebic mapping. Pure: takes rows, returns a bundle
 * plus a report. Reading MySQL and copying media live in source.ts and cli.ts.
 */

export interface DsUser {
  id: number;
  username: string;
  email: string;
  password: string;
  name: string;
  about: string | null;
  facebook: string | null;
  twitter: string | null;
  instagram: string | null;
  website: string | null;
  active: number;
  admin: number;
  verified: number;
  artist: number;
  registered: string | null;
  time: number | null;
  balance: number | string | null;
  wallet: number | string | null;
}

export interface DsSong {
  id: number;
  user_id: number;
  audio_id: string;
  title: string;
  description: string | null;
  lyrics: string | null;
  thumbnail: string | null;
  availability: number;
  price: number | string | null;
  audio_location: string | null;
  time: number;
}

export interface DsFollower {
  id: number;
  follower_id: number;
  following_id: number;
  time: number;
}

export interface DsLike {
  id: number;
  track_id: number;
  user_id: number;
  time: number;
}

export interface DsView {
  id: number;
  track_id: number;
  user_id: number;
  fingerprint: string | null;
  time: number;
}

export interface DsPurchase {
  id: number;
  user_id: number;
  track_id: number;
  event_id: number;
  price: number | string;
  final_price: number | string;
  time: number;
}

export interface DsWithdrawal {
  id: number;
  user_id: number;
  amount: string;
  currency: string;
  status: number;
  requested: string | null;
  type: string | null;
}

export interface DeepSoundRows {
  config: Record<string, string>;
  users: DsUser[];
  songs: DsSong[];
  followers: DsFollower[];
  likes: DsLike[];
  views: DsView[];
  purchases: DsPurchase[];
  withdrawals: DsWithdrawal[];
  /** Row counts of tables with no Livebic equivalent yet, for the report. */
  notMigrated: Record<string, number>;
}

export interface SettlementRow {
  legacyUserId: number;
  username: string;
  email: string;
  earningsBalance: number;
  walletCredit: number;
  pendingWithdrawal: number;
}

export interface MigrationReport {
  legacyCurrency: string;
  ngnPerUnit: number;
  counts: Record<string, { legacy: number; imported: number }>;
  skipped: Record<string, number>;
  usersNeedingPasswordReset: number;
  promotedToArtist: number;
  pricesRaisedToMinimum: { legacyAudioId: string; title: string; legacyPrice: number }[];
  settlement: SettlementRow[];
  notMigrated: Record<string, number>;
  warnings: string[];
}

export const userId = (id: number) => `usr_ds${id}`;
export const artistId = (id: number) => `art_ds${id}`;
export const releaseId = (id: number) => `rel_ds${id}`;

const DEFAULT_MEDIA = new Set(["upload/photos/thumbnail.jpg", "upload/photos/d-avatar.jpg", "upload/photos/d-cover.jpg"]);
const MIN_PRICE_KOBO = 10_000;

const AUDIO_TYPES: Record<string, string> = { mp3: "audio/mpeg", wav: "audio/wav", m4a: "audio/mp4", ogg: "audio/ogg", aac: "audio/aac", flac: "audio/flac" };
const IMAGE_TYPES: Record<string, string> = { jpg: "image/jpeg", jpeg: "image/jpeg", png: "image/png", webp: "image/webp", gif: "image/gif" };

function media(path: string | null | undefined, types: Record<string, string>): BundleMedia | null {
  if (!path || DEFAULT_MEDIA.has(path)) return null;
  const ext = path.split("?")[0]!.split(".").pop()?.toLowerCase() ?? "";
  const contentType = types[ext];
  if (!contentType) return null;
  return { legacyPath: path, contentType, sha256: null, bytes: null };
}

function legacyDate(unixSeconds: number | null | undefined, registered?: string | null): string {
  if (unixSeconds && unixSeconds > 0) return new Date(unixSeconds * 1000).toISOString();
  const m = registered?.match(/^(\d{4})\/(\d{1,2})$/);
  if (m) return new Date(Date.UTC(Number(m[1]), Number(m[2]) - 1, 1)).toISOString();
  return new Date(0).toISOString();
}

/** PHP's password_hash() writes $2y$; it is the same algorithm as $2b$, which bcryptjs verifies. */
export function portablePasswordHash(hash: string | null | undefined): string | null {
  if (!hash || !/^\$2[aby]\$\d{2}\$[./A-Za-z0-9]{53}$/.test(hash)) return null;
  return hash.replace(/^\$2y\$/, "$2b$");
}

function handleFrom(username: string, id: number, taken: Set<string>): string {
  let h = username.toLowerCase().replace(/[^a-z0-9_]/g, "_").replace(/_+/g, "_").replace(/^_|_$/g, "").slice(0, 30);
  if (h.length < 2) h = `artist_${id}`;
  if (taken.has(h)) h = `${h.slice(0, 30 - String(id).length - 1)}_${id}`;
  taken.add(h);
  return h;
}

const num = (v: number | string | null | undefined) => (v === null || v === undefined || v === "" ? 0 : Number(v));

function links(u: DsUser): string[] {
  const out: string[] = [];
  const add = (url: string) => {
    try {
      out.push(new URL(url).toString());
    } catch {}
  };
  if (u.website) add(/^https?:\/\//.test(u.website) ? u.website : `https://${u.website}`);
  if (u.facebook) add(`https://facebook.com/${u.facebook}`);
  if (u.twitter) add(`https://x.com/${u.twitter}`);
  if (u.instagram) add(/^https?:\/\//.test(u.instagram) ? u.instagram : `https://instagram.com/${u.instagram}`);
  return out.slice(0, 10);
}

export function mapDeepSound(rows: DeepSoundRows, opts: { ngnPerUnit?: number; now?: Date } = {}): { bundle: MigrationBundle; report: MigrationReport } {
  const legacyCurrency = (rows.config.currency || "USD").toUpperCase();
  const ngnPerUnit = legacyCurrency === "NGN" ? 1 : opts.ngnPerUnit;
  if (!ngnPerUnit || ngnPerUnit <= 0) {
    throw new Error(`The legacy site prices in ${legacyCurrency}. Pass --ngn-per-unit with the naira rate to convert prices and purchase history.`);
  }
  const toKobo = (amount: number) => Math.round(amount * ngnPerUnit * 100);

  const report: MigrationReport = {
    legacyCurrency,
    ngnPerUnit,
    counts: {},
    skipped: {},
    usersNeedingPasswordReset: 0,
    promotedToArtist: 0,
    pricesRaisedToMinimum: [],
    settlement: [],
    notMigrated: rows.notMigrated,
    warnings: [],
  };
  const skip = (reason: string) => (report.skipped[reason] = (report.skipped[reason] ?? 0) + 1);

  // Users: one account per email; the first (lowest id) wins on duplicates.
  const users = new Map<number, BundleUser>();
  const emails = new Set<string>();
  for (const u of [...rows.users].sort((a, b) => a.id - b.id)) {
    const email = (u.email ?? "").trim().toLowerCase();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      skip("user without a valid email");
      continue;
    }
    if (emails.has(email)) {
      skip("user with a duplicate email");
      continue;
    }
    emails.add(email);
    const passwordHash = portablePasswordHash(u.password);
    if (!passwordHash) report.usersNeedingPasswordReset++;
    users.set(u.id, {
      id: userId(u.id),
      email,
      username: (u.username ?? "").toLowerCase(),
      name: (u.name || u.username || email.split("@")[0]!).slice(0, 80),
      role: u.admin === 1 ? "admin" : "fan",
      createdAt: legacyDate(u.time, u.registered),
      verified: u.active === 1,
      passwordHash,
      artistId: null,
    });
  }

  // Artists: flagged artists, plus anyone who uploaded songs (Livebic needs an artist page to publish).
  const uploaders = new Set(rows.songs.map((s) => s.user_id));
  const handles = new Set<string>();
  const artists = new Map<number, BundleArtist>();
  for (const u of [...rows.users].sort((a, b) => a.id - b.id)) {
    const bu = users.get(u.id);
    if (!bu || (u.artist !== 1 && !uploaders.has(u.id))) continue;
    if (u.artist !== 1) report.promotedToArtist++;
    const a: BundleArtist = {
      id: artistId(u.id),
      ownerUserId: bu.id,
      handle: handleFrom(u.username || bu.name, u.id, handles),
      displayName: bu.name,
      bio: (u.about ?? "").slice(0, 2000),
      links: links(u),
      createdAt: bu.createdAt,
      // DeepSound's verified badge was admin-granted; payouts still need the new partner KYC.
      verification: u.artist === 1 && u.verified === 1 ? "verified" : "unverified",
    };
    artists.set(u.id, a);
    if (bu.role !== "admin") bu.role = "artist";
    bu.artistId = a.id;
  }

  const releases = new Map<number, BundleRelease>();
  for (const s of rows.songs) {
    const artist = artists.get(s.user_id);
    if (!artist) {
      skip("song whose uploader was not imported");
      continue;
    }
    const legacyPrice = num(s.price);
    let priceKobo: number | null = null;
    if (legacyPrice > 0) {
      priceKobo = toKobo(legacyPrice);
      if (priceKobo < MIN_PRICE_KOBO) {
        report.pricesRaisedToMinimum.push({ legacyAudioId: s.audio_id, title: s.title, legacyPrice });
        priceKobo = MIN_PRICE_KOBO;
      }
    }
    const audio = media(s.audio_location, AUDIO_TYPES);
    if (!audio && s.audio_location) report.warnings.push(`Track ${s.audio_id} has unsupported audio "${s.audio_location}"; imported as a draft.`);
    const createdAt = legacyDate(s.time);
    releases.set(s.id, {
      id: releaseId(s.id),
      artistId: artist.id,
      legacyAudioId: s.audio_id,
      title: (s.title || "Untitled").slice(0, 120),
      description: (s.description ?? "").slice(0, 5000),
      lyrics: (s.lyrics ?? "").slice(0, 20000),
      access: priceKobo ? "paid" : "public",
      priceKobo,
      splits: [{ payeeId: artist.ownerUserId, role: "creator", bps: 10_000 }],
      // availability 0 = public, 1 = private (owner only) in DeepSound.
      status: s.availability === 0 ? "published" : "draft",
      createdAt,
      publishedAt: s.availability === 0 ? createdAt : null,
      audio,
      cover: media(s.thumbnail, IMAGE_TYPES),
    });
  }

  const follows: MigrationBundle["follows"] = [];
  const events: MigrationBundle["events"] = [];
  for (const f of rows.followers) {
    const fan = users.get(f.follower_id);
    const artist = artists.get(f.following_id);
    if (!fan || !artist || fan.id === artist.ownerUserId) {
      skip("follow of a non-artist or missing user");
      continue;
    }
    const at = legacyDate(f.time);
    follows.push({ userId: fan.id, artistId: artist.id, at });
    events.push({ id: `evt_ds_follow_${f.id}`, type: "follow", actorId: fan.id, artistId: artist.id, itemId: null, at });
  }

  for (const l of rows.likes) {
    const fan = users.get(l.user_id);
    const r = releases.get(l.track_id);
    if (!fan || !r) {
      skip("like on a comment or missing track");
      continue;
    }
    events.push({ id: `evt_ds_like_${l.id}`, type: "like", actorId: fan.id, artistId: r.artistId, itemId: r.id, at: legacyDate(l.time) });
  }

  for (const v of rows.views) {
    const r = releases.get(v.track_id);
    if (!r) {
      skip("play of an album or missing track");
      continue;
    }
    const actorId = users.get(v.user_id)?.id ?? `anon_ds_${v.fingerprint || v.id}`;
    events.push({ id: `evt_ds_view_${v.id}`, type: "play", actorId, artistId: r.artistId, itemId: r.id, at: legacyDate(v.time) });
  }

  const orders: BundleOrder[] = [];
  for (const p of rows.purchases) {
    if (!p.track_id) {
      skip("purchase of an event ticket or store product");
      continue;
    }
    const fan = users.get(p.user_id);
    const r = releases.get(p.track_id);
    if (!fan || !r) {
      skip("purchase by a missing user or of a missing track");
      continue;
    }
    // `price` is what the fan paid; `final_price` was the artist's share after DeepSound's commission.
    const amount = num(p.price);
    const paidAt = legacyDate(p.time);
    const amountKobo = toKobo(amount);
    orders.push({
      id: `ord_ds${p.id}`,
      fanId: fan.id,
      artistId: r.artistId,
      releaseId: r.id,
      kind: "unlock",
      amountKobo,
      paidAt,
      legacy: { source: "deepsound", currency: legacyCurrency, amount },
    });
    events.push({ id: `evt_ds_purchase_${p.id}`, type: "unlock", actorId: fan.id, artistId: r.artistId, itemId: r.id, at: paidAt, amountKobo });
  }

  // Money still held by the old system. Not imported: it must be paid out or refunded before cutover.
  const pending = new Map<number, number>();
  for (const w of rows.withdrawals) if (w.status === 0) pending.set(w.user_id, (pending.get(w.user_id) ?? 0) + num(w.amount));
  for (const u of rows.users) {
    const row: SettlementRow = {
      legacyUserId: u.id,
      username: u.username,
      email: u.email,
      earningsBalance: num(u.balance),
      walletCredit: num(u.wallet),
      pendingWithdrawal: pending.get(u.id) ?? 0,
    };
    if (row.earningsBalance > 0 || row.walletCredit > 0 || row.pendingWithdrawal > 0) report.settlement.push(row);
  }
  report.settlement.sort((a, b) => b.earningsBalance + b.walletCredit - (a.earningsBalance + a.walletCredit));

  report.counts = {
    users: { legacy: rows.users.length, imported: users.size },
    artists: { legacy: rows.users.filter((u) => u.artist === 1).length, imported: artists.size },
    releases: { legacy: rows.songs.length, imported: releases.size },
    follows: { legacy: rows.followers.length, imported: follows.length },
    likes: { legacy: rows.likes.length, imported: events.filter((e) => e.type === "like").length },
    plays: { legacy: rows.views.length, imported: events.filter((e) => e.type === "play").length },
    purchases: { legacy: rows.purchases.length, imported: orders.length },
  };

  return {
    bundle: {
      format: 1,
      source: "deepsound",
      createdAt: (opts.now ?? new Date()).toISOString(),
      currency: { legacy: legacyCurrency, ngnPerUnit },
      users: [...users.values()],
      artists: [...artists.values()],
      releases: [...releases.values()],
      follows,
      events,
      orders,
    },
    report,
  };
}

function csvCell(v: string | number): string {
  const s = String(v);
  const safe = /^[=+\-@\t\r]/.test(s) ? `'${s}` : s;
  return /[",\r\n]/.test(safe) ? `"${safe.replace(/"/g, '""')}"` : safe;
}

export function settlementCsv(report: MigrationReport): string {
  const head = ["legacy_user_id", "username", "email", `earnings_balance_${report.legacyCurrency}`, `wallet_credit_${report.legacyCurrency}`, `pending_withdrawal_${report.legacyCurrency}`];
  const lines = report.settlement.map((r) => [r.legacyUserId, r.username, r.email, r.earningsBalance, r.walletCredit, r.pendingWithdrawal].map(csvCell).join(","));
  return [head.join(","), ...lines].join("\r\n") + "\r\n";
}

export function reportMarkdown(report: MigrationReport, extra: { mediaCopied: number; mediaMissing: string[] }): string {
  const total = (k: "earningsBalance" | "walletCredit" | "pendingWithdrawal") => report.settlement.reduce((s, r) => s + r[k], 0).toFixed(2);
  const lines = [
    "# DeepSound → Livebic import report",
    "",
    `Legacy currency: ${report.legacyCurrency} (₦${report.ngnPerUnit} per unit used for prices and purchase history).`,
    "",
    "| Data | In DeepSound | Imported |",
    "| --- | ---: | ---: |",
    ...Object.entries(report.counts).map(([k, c]) => `| ${k} | ${c.legacy} | ${c.imported} |`),
    "",
    `Media files copied: ${extra.mediaCopied}. Missing: ${extra.mediaMissing.length}.`,
    `Users who must reset their password (no bcrypt hash): ${report.usersNeedingPasswordReset}.`,
    `Uploaders given an artist page: ${report.promotedToArtist}.`,
    "",
    "## Money to settle before cutover",
    "",
    "Balances are **not** imported (Livebic never holds funds; see docs/SPEC.md). Pay out or refund these in the old system, then switch it off.",
    "",
    `- Earnings balances: ${report.legacyCurrency} ${total("earningsBalance")}`,
    `- Fan wallet credit: ${report.legacyCurrency} ${total("walletCredit")}`,
    `- Pending withdrawal requests: ${report.legacyCurrency} ${total("pendingWithdrawal")}`,
    `- Accounts affected: ${report.settlement.length} (see settlement.csv)`,
    "",
    "## Skipped",
    "",
    ...(Object.keys(report.skipped).length ? Object.entries(report.skipped).map(([k, n]) => `- ${k}: ${n}`) : ["- nothing"]),
    "",
    "## Not migrated (no Livebic equivalent yet)",
    "",
    ...(Object.values(report.notMigrated).some((n) => n > 0)
      ? Object.entries(report.notMigrated).filter(([, n]) => n > 0).map(([k, n]) => `- ${k}: ${n} rows`)
      : ["- nothing"]),
    "",
  ];
  if (report.pricesRaisedToMinimum.length) {
    lines.push("## Prices raised to the ₦100 minimum", "", ...report.pricesRaisedToMinimum.map((p) => `- ${p.title} (${p.legacyAudioId}): was ${p.legacyPrice}`), "");
  }
  if (extra.mediaMissing.length || report.warnings.length) {
    lines.push("## Warnings", "", ...report.warnings.map((w) => `- ${w}`), ...extra.mediaMissing.map((m) => `- Missing file: ${m}`), "");
  }
  return lines.join("\n");
}
