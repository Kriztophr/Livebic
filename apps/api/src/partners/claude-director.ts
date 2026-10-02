import Anthropic from "@anthropic-ai/sdk";
import { zodOutputFormat } from "@anthropic-ai/sdk/helpers/zod";
import { templateDirector, type Director, type DirectorBrief, type Treatment } from "@livebic/core";
import { z } from "zod/v4";

const MODEL = "claude-opus-5-5";

const TreatmentSchema = z.object({
  title: z.string(),
  concept: z.string(),
  look: z.string(),
  locations: z.array(z.string()),
  palette: z.array(z.string()),
  directions: z.array(z.object({ shotId: z.string(), direction: z.string() })),
});

const SYSTEM = `You are a music video director working with independent Nigerian artists on Livebic.
You write treatments and per-shot directions that a video generation model will render, so each
direction must describe exactly what is on screen: subject, action, framing, camera move, light,
location. Write for a Lagos audience: real places, real street life, no clichés about Africa.
Keep the artist's own notes as the top priority. Directions are plain language, one or two sentences,
and never include political endorsement, sexual content, health or financial claims, or any other
real person's likeness. When castMode is "character", describe a stylised animated performer and
never the artist's real face.`;

function briefText(brief: DirectorBrief): string {
  const shots = brief.shots
    .map((s) => `${s.id}: ${s.section}, ${s.type}, ${s.durationSeconds}s at ${s.startSeconds}s${s.featuresArtist ? "" : " (no artist in shot)"}`)
    .join("\n");
  return [
    `Song: "${brief.songTitle}" by ${brief.artistName}${brief.genre ? ` (${brief.genre})` : ""}.`,
    `Tempo ${brief.song.bpm} bpm, energy ${brief.song.energy}, ${brief.song.durationSeconds}s long.`,
    `Cast mode: ${brief.castMode}.`,
    brief.notes ? `Artist's notes: ${brief.notes}` : "",
    brief.lyrics ? `Lyrics:\n${brief.lyrics.slice(0, 4000)}` : "No lyrics supplied.",
    `Shot list (write one direction per shot id, all of them):\n${shots}`,
  ]
    .filter(Boolean)
    .join("\n\n");
}

function toTreatment(parsed: z.infer<typeof TreatmentSchema>, brief: DirectorBrief, fallback: Treatment): Treatment {
  const directions: Record<string, string> = {};
  for (const d of parsed.directions) directions[d.shotId] = d.direction;
  // Never leave a shot without a direction: fill any the model skipped from the template.
  for (const s of brief.shots) directions[s.id] ??= fallback.directions[s.id] ?? "";
  return { title: parsed.title, concept: parsed.concept, look: parsed.look, locations: parsed.locations, palette: parsed.palette, directions };
}

/** Treatment writer backed by Claude. Falls back to the template director if the request is refused. */
export function claudeDirector(client: Anthropic = new Anthropic()): Director {
  async function ask(brief: DirectorBrief, userText: string): Promise<Treatment> {
    const fallback = await templateDirector.write(brief);
    const response = await client.messages.parse({
      model: MODEL,
      max_tokens: 16000,
      system: [{ type: "text", text: SYSTEM, cache_control: { type: "ephemeral" } }],
      messages: [{ role: "user", content: userText }],
      output_config: { format: zodOutputFormat(TreatmentSchema), effort: "medium" },
    });
    if (response.stop_reason === "refusal" || !response.parsed_output) return fallback;
    return toTreatment(response.parsed_output, brief, fallback);
  }
  return {
    write: (brief) => ask(brief, `${briefText(brief)}\n\nWrite the treatment.`),
    revise: (brief, current, notes) =>
      ask(brief, `${briefText(brief)}\n\nCurrent treatment:\n${JSON.stringify(current, null, 2)}\n\nThe artist asks for this change: "${notes}"\nRevise the treatment, keeping everything they did not ask to change.`),
  };
}
