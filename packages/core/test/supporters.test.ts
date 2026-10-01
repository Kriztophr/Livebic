import { describe, expect, it } from "vitest";
import { buildSupporterList, findForbiddenTerms, formatNaira, supportersToCsv } from "../src";

describe("supporter list", () => {
  const d = (s: string) => new Date(s);
  const rows = buildSupporterList("art_1", [
    { fanId: "f1", fanName: "Ada", fanEmail: "ada@example.com", artistId: "art_1", kind: "tip", amountKobo: 100_000, at: d("2026-09-01") },
    { fanId: "f1", fanName: "Ada", fanEmail: "ada@example.com", artistId: "art_1", kind: "membership", amountKobo: 200_000, at: d("2026-09-10") },
    { fanId: "f2", fanName: "=HYPERLINK(\"x\")", fanEmail: "b@example.com", artistId: "art_1", kind: "tip", amountKobo: 50_000, at: d("2026-09-02") },
    { fanId: "f3", fanName: "Other", fanEmail: "c@example.com", artistId: "art_2", kind: "tip", amountKobo: 1, at: d("2026-09-02") },
  ]);

  it("aggregates per fan with the highest tier", () => {
    expect(rows).toHaveLength(2);
    expect(rows[0]).toMatchObject({ fanId: "f1", tier: "member", totalSupportKobo: 300_000, supportCount: 2 });
  });

  it("exports CSV that is safe to open in a spreadsheet", () => {
    const csv = supportersToCsv(rows);
    expect(csv.split("\r\n")[0]).toBe("name,email,tier,total_support_ngn,support_count,first_supported_at,last_supported_at");
    expect(csv).toContain("Ada,ada@example.com,member,3000.00,2");
    expect(csv).toContain(`"'=HYPERLINK(""x"")"`);
  });
});

describe("fan-facing copy", () => {
  it("formats naira", () => expect(formatNaira(200_000)).toMatch(/₦\s?2,000/));
  it("catches crypto vocabulary", () => {
    expect(findForbiddenTerms("Support Tobi and own a piece of the release")).toEqual([]);
    expect(findForbiddenTerms("Mint this NFT, no gas fee!")).toEqual(expect.arrayContaining(["nft", "mint", "gas fee"]));
  });
});
