import type { Aspect, PlannedShot, SectionKind, ShotType, SongAnalysis, SongSection, VideoFormat } from "./types";

export const TEASER_SECONDS = 30;
/** Generation models cap a single clip; longer shots are split. */
export const MAX_SHOT_SECONDS = 8;
export const MIN_SHOT_SECONDS = 2;

export interface PlanOptions {
  format: VideoFormat;
  bars?: number;
}

/** Choose the strongest 30 seconds: the first chorus with a little run-up, else the start. */
export function teaserWindow(song: SongAnalysis): { start: number; end: number } {
  const length = Math.min(TEASER_SECONDS, song.durationSeconds);
  const chorus = song.sections.find((s) => s.kind === "chorus");
  if (!chorus) return { start: 0, end: length };
  const runUp = Math.min(4, chorus.startSeconds);
  const start = Math.max(0, Math.min(chorus.startSeconds - runUp, song.durationSeconds - length));
  return { start, end: start + length };
}

const CYCLE: Record<SectionKind, ShotType[]> = {
  intro: ["wide", "b-roll"],
  verse: ["performance", "close-up", "b-roll", "performance"],
  "pre-chorus": ["close-up", "performance"],
  chorus: ["dance", "performance", "wide", "dance"],
  bridge: ["close-up", "b-roll", "lyric"],
  outro: ["wide", "performance"],
};

function sectionAt(sections: SongSection[], t: number): SectionKind {
  return sections.find((s) => t >= s.startSeconds && t < s.endSeconds)?.kind ?? "verse";
}

/**
 * Cut the song into shots that land on the bar. One bar = 4 beats; a shot is 2 bars by default,
 * which at 100–130 bpm gives 3.7–4.8 s shots. High-energy choruses cut every bar.
 */
export function planShots(song: SongAnalysis, opts: PlanOptions): PlannedShot[] {
  if (song.bpm < 40 || song.bpm > 220) throw new RangeError(`Unusable tempo ${song.bpm} bpm`);
  const window = opts.format === "teaser" ? teaserWindow(song) : { start: 0, end: song.durationSeconds };
  const barSeconds = (60 / song.bpm) * 4;
  const shots: PlannedShot[] = [];
  const counters: Partial<Record<SectionKind, number>> = {};
  let t = window.start;
  let index = 0;
  while (t < window.end - 0.05) {
    const section = sectionAt(song.sections, t);
    const bars = opts.bars ?? (section === "chorus" && song.energy === "high" ? 1 : 2);
    let duration = Math.min(bars * barSeconds, window.end - t);
    while (duration > MAX_SHOT_SECONDS) duration /= 2;
    if (duration < MIN_SHOT_SECONDS && shots.length) {
      // Tail too short for its own shot: extend the previous one.
      shots[shots.length - 1]!.durationSeconds += duration;
      break;
    }
    const cycle = CYCLE[section];
    const n = counters[section] ?? 0;
    counters[section] = n + 1;
    const type = cycle[n % cycle.length]!;
    shots.push({
      id: `shot_${index + 1}`,
      index,
      section,
      startSeconds: round(t),
      durationSeconds: round(duration),
      type,
      featuresArtist: type !== "b-roll" && type !== "lyric",
    });
    t += duration;
    index++;
  }
  return shots;
}

export function totalSeconds(shots: readonly PlannedShot[]): number {
  return round(shots.reduce((s, x) => s + x.durationSeconds, 0));
}

export function aspectFor(format: VideoFormat): Aspect {
  return format === "teaser" ? "9:16" : "16:9";
}

function round(n: number): number {
  return Math.round(n * 100) / 100;
}
