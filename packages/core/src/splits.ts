import { assertMinorUnits, type Kobo } from "./money";

/** Basis points: 10_000 bps = 100%. */
export type Bps = number;

export const MAX_PLATFORM_FEE_BPS: Bps = 999; // spec goal: platform cut under 10%
export const DEFAULT_PLATFORM_FEE_BPS: Bps = 800;

export interface SplitShare {
  payeeId: string;
  role: "creator" | "collaborator";
  bps: Bps;
}

export interface SplitAllocation {
  payeeId: string;
  role: SplitShare["role"] | "platform";
  amount: Kobo;
}

export function validateSplits(shares: readonly SplitShare[]): void {
  if (shares.length === 0) throw new Error("A release needs at least one payee");
  if (!shares.some((s) => s.role === "creator")) throw new Error("A release needs a creator payee");
  const seen = new Set<string>();
  let total = 0;
  for (const s of shares) {
    if (!Number.isInteger(s.bps) || s.bps <= 0) throw new Error(`Invalid share for ${s.payeeId}: ${s.bps} bps`);
    if (seen.has(s.payeeId)) throw new Error(`Duplicate payee ${s.payeeId}`);
    seen.add(s.payeeId);
    total += s.bps;
  }
  if (total !== 10_000) throw new Error(`Split shares must total 10000 bps (100%), got ${total}`);
}

/**
 * Divide a sale between the platform and the release's payees.
 * The platform fee comes off the top; the remainder is divided by share using the
 * largest-remainder method so allocations always sum exactly to the sale amount.
 */
export function allocateSale(
  amount: Kobo,
  shares: readonly SplitShare[],
  platformFeeBps: Bps = DEFAULT_PLATFORM_FEE_BPS,
): SplitAllocation[] {
  assertMinorUnits(amount);
  validateSplits(shares);
  if (!Number.isInteger(platformFeeBps) || platformFeeBps < 0 || platformFeeBps > MAX_PLATFORM_FEE_BPS) {
    throw new RangeError(`Platform fee must be 0–${MAX_PLATFORM_FEE_BPS} bps`);
  }

  const fee = Math.floor((amount * platformFeeBps) / 10_000);
  const net = amount - fee;

  const raw = shares.map((s, index) => {
    const exact = (net * s.bps) / 10_000;
    return { s, index, floor: Math.floor(exact), frac: exact - Math.floor(exact) };
  });
  let leftover = net - raw.reduce((sum, r) => sum + r.floor, 0);
  // Hand out leftover kobo by largest fractional part; ties go to earlier payees (creator first).
  const order = [...raw].sort((a, b) => b.frac - a.frac || a.index - b.index);
  for (const r of order) {
    if (leftover === 0) break;
    r.floor += 1;
    leftover -= 1;
  }

  const allocations: SplitAllocation[] = raw.map((r) => ({ payeeId: r.s.payeeId, role: r.s.role, amount: r.floor }));
  allocations.push({ payeeId: "platform", role: "platform", amount: fee });
  return allocations;
}
