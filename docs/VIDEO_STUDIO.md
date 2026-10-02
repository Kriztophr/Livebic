# Video Studio — AI music video direction

An AI director plans and renders a music video for a release, so an artist who cannot afford a
video shoot still has something to post. It sits inside the artist Studio (`/studio/video`) and
reuses the existing payment flow: fans fund videos the same way they tip.

## How it works

1. **Listen.** The artist picks a published release. The audio analyser returns tempo, energy and
   song structure (intro, verse, chorus, bridge, outro).
2. **Plan.** `planShots` cuts the song into shots that land on the bar (2 bars per shot; 1 bar in a
   high-energy chorus; never shorter than 2 s or longer than 8 s, which is what the generation
   models accept). A teaser is the 30 seconds around the first chorus in 9:16; a full video is the
   whole song in 16:9.
3. **Write the treatment.** The director writes a concept, look, locations, palette and one
   direction per shot. The artist steers it in plain language ("colder colours, fewer rooftops")
   or edits any shot's direction by hand; hand-edited shots are locked and survive later revisions.
4. **Cast.** Likeness mode: the artist uploads at least 3 photos of themselves and signs a consent
   record (its hash goes to the chain registry). Character mode: a stylised performer stands in,
   no photos needed.
5. **Draft, then pick.** Two cheap low-resolution takes are generated per shot. The artist picks
   one per shot. Only picked takes are re-rendered in full quality, with the same seed, so the
   final matches the draft.
6. **Render and export.** Clips are joined over the song, watermarked with the artist's Livebic
   link and labelled "AI-generated video, approved by <artist>". The export page has Download and
   Copy Livebic link, for posting to TikTok, Reels, Shorts and YouTube.

## Who pays

Generation cost is passed through inside the price so a video never runs at a loss. The project is
charged once, before any generation. Coverage order (`coverage()` in `packages/core/src/video/pricing.ts`):

| Tier | Rule |
| --- | --- |
| Starter | The first teaser each calendar month is free. |
| Fan-funded | The artist opens a campaign with a goal; fans back it from the artist page with the usual fixed amounts (`kind: "fund"` orders). Backers are supporters, their money goes through the normal split, and they are credited in the video. What they raised is applied first. |
| Studio | Whatever remains is taken from the artist's Livebic balance. If balance and funding together fall short, generation returns 402 with the shortfall. |

Prices in `DEFAULT_VIDEO_PRICING` are placeholders (₦40/s per draft take, ₦300/s final) until
Higgsfield's credit prices are costed against real videos. A 30 s teaser quotes at ₦11,400.

## Guardrails

- Only verified artists can start a video. Only the artist's own likeness can be used, and only
  after the signed consent; there is no way to upload photos of anyone else.
- Blocked categories are checked on every note and direction before generation: political
  endorsement, sexual content, health or financial claims, impersonation of other people
  (`checkDirection` in `packages/core/src/video/guardrails.ts`). The generation partner's own
  moderation runs after it.
- Every export carries the label and watermark.
- Withdrawing consent deletes the reference set at the partner and stops the project.
- Videos emit no ranking events. A paid video does not move an artist up the feeds.

## Code

| Piece | Where |
| --- | --- |
| Shot planning, pricing, coverage, guardrails, template director | `packages/core/src/video/` |
| Claude-backed director (structured output, falls back to the template on refusal) | `apps/api/src/partners/claude-director.ts`; on when `ANTHROPIC_API_KEY` is set |
| Partner interfaces: `VideoGenerator`, `AudioAnalyzer`, `VideoRenderer` | `apps/api/src/partners/types.ts`; sandbox stand-ins in `sandbox.ts` |
| Project lifecycle API | `apps/api/src/modules/video.ts` |
| Fan funding orders | `apps/api/src/modules/payments.ts` (`kind: "fund"`) |
| Studio UI | `apps/web/app/studio/video/page.tsx`; "Fund the video" card on the artist page |

## Still to do

- **Real generation.** Implement `VideoGenerator` against Higgsfield. Seedance 2.5 fits: it takes
  the song and reference photos, makes clips up to 30 s, and has a 480p draft mode that can be
  finalised at 1080p within seven days, which is exactly the draft-then-pick flow. Wan 2.7 is the
  alternative for character consistency with synced audio.
- **Real audio analysis** (tempo and structure). The sandbox analyser derives a plausible structure
  from the file hash.
- **Real rendering**: an ffmpeg job that trims the audio slice, joins clips, burns in the watermark
  and label, and produces both aspects.
- **Async jobs.** Generation takes minutes per clip in production. The API interfaces return jobs;
  the module currently awaits them inline. Move drafting and finalising to a queue and poll
  `getJob` from the Studio page.
- **Lip-sync quality** across a full 3-minute song is still hard for these models. Launch with
  teasers, visualisers and performance clips; treat full videos as a Studio-tier product once
  real output has been reviewed.
- **Cost the pricing table** against Higgsfield's credits and confirm its terms allow commercial
  use of generated video.
- **Backer credits** in the rendered video (names from `fund` orders) and a "Director's Cut" tier
  where a human director polishes the AI edit.
