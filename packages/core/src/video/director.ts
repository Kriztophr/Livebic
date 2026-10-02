import type { PlannedShot, SongAnalysis, Treatment } from "./types";

export interface DirectorBrief {
  songTitle: string;
  artistName: string;
  lyrics: string;
  genre?: string;
  /** Artist's own words: "Lagos at night, danfo buses, more dancing in the hook". */
  notes?: string;
  song: SongAnalysis;
  shots: readonly PlannedShot[];
  castMode: "likeness" | "character";
}

/**
 * Writes and revises treatments. The Claude-backed director in the API implements this too.
 * On revise, `brief.notes` already includes every note so far; `latest` is the one just added.
 */
export interface Director {
  write(brief: DirectorBrief): Promise<Treatment>;
  revise(brief: DirectorBrief, current: Treatment, latest: string): Promise<Treatment>;
}

const LOOKS = {
  low: { look: "Soft, handheld, natural light; slow pushes and long holds.", palette: ["warm amber", "deep teal", "dusty rose"] },
  medium: { look: "Clean 35mm feel, steady gimbal moves, golden-hour exteriors.", palette: ["gold", "sky blue", "terracotta"] },
  high: { look: "Punchy, high-contrast, fast whip pans and strobe-lit interiors.", palette: ["electric green", "hot pink", "black"] },
};

const LOCATIONS = ["Lagos Island rooftop at dusk", "Yaba street market", "Third Mainland Bridge at night", "Lekki beach at sunrise", "a Surulere compound courtyard", "a neon-lit Ikeja studio"];

function subjectFor(brief: DirectorBrief): string {
  return brief.castMode === "likeness" ? brief.artistName : `a stylised animated performer standing in for ${brief.artistName}`;
}

function lyricLine(lyrics: string, i: number): string {
  const lines = lyrics.split(/\r?\n/).map((l) => l.trim()).filter((l) => l.length > 3);
  return lines.length ? lines[i % lines.length]! : "";
}

/**
 * Deterministic treatment writer used when no LLM is configured and in tests. Good enough to
 * exercise the whole pipeline; the Claude director writes something an artist would actually want.
 */
export const templateDirector: Director = {
  async write(brief) {
    const base = LOOKS[brief.song.energy];
    const subject = subjectFor(brief);
    const locations = LOCATIONS.slice(0, 3);
    const directions: Record<string, string> = {};
    for (const shot of brief.shots) {
      const loc = locations[shot.index % locations.length]!;
      switch (shot.type) {
        case "performance":
          directions[shot.id] = `${subject} performs the ${shot.section} to camera at ${loc}, medium shot, slow push in.`;
          break;
        case "close-up":
          directions[shot.id] = `Tight close-up of ${subject} singing, shallow focus, ${loc} lights blurred behind.`;
          break;
        case "wide":
          directions[shot.id] = `Wide establishing shot of ${loc}, ${subject} small in frame, slow drone-style drift.`;
          break;
        case "dance":
          directions[shot.id] = `${subject} dances with a small crew at ${loc}, energetic, camera orbits on the beat.`;
          break;
        case "b-roll":
          directions[shot.id] = `B-roll of ${loc}: traffic light trails, street vendors, textures, no people in focus.`;
          break;
        case "lyric":
          directions[shot.id] = `Bold kinetic typography of the line "${lyricLine(brief.lyrics, shot.index)}" over a textured backdrop.`;
          break;
      }
    }
    return {
      title: `${brief.songTitle} — official visual`,
      concept: `${brief.artistName} moves through one night in Lagos as the song builds; every chorus brings more people and more light.${brief.notes ? ` Artist's notes: ${brief.notes}.` : ""}`,
      look: base.look,
      locations,
      palette: base.palette,
      directions,
    };
  },
  async revise(brief, current) {
    const written = await this.write(brief);
    return { ...current, concept: written.concept, directions: { ...current.directions, ...written.directions } };
  },
};
