export type VideoFormat = "teaser" | "full";
export type Aspect = "9:16" | "16:9";
export type CastMode = "likeness" | "character";
export type SectionKind = "intro" | "verse" | "pre-chorus" | "chorus" | "bridge" | "outro";

export interface SongSection {
  kind: SectionKind;
  startSeconds: number;
  endSeconds: number;
}

/** What the audio analyser hands the planner. */
export interface SongAnalysis {
  durationSeconds: number;
  bpm: number;
  energy: "low" | "medium" | "high";
  sections: SongSection[];
}

export type ShotType = "performance" | "close-up" | "wide" | "dance" | "b-roll" | "lyric";

export interface PlannedShot {
  id: string;
  index: number;
  section: SectionKind;
  startSeconds: number;
  durationSeconds: number;
  type: ShotType;
  /** True when the artist should appear; false for b-roll and lyric cards. */
  featuresArtist: boolean;
}

export interface Treatment {
  title: string;
  concept: string;
  look: string;
  locations: string[];
  palette: string[];
  /** Per-shot direction, keyed by shot id. Written for the generation model, in plain language. */
  directions: Record<string, string>;
}

export type Quality = "draft" | "final";
