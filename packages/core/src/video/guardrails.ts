/**
 * Blocked categories are enforced before generation (spec: Digital twins — Guardrails).
 * This is the first, cheap line: a vocabulary check on the brief and every shot direction.
 * The generation partner's own moderation runs after it.
 */
export type BlockedCategory = "political" | "sexual" | "health-or-financial-claim" | "impersonation";

const PATTERNS: Record<BlockedCategory, RegExp> = {
  political: /\b(vote for|endorse[sd]?|apc|pdp|labour party|lp candidate|president(ial)? candidate|governor(ship)? candidate|campaign rally|tinubu|atiku|obi)\b/i,
  sexual: /\b(nude|naked|sex(ual)?|porn|explicit|strip(ping)?|erotic)\b/i,
  "health-or-financial-claim": /\b(cures?|heals?|guaranteed (returns?|profit)|get rich|invest(ment)? (scheme|opportunity)|forex signal|miracle (drug|cure))\b/i,
  impersonation: /\b(as|like|playing|dressed as|impersonat(e|ing)) (davido|burna boy|wizkid|tems|rema|asake|ayra starr|tiwa savage|olamide|beyonc[eé]|drake)\b/i,
};

export interface GuardrailHit {
  category: BlockedCategory;
  text: string;
}

export function checkDirection(text: string): GuardrailHit[] {
  const hits: GuardrailHit[] = [];
  for (const [category, re] of Object.entries(PATTERNS) as [BlockedCategory, RegExp][]) {
    const m = text.match(re);
    if (m) hits.push({ category, text: m[0] });
  }
  return hits;
}

export function checkTreatmentText(parts: readonly string[]): GuardrailHit[] {
  return parts.flatMap(checkDirection);
}

/** Label every output carries (spec: "AI message approved by [artist]"). */
export function aiLabel(artistName: string): string {
  return `AI-generated video, approved by ${artistName}`;
}

export interface ConsentTerms {
  /** The artist, not a third party, is the person in the reference photos. */
  isSelf: true;
  allowedUses: ("music-video" | "teaser" | "social-clip")[];
  blockedTopics: string[];
  signedAt: string;
}
