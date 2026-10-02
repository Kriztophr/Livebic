import { describe, expect, it } from "vitest";
import {
  DEFAULT_VIDEO_PRICING,
  aiLabel,
  checkTreatmentText,
  coverage,
  findForbiddenTerms,
  naira,
  planShots,
  quoteVideo,
  teaserWindow,
  templateDirector,
  totalSeconds,
  type SongAnalysis,
} from "../src";

const song: SongAnalysis = {
  durationSeconds: 180,
  bpm: 120,
  energy: "high",
  sections: [
    { kind: "intro", startSeconds: 0, endSeconds: 8 },
    { kind: "verse", startSeconds: 8, endSeconds: 40 },
    { kind: "chorus", startSeconds: 40, endSeconds: 72 },
    { kind: "verse", startSeconds: 72, endSeconds: 104 },
    { kind: "chorus", startSeconds: 104, endSeconds: 136 },
    { kind: "bridge", startSeconds: 136, endSeconds: 152 },
    { kind: "chorus", startSeconds: 152, endSeconds: 176 },
    { kind: "outro", startSeconds: 176, endSeconds: 180 },
  ],
};

describe("shot planning", () => {
  it("picks the first chorus for a 30-second teaser", () => {
    expect(teaserWindow(song)).toEqual({ start: 36, end: 66 });
  });

  it("cuts on the bar and covers the window exactly", () => {
    const shots = planShots(song, { format: "teaser" });
    expect(totalSeconds(shots)).toBe(30);
    expect(shots[0]).toMatchObject({ startSeconds: 36, durationSeconds: 4, section: "verse" });
    // High-energy chorus cuts every bar (2 s at 120 bpm).
    expect(shots.filter((s) => s.section === "chorus").every((s) => s.durationSeconds === 2)).toBe(true);
    expect(shots.every((s) => s.durationSeconds >= 2 && s.durationSeconds <= 8)).toBe(true);
  });

  it("plans a full video without gaps", () => {
    const shots = planShots(song, { format: "full" });
    expect(totalSeconds(shots)).toBe(180);
    for (let i = 1; i < shots.length; i++) expect(shots[i]!.startSeconds).toBeCloseTo(shots[i - 1]!.startSeconds + shots[i - 1]!.durationSeconds, 2);
  });
});

describe("pricing and coverage", () => {
  const shots = planShots(song, { format: "teaser" });
  const quote = quoteVideo(shots);

  it("quotes drafts for every take plus one final pass", () => {
    expect(quote.seconds).toBe(30);
    expect(quote.draftKobo).toBe(30 * 2 * DEFAULT_VIDEO_PRICING.draftPerSecondKobo);
    expect(quote.finalKobo).toBe(30 * DEFAULT_VIDEO_PRICING.finalPerSecondKobo);
  });

  it("uses the free teaser first, then fan funding, then the artist's balance", () => {
    expect(coverage({ format: "teaser", quoteKobo: quote.totalKobo, starterTeasersUsedThisMonth: 0, fundingRaisedKobo: 0, artistBalanceKobo: 0 })).toMatchObject({ tier: "starter", covered: true });
    const funded = coverage({ format: "full", quoteKobo: naira(100_000), starterTeasersUsedThisMonth: 0, fundingRaisedKobo: naira(60_000), artistBalanceKobo: naira(10_000) });
    expect(funded).toEqual({ tier: "fan-funded", fromFundingKobo: naira(60_000), fromBalanceKobo: naira(10_000), covered: false, shortfallKobo: naira(30_000) });
    const studio = coverage({ format: "full", quoteKobo: naira(100_000), starterTeasersUsedThisMonth: 1, fundingRaisedKobo: 0, artistBalanceKobo: naira(500_000) });
    expect(studio).toMatchObject({ tier: "studio", fromBalanceKobo: naira(100_000), covered: true });
  });
});

describe("director and guardrails", () => {
  it("writes a direction for every shot in plain language", async () => {
    const shots = planShots(song, { format: "teaser" });
    const t = await templateDirector.write({ songTitle: "Danfo Driver", artistName: "Tobi", lyrics: "Na Lagos we dey\nDanfo driver carry me go", song, shots, castMode: "likeness" });
    expect(Object.keys(t.directions)).toHaveLength(shots.length);
    expect(findForbiddenTerms(JSON.stringify(t))).toEqual([]);
    const revised = await templateDirector.revise({ songTitle: "Danfo Driver", artistName: "Tobi", lyrics: "", song, shots, castMode: "character", notes: "more dancing" }, t, "more dancing");
    expect(revised.concept).toContain("more dancing");
    expect(revised.concept.split("more dancing")).toHaveLength(2);
    expect(revised.directions[shots[0]!.id]).toContain("stylised animated performer");
  });

  it("blocks political, sexual, claim and impersonation directions", () => {
    const hits = checkTreatmentText(["Tobi tells fans to vote for the APC candidate", "dressed as Burna Boy on stage", "this tea cures malaria", "nude scene on the beach", "a danfo bus at night"]);
    expect(hits.map((h) => h.category).sort()).toEqual(["health-or-financial-claim", "impersonation", "political", "sexual"]);
    expect(aiLabel("Tobi")).toBe("AI-generated video, approved by Tobi");
  });
});
