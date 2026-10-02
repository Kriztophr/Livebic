import { naira, type Kobo } from "../money";
import type { PlannedShot, Quality, VideoFormat } from "./types";
import { totalSeconds } from "./plan";

/**
 * Generation cost is passed through inside the price so videos never run at a loss (spec: Pricing).
 * Rates are per generated second and are placeholders until Higgsfield's credit prices are costed.
 */
export interface VideoPricing {
  draftPerSecondKobo: Kobo;
  finalPerSecondKobo: Kobo;
  /** Draft takes generated per shot before the artist picks one. */
  takesPerShot: number;
  /** Free teasers per artist per calendar month. */
  starterTeasersPerMonth: number;
  minFundingGoalKobo: Kobo;
}

export const DEFAULT_VIDEO_PRICING: VideoPricing = {
  draftPerSecondKobo: naira(40),
  finalPerSecondKobo: naira(300),
  takesPerShot: 2,
  starterTeasersPerMonth: 1,
  minFundingGoalKobo: naira(20_000),
};

export interface VideoQuote {
  draftKobo: Kobo;
  finalKobo: Kobo;
  totalKobo: Kobo;
  seconds: number;
  takes: number;
}

export function quoteVideo(shots: readonly PlannedShot[], pricing: VideoPricing = DEFAULT_VIDEO_PRICING): VideoQuote {
  const seconds = totalSeconds(shots);
  const takes = shots.length * pricing.takesPerShot;
  const draftKobo = Math.ceil(seconds * pricing.takesPerShot * pricing.draftPerSecondKobo);
  const finalKobo = Math.ceil(seconds * pricing.finalPerSecondKobo);
  return { draftKobo, finalKobo, totalKobo: draftKobo + finalKobo, seconds, takes };
}

export function clipCost(durationSeconds: number, quality: Quality, pricing: VideoPricing = DEFAULT_VIDEO_PRICING): Kobo {
  return Math.ceil(durationSeconds * (quality === "draft" ? pricing.draftPerSecondKobo : pricing.finalPerSecondKobo));
}

export type Tier = "starter" | "fan-funded" | "studio";

export interface CoverageInput {
  format: VideoFormat;
  quoteKobo: Kobo;
  starterTeasersUsedThisMonth: number;
  fundingRaisedKobo: Kobo;
  artistBalanceKobo: Kobo;
  pricing?: VideoPricing;
}

export interface Coverage {
  tier: Tier;
  /** Amount the artist's balance is charged after free quota and fan funding. */
  fromBalanceKobo: Kobo;
  fromFundingKobo: Kobo;
  covered: boolean;
  shortfallKobo: Kobo;
}

/** Who pays for a video: free starter quota first, then what fans raised, then the artist's balance. */
export function coverage(input: CoverageInput): Coverage {
  const pricing = input.pricing ?? DEFAULT_VIDEO_PRICING;
  if (input.format === "teaser" && input.starterTeasersUsedThisMonth < pricing.starterTeasersPerMonth) {
    return { tier: "starter", fromBalanceKobo: 0, fromFundingKobo: 0, covered: true, shortfallKobo: 0 };
  }
  const fromFunding = Math.min(input.fundingRaisedKobo, input.quoteKobo);
  const remaining = input.quoteKobo - fromFunding;
  const fromBalance = Math.min(input.artistBalanceKobo, remaining);
  const shortfall = remaining - fromBalance;
  return {
    tier: fromFunding > 0 ? "fan-funded" : "studio",
    fromBalanceKobo: fromBalance,
    fromFundingKobo: fromFunding,
    covered: shortfall === 0,
    shortfallKobo: shortfall,
  };
}
