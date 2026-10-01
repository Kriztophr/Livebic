import type { Kobo } from "./money";

export type SupportKind = "tip" | "unlock" | "membership" | "drop";

/** Tier shown in the supporter list, highest commitment wins. */
export type SupporterTier = "owner" | "member" | "supporter";

const TIER_RANK: Record<SupporterTier, number> = { supporter: 0, member: 1, owner: 2 };

export function tierFor(kind: SupportKind): SupporterTier {
  if (kind === "drop") return "owner";
  if (kind === "membership") return "member";
  return "supporter";
}

export interface ConfirmedSupport {
  fanId: string;
  fanName: string;
  fanEmail: string;
  artistId: string;
  kind: SupportKind;
  amountKobo: Kobo;
  at: Date;
}

export interface SupporterRow {
  fanId: string;
  name: string;
  email: string;
  tier: SupporterTier;
  totalSupportKobo: Kobo;
  supportCount: number;
  firstSupportedAt: Date;
  lastSupportedAt: Date;
}

export function buildSupporterList(artistId: string, supports: Iterable<ConfirmedSupport>): SupporterRow[] {
  const rows = new Map<string, SupporterRow>();
  for (const s of supports) {
    if (s.artistId !== artistId) continue;
    const tier = tierFor(s.kind);
    const row = rows.get(s.fanId);
    if (!row) {
      rows.set(s.fanId, {
        fanId: s.fanId,
        name: s.fanName,
        email: s.fanEmail,
        tier,
        totalSupportKobo: s.amountKobo,
        supportCount: 1,
        firstSupportedAt: s.at,
        lastSupportedAt: s.at,
      });
      continue;
    }
    row.totalSupportKobo += s.amountKobo;
    row.supportCount += 1;
    if (TIER_RANK[tier] > TIER_RANK[row.tier]) row.tier = tier;
    if (s.at < row.firstSupportedAt) row.firstSupportedAt = s.at;
    if (s.at > row.lastSupportedAt) row.lastSupportedAt = s.at;
  }
  return [...rows.values()].sort((a, b) => b.totalSupportKobo - a.totalSupportKobo || a.name.localeCompare(b.name));
}

function csvCell(value: string): string {
  // Neutralise spreadsheet formula injection, then quote per RFC 4180.
  const safe = /^[=+\-@\t\r]/.test(value) ? `'${value}` : value;
  return /[",\r\n]/.test(safe) ? `"${safe.replace(/"/g, '""')}"` : safe;
}

export function supportersToCsv(rows: readonly SupporterRow[]): string {
  const header = ["name", "email", "tier", "total_support_ngn", "support_count", "first_supported_at", "last_supported_at"];
  const lines = rows.map((r) =>
    [
      r.name,
      r.email,
      r.tier,
      (r.totalSupportKobo / 100).toFixed(2),
      String(r.supportCount),
      r.firstSupportedAt.toISOString(),
      r.lastSupportedAt.toISOString(),
    ]
      .map(csvCell)
      .join(","),
  );
  return [header.join(","), ...lines].join("\r\n") + "\r\n";
}
