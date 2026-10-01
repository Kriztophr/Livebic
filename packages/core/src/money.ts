/**
 * All money inside Livebic is held as integer minor units to avoid float drift.
 * Naira amounts are in kobo (1 NGN = 100 kobo).
 */
export type Kobo = number;

export type Currency = "NGN" | "USD" | "GBP";

export function assertMinorUnits(amount: number, label = "amount"): void {
  if (!Number.isSafeInteger(amount) || amount < 0) {
    throw new RangeError(`${label} must be a non-negative integer in minor units, got ${amount}`);
  }
}

export function naira(n: number): Kobo {
  return Math.round(n * 100);
}

const ngnFormatter = new Intl.NumberFormat("en-NG", {
  style: "currency",
  currency: "NGN",
  maximumFractionDigits: 0,
});

/** Fan-facing price label, e.g. "₦2,000". Kobo are dropped because prices are set in whole naira. */
export function formatNaira(kobo: Kobo): string {
  return ngnFormatter.format(Math.round(kobo / 100));
}

/** Fixed tip amounts offered on every artist page (spec: "Tip (fixed amounts in naira)"). */
export const TIP_AMOUNTS_KOBO: readonly Kobo[] = [naira(500), naira(1000), naira(2000), naira(5000), naira(10000)];
