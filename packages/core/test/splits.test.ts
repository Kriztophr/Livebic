import { describe, expect, it } from "vitest";
import { allocateSale, validateSplits } from "../src";

describe("allocateSale", () => {
  it("takes the platform fee then divides the rest exactly", () => {
    const out = allocateSale(100_001, [
      { payeeId: "artist", role: "creator", bps: 3_333 },
      { payeeId: "producer", role: "collaborator", bps: 3_333 },
      { payeeId: "feature", role: "collaborator", bps: 3_334 },
    ]);
    expect(out.reduce((s, a) => s + a.amount, 0)).toBe(100_001);
    expect(out.find((a) => a.role === "platform")!.amount).toBe(8_000);
  });

  it("rejects shares that do not total 100%", () => {
    expect(() => validateSplits([{ payeeId: "a", role: "creator", bps: 9_000 }])).toThrow(/10000/);
  });

  it("refuses a platform fee of 10% or more", () => {
    expect(() => allocateSale(1000, [{ payeeId: "a", role: "creator", bps: 10_000 }], 1_000)).toThrow();
  });

  it("never produces fractional kobo", () => {
    for (let amount = 0; amount < 500; amount += 7) {
      const out = allocateSale(amount, [
        { payeeId: "a", role: "creator", bps: 7_001 },
        { payeeId: "b", role: "collaborator", bps: 2_999 },
      ]);
      expect(out.every((a) => Number.isInteger(a.amount) && a.amount >= 0)).toBe(true);
      expect(out.reduce((s, a) => s + a.amount, 0)).toBe(amount);
    }
  });
});
