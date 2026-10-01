/**
 * Product principle 1, "Hidden blockchain": no crypto vocabulary in fan-facing copy.
 * Used by tests over UI strings and by the admin content tools.
 */
export const FORBIDDEN_FAN_TERMS = [
  "blockchain",
  "crypto",
  "wallet address",
  "seed phrase",
  "gas fee",
  "gas",
  "nft",
  "token",
  "usdc",
  "stablecoin",
  "web3",
  "mint",
  "on-chain",
  "onchain",
] as const;

export function findForbiddenTerms(text: string): string[] {
  const lower = text.toLowerCase();
  return FORBIDDEN_FAN_TERMS.filter((term) => new RegExp(`\\b${term.replace(/[-\s]/g, "[-\\s]?")}\\b`).test(lower));
}
