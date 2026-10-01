import { describe, expect, it } from "vitest";
import {
  DEFAULT_WEIGHTS,
  RulesRegistry,
  applyDiversityCap,
  computeFeeds,
  describeFeeds,
  explain,
  findForbiddenTerms,
  followingFeed,
  type EngagementEvent,
  type RankActor,
  type RankArtist,
  type RankItem,
} from "../src";

const NOW = new Date("2026-10-01T12:00:00Z");
const daysAgo = (n: number) => new Date(NOW.getTime() - n * 86_400_000);
let seq = 0;
const ev = (p: Omit<EngagementEvent, "id">): EngagementEvent => ({ id: `e${++seq}`, ...p });

function world() {
  const actors = new Map<string, RankActor>();
  const fan = (id: string, verified = true, age = 100, artistId: string | null = null) =>
    actors.set(id, { id, verified, createdAt: daysAgo(age), artistId });
  const artists = new Map<string, RankArtist>([
    ["old", { id: "old", createdAt: daysAgo(400) }],
    ["new", { id: "new", createdAt: daysAgo(30) }],
  ]);
  const items: RankItem[] = [
    { id: "old-1", artistId: "old", publishedAt: daysAgo(3) },
    { id: "new-1", artistId: "new", publishedAt: daysAgo(2) },
  ];
  return { actors, artists, items, fan };
}

const run = (w: ReturnType<typeof world>, events: EngagementEvent[]) =>
  computeFeeds({ ...w, events, weights: DEFAULT_WEIGHTS, rulesVersion: 1, now: NOW });

describe("ranking engine", () => {
  it("weights one verified supporter above many anonymous plays", () => {
    const w = world();
    w.fan("payer");
    const events = [ev({ type: "tip", actorId: "payer", artistId: "old", itemId: "old-1", at: daysAgo(1), amountKobo: 100_000 })];
    for (let i = 0; i < 40; i++) {
      w.fan(`anon${i}`, false, 1);
      events.push(ev({ type: "play", actorId: `anon${i}`, artistId: "new", itemId: "new-1", at: daysAgo(1) }));
    }
    const out = run(w, events);
    const ms = out.snapshots["most-supported"].entries;
    expect(ms.map((e) => e.itemId)).toEqual(["old-1"]);
    const rising = out.snapshots.rising.entries.find((e) => e.itemId === "new-1")!;
    expect(rising.score).toBeLessThan(ms[0]!.score);
  });

  it("decays signals with a 7-day half-life", () => {
    const w = world();
    w.fan("a");
    w.fan("b");
    const out = run(w, [
      ev({ type: "tip", actorId: "a", artistId: "old", itemId: "old-1", at: NOW, amountKobo: 100_000 }),
      ev({ type: "tip", actorId: "b", artistId: "new", itemId: "new-1", at: daysAgo(7), amountKobo: 100_000 }),
    ]);
    const [fresh, week] = out.snapshots["most-supported"].entries;
    expect(fresh!.itemId).toBe("old-1");
    expect(week!.score / fresh!.score).toBeCloseTo(0.5, 5);
  });

  it("excludes self-support and circular tipping", () => {
    const w = world();
    w.fan("oldOwner", true, 400, "old");
    w.fan("newOwner", true, 30, "new");
    const out = run(w, [
      ev({ type: "tip", actorId: "oldOwner", artistId: "old", itemId: "old-1", at: daysAgo(1), amountKobo: 100_000 }),
      ev({ type: "tip", actorId: "oldOwner", artistId: "new", itemId: "new-1", at: daysAgo(1), amountKobo: 100_000 }),
      ev({ type: "tip", actorId: "newOwner", artistId: "old", itemId: "old-1", at: daysAgo(1), amountKobo: 100_000 }),
    ]);
    expect(out.excludedEventIds).toHaveLength(3);
    expect(out.snapshots["most-supported"].entries).toEqual([]);
  });

  it("discounts unverified and brand-new accounts", () => {
    const w = world();
    w.fan("good");
    w.fan("fresh", false, 1);
    const out = run(w, [
      ev({ type: "tip", actorId: "good", artistId: "old", itemId: "old-1", at: NOW, amountKobo: 100_000 }),
      ev({ type: "tip", actorId: "fresh", artistId: "new", itemId: "new-1", at: NOW, amountKobo: 100_000 }),
    ]);
    const [a, b] = out.snapshots["most-supported"].entries;
    expect(b!.score / a!.score).toBeCloseTo(DEFAULT_WEIGHTS.discounts.unverified * DEFAULT_WEIGHTS.discounts.newAccount, 5);
  });

  it("limits Rising to artists under 90 days old", () => {
    const w = world();
    w.fan("a");
    const out = run(w, [
      ev({ type: "tip", actorId: "a", artistId: "old", itemId: "old-1", at: daysAgo(1), amountKobo: 100_000 }),
      ev({ type: "tip", actorId: "a", artistId: "new", itemId: "new-1", at: daysAgo(1), amountKobo: 100_000 }),
    ]);
    expect(out.snapshots.rising.entries.map((e) => e.artistId)).toEqual(["new"]);
    expect(explain(out.snapshots.rising.entries[0]!).map((f) => f.key)).toContain("supporter_growth");
  });

  it("credits artist-level tips to the latest item and flags velocity spikes", () => {
    const w = world();
    const events: EngagementEvent[] = [];
    for (let i = 0; i < 25; i++) {
      w.fan(`f${i}`);
      events.push(ev({ type: "tip", actorId: `f${i}`, artistId: "new", itemId: null, at: new Date(NOW.getTime() - 60_000), amountKobo: 50_000 }));
    }
    const out = run(w, events);
    expect(out.snapshots["most-supported"].entries[0]!.itemId).toBe("new-1");
    expect(out.flags).toEqual([expect.objectContaining({ kind: "velocity_spike", itemId: "new-1", eventsInWindow: 25 })]);
  });

  it("builds a chronological following feed", () => {
    const w = world();
    const out = run(w, []);
    expect(out.snapshots.newest.entries.map((e) => e.itemId)).toEqual(["new-1", "old-1"]);
    expect(followingFeed(out.snapshots.newest, new Set(["old"])).entries.map((e) => e.itemId)).toEqual(["old-1"]);
  });
});

describe("diversity cap", () => {
  it("keeps any artist to two of the top slots", () => {
    const sorted = ["a", "a", "a", "b", "a", "c"].map((artistId, i) => ({ artistId, i }));
    const capped = applyDiversityCap(sorted, 4, 2);
    expect(capped.slice(0, 4).map((e) => e.artistId)).toEqual(["a", "a", "b", "c"]);
    expect(capped).toHaveLength(6);
  });
});

describe("rules registry", () => {
  it("publishes changes in the changelog before they take effect", () => {
    const reg = new RulesRegistry();
    const change = { id: "chg_2", author: "admin", reason: "Reward unique supporters more", createdAt: NOW.toISOString() };
    const weights = { ...DEFAULT_WEIGHTS, paid: { ...DEFAULT_WEIGHTS.paid, perUniqueSupporter: 4 } };
    expect(() => reg.propose({ weights, summary: "x", change, effectiveFrom: new Date(NOW.getTime() + 3_600_000), now: NOW })).toThrow(/24 hours/);
    const v2 = reg.propose({ weights, summary: "x", change, effectiveFrom: new Date(NOW.getTime() + 2 * 86_400_000), now: NOW });
    expect(reg.changelog()[0]!.version).toBe(2);
    expect(reg.active(NOW).version).toBe(1);
    expect(reg.active(new Date(v2.effectiveFrom)).version).toBe(2);
  });

  it("refuses weights where a free action outweighs a paid one", () => {
    const reg = new RulesRegistry();
    const change = { id: "c", author: "admin", reason: "r", createdAt: NOW.toISOString() };
    const weights = { ...DEFAULT_WEIGHTS, free: { ...DEFAULT_WEIGHTS.free, play: 10 } };
    expect(() => reg.propose({ weights, summary: "x", change, effectiveFrom: new Date(NOW.getTime() + 2 * 86_400_000), now: NOW })).toThrow(/paid/);
  });

  it("describes the rules in plain language", () => {
    const text = JSON.stringify(describeFeeds(DEFAULT_WEIGHTS));
    expect(findForbiddenTerms(text)).toEqual([]);
  });
});
