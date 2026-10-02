import { createHash } from "node:crypto";
import type { FastifyInstance } from "fastify";
import {
  aiLabel,
  aspectFor,
  checkTreatmentText,
  coverage,
  planShots,
  quoteVideo,
  sha256Hex,
  type ConsentTerms,
  type DirectorBrief,
  type Treatment,
} from "@livebic/core";
import { z } from "zod";
import { requireUser, type AppContext } from "../context";
import { badRequest, conflict, forbidden, HttpError, notFound } from "../errors";
import { newId } from "../ids";
import type { Artist, Release, Take, User, VideoProject } from "../store";
import { signMediaUrl } from "./content";

const IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp"];
const MAX_PHOTO_BYTES = 10 * 1024 * 1024;
const MIN_PHOTOS = 3;
const MAX_PHOTOS = 10;

function monthKey(d: Date): string {
  return d.toISOString().slice(0, 7);
}

function own(ctx: AppContext, user: User): Artist {
  const artist = user.artistId ? ctx.store.artists.get(user.artistId) : undefined;
  if (!artist) throw forbidden("Only artists can use the video studio");
  return artist;
}

function ownProject(ctx: AppContext, user: User, id: string): { artist: Artist; project: VideoProject; release: Release } {
  const artist = own(ctx, user);
  const project = ctx.store.videoProjects.get(id);
  if (!project || project.artistId !== artist.id) throw notFound("Video project");
  const release = ctx.store.releases.get(project.releaseId);
  if (!release) throw notFound("Release");
  return { artist, project, release };
}

function briefFor(ctx: AppContext, artist: Artist, release: Release, project: VideoProject): DirectorBrief {
  return {
    songTitle: release.title,
    artistName: artist.displayName,
    lyrics: release.lyrics,
    notes: project.notes.join("; "),
    song: project.analysis,
    shots: project.shots,
    castMode: project.castMode,
  };
}

function guard(treatment: Treatment, notes: string[] = []): void {
  const hits = checkTreatmentText([treatment.concept, treatment.look, ...treatment.locations, ...Object.values(treatment.directions), ...notes]);
  if (hits.length) {
    throw new HttpError(422, "blocked_content", `This can't go in a video: ${hits.map((h) => `${h.category} ("${h.text}")`).join(", ")}`);
  }
}

export function projectView(ctx: AppContext, p: VideoProject) {
  const now = ctx.now().getTime();
  const raised = ctx.store.fundingRaised(p.id);
  const takeView = (t: Take) => ({
    id: t.id,
    shotId: t.shotId,
    quality: t.quality,
    url: signMediaUrl(ctx, t.objectKey, now),
    thumbnailUrl: t.thumbnailKey ? signMediaUrl(ctx, t.thumbnailKey, now) : null,
  });
  return {
    id: p.id,
    releaseId: p.releaseId,
    format: p.format,
    aspect: p.aspect,
    castMode: p.castMode,
    status: p.status,
    song: { durationSeconds: p.analysis.durationSeconds, bpm: p.analysis.bpm, energy: p.analysis.energy },
    shots: p.shots.map((s) => ({ ...s, direction: p.treatment.directions[s.id] ?? "", pickedTakeId: p.picks[s.id] ?? null })),
    treatment: { title: p.treatment.title, concept: p.treatment.concept, look: p.treatment.look, locations: p.treatment.locations, palette: p.treatment.palette },
    notes: p.notes,
    consent: p.consentId ? { id: p.consentId, revoked: ctx.store.consents.get(p.consentId)?.revokedAt !== null } : null,
    takes: p.takes.map(takeView),
    quote: p.quote,
    funding: p.funding ? { goalKobo: p.funding.goalKobo, raisedKobo: raised, open: p.funding.closedAt === null } : null,
    charged: p.charged,
    exports: p.exports.map((e) => ({ id: e.id, aspect: e.aspect, label: e.label, shareUrl: e.shareUrl, downloadUrl: signMediaUrl(ctx, e.objectKey, now), createdAt: e.createdAt })),
    createdAt: p.createdAt,
    updatedAt: p.updatedAt,
  };
}

/** What fans see on the artist page: open campaigns with progress. */
export function publicFunding(ctx: AppContext, artistId: string) {
  return [...ctx.store.videoProjects.values()]
    .filter((p) => p.artistId === artistId && p.funding && p.funding.closedAt === null)
    .map((p) => {
      const release = ctx.store.releases.get(p.releaseId);
      return {
        projectId: p.id,
        releaseId: p.releaseId,
        title: release?.title ?? "",
        format: p.format,
        goalKobo: p.funding!.goalKobo,
        raisedKobo: ctx.store.fundingRaised(p.id),
        backers: new Set(ctx.store.paidOrders((o) => o.kind === "fund" && o.videoProjectId === p.id).map((o) => o.fanId)).size,
      };
    });
}

export async function registerVideo(app: FastifyInstance, ctx: AppContext) {
  const { store, partners, config } = ctx;

  app.get("/v1/video/projects", async (req) => {
    const artist = own(ctx, requireUser(ctx, req, "artist"));
    return [...store.videoProjects.values()].filter((p) => p.artistId === artist.id).map((p) => projectView(ctx, p));
  });

  app.post("/v1/video/projects", async (req, reply) => {
    const user = requireUser(ctx, req, "artist");
    const artist = own(ctx, user);
    if (artist.verification !== "verified") throw forbidden("Get verified before making videos");
    const body = z
      .object({
        releaseId: z.string(),
        format: z.enum(["teaser", "full"]).default("teaser"),
        castMode: z.enum(["likeness", "character"]).default("likeness"),
        notes: z.string().max(2000).optional(),
      })
      .parse(req.body);
    const release = store.releases.get(body.releaseId);
    if (!release || release.artistId !== artist.id || release.status === "removed") throw notFound("Release");
    if (!release.audio) throw badRequest("audio_required", "Upload the song before planning its video");

    const audio = await partners.storage.get(release.audio.objectKey);
    if (!audio) throw notFound("Audio file");
    const analysis = await partners.audioAnalyzer.analyze(audio);
    const shots = planShots(analysis, { format: body.format });
    const now = ctx.now();
    const project: VideoProject = {
      id: newId("vid"),
      artistId: artist.id,
      releaseId: release.id,
      format: body.format,
      aspect: aspectFor(body.format),
      castMode: body.castMode,
      status: "treatment",
      analysis,
      shots,
      treatment: { title: "", concept: "", look: "", locations: [], palette: [], directions: {} },
      notes: body.notes ? [body.notes] : [],
      lockedShots: [],
      consentId: null,
      takes: [],
      picks: {},
      quote: quoteVideo(shots, config.videoPricing),
      funding: null,
      charged: null,
      exports: [],
      createdAt: now,
      updatedAt: now,
    };
    if (body.notes) guard(project.treatment, [body.notes]);
    project.treatment = await ctx.director.write(briefFor(ctx, artist, release, project));
    guard(project.treatment);
    store.videoProjects.set(project.id, project);
    return reply.code(201).send(projectView(ctx, project));
  });

  app.get<{ Params: { id: string } }>("/v1/video/projects/:id", async (req) => {
    const { project } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    return projectView(ctx, project);
  });

  /** Artist steers the treatment in their own words, or edits one shot's direction directly. */
  app.post<{ Params: { id: string } }>("/v1/video/projects/:id/revise", async (req) => {
    const { artist, project, release } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    if (project.status !== "treatment" && project.status !== "casting") throw conflict("locked", "The treatment is locked once drafts are generated");
    const body = z
      .object({ notes: z.string().min(2).max(2000).optional(), shotId: z.string().optional(), direction: z.string().min(5).max(1000).optional() })
      .parse(req.body);
    if (body.notes) {
      guard(project.treatment, [body.notes]);
      project.notes.push(body.notes);
      const kept = Object.fromEntries(project.lockedShots.map((id) => [id, project.treatment.directions[id] ?? ""]));
      const revised = await ctx.director.revise(briefFor(ctx, artist, release, project), project.treatment, body.notes);
      project.treatment = { ...revised, directions: { ...revised.directions, ...kept } };
      guard(project.treatment);
    }
    if (body.shotId && body.direction) {
      if (!project.shots.some((s) => s.id === body.shotId)) throw notFound("Shot");
      guard({ ...project.treatment, directions: { [body.shotId]: body.direction } });
      project.treatment.directions[body.shotId] = body.direction;
      if (!project.lockedShots.includes(body.shotId)) project.lockedShots.push(body.shotId);
    }
    project.updatedAt = ctx.now();
    return projectView(ctx, project);
  });

  // Casting: reference photos plus a signed consent record. Required for likeness mode only.
  app.register(async (scope) => {
    scope.addContentTypeParser(IMAGE_TYPES, { parseAs: "buffer", bodyLimit: MAX_PHOTO_BYTES }, (_req, body, done) => done(null, body));
    scope.put<{ Params: { id: string } }>("/v1/video/projects/:id/photos", { bodyLimit: MAX_PHOTO_BYTES }, async (req) => {
      const { project } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
      if (project.consentId) throw conflict("consent_signed", "Photos are locked once consent is signed");
      const body = req.body as Buffer;
      if (!Buffer.isBuffer(body) || body.length === 0) throw badRequest("empty_upload", "Upload a JPEG, PNG or WebP photo");
      const pending = pendingPhotos.get(project.id) ?? [];
      if (pending.length >= MAX_PHOTOS) throw badRequest("too_many_photos", `Up to ${MAX_PHOTOS} photos`);
      const key = `likeness/${project.artistId}/${project.id}/${sha256Hex(body)}`;
      await partners.storage.put(key, body, String(req.headers["content-type"]).split(";")[0]!);
      if (!pending.includes(key)) pending.push(key);
      pendingPhotos.set(project.id, pending);
      project.status = "casting";
      return { photos: pending.length };
    });
  });
  const pendingPhotos = new Map<string, string[]>();

  app.post<{ Params: { id: string } }>("/v1/video/projects/:id/consent", async (req, reply) => {
    const user = requireUser(ctx, req, "artist");
    const { artist, project } = ownProject(ctx, user, req.params.id);
    if (project.castMode !== "likeness") throw badRequest("not_likeness", "Consent is only needed when you appear in the video yourself");
    if (project.consentId) throw conflict("consent_signed", "Already signed");
    const body = z
      .object({
        isSelf: z.literal(true),
        allowedUses: z.array(z.enum(["music-video", "teaser", "social-clip"])).min(1),
        blockedTopics: z.array(z.string().max(100)).max(20).default([]),
      })
      .parse(req.body);
    const photos = pendingPhotos.get(project.id) ?? [];
    if (photos.length < MIN_PHOTOS) throw badRequest("photos_required", `Upload at least ${MIN_PHOTOS} clear photos of yourself first`);

    const terms: ConsentTerms = { ...body, signedAt: ctx.now().toISOString() };
    const record = { consentId: newId("cns"), artistId: artist.id, userId: user.id, projectId: project.id, terms, photos };
    const hash = createHash("sha256").update(JSON.stringify(record)).digest("hex");
    const { txRef } = await partners.chain.recordConsentHash({ consentId: record.consentId, hash });
    store.consents.set(record.consentId, { id: record.consentId, terms, hash, registryTx: txRef, photoKeys: photos, revokedAt: null });
    project.consentId = record.consentId;
    project.status = "casting";
    project.updatedAt = ctx.now();
    pendingPhotos.delete(project.id);
    return reply.code(201).send({ consentId: record.consentId, hash, registryRef: txRef });
  });

  /** Revocation deletes the reference set at the partner and stops new generations (spec: step 6). */
  app.delete<{ Params: { id: string } }>("/v1/video/projects/:id/consent", async (req, reply) => {
    const { artist, project } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    const consent = project.consentId ? store.consents.get(project.consentId) : undefined;
    if (!consent) throw notFound("Consent");
    consent.revokedAt = ctx.now();
    await partners.video.revokeIdentity(artist.id);
    if (project.status !== "done") project.status = "cancelled";
    return reply.code(204).send();
  });

  app.post<{ Params: { id: string } }>("/v1/video/projects/:id/funding", async (req, reply) => {
    const { project } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    const { goalKobo } = z.object({ goalKobo: z.number().int() }).parse(req.body);
    if (goalKobo < config.videoPricing.minFundingGoalKobo) throw badRequest("goal_too_small", "Set a bigger goal");
    if (goalKobo > project.quote.totalKobo) throw badRequest("goal_too_big", "The goal can't be more than the video costs");
    if (project.funding?.closedAt === null) throw conflict("funding_open", "A campaign is already running");
    project.funding = { goalKobo, openedAt: ctx.now(), closedAt: null };
    project.updatedAt = ctx.now();
    return reply.code(201).send(projectView(ctx, project));
  });

  function ensureCanGenerate(project: VideoProject): void {
    if (project.castMode === "likeness") {
      const consent = project.consentId ? store.consents.get(project.consentId) : undefined;
      if (!consent) throw new HttpError(403, "consent_required", "Upload your photos and sign the consent form first");
      if (consent.revokedAt) throw new HttpError(403, "consent_revoked", "Consent was withdrawn for this project");
    }
    if (project.status === "cancelled") throw conflict("cancelled", "This project was cancelled");
  }

  async function references(project: VideoProject): Promise<{ body: Buffer; contentType: string }[]> {
    const consent = project.consentId ? store.consents.get(project.consentId) : undefined;
    if (!consent) return [];
    const out = [];
    for (const key of consent.photoKeys) {
      const obj = await partners.storage.get(key);
      if (obj) out.push(obj);
    }
    return out;
  }

  async function audioSlice(release: Release, offset: number): Promise<{ body: Buffer; contentType: string; offsetSeconds: number } | null> {
    if (!release.audio) return null;
    const obj = await partners.storage.get(release.audio.objectKey);
    // The real renderer trims the slice; the sandbox hands the whole file with an offset.
    return obj ? { ...obj, offsetSeconds: offset } : null;
  }

  async function storeTake(project: VideoProject, shotId: string, seed: number, quality: Take["quality"], job: Awaited<ReturnType<typeof partners.video.generateClip>>): Promise<Take> {
    if (job.status !== "done" || !job.output) throw new HttpError(502, "generation_failed", job.error ?? "The video partner did not return a clip");
    const takeId = newId("take");
    const objectKey = `video/${project.id}/${quality}/${takeId}.mp4`;
    await partners.storage.put(objectKey, job.output.body, job.output.contentType);
    let thumbnailKey: string | null = null;
    if (job.output.thumbnail) {
      thumbnailKey = `video/${project.id}/${quality}/${takeId}.thumb`;
      await partners.storage.put(thumbnailKey, job.output.thumbnail.body, job.output.thumbnail.contentType);
    }
    const take: Take = { id: takeId, shotId, seed, quality, objectKey, thumbnailKey, jobId: job.jobId };
    project.takes.push(take);
    return take;
  }

  /** Charge for the whole project once, before any generation, so a video never runs at a loss. */
  function charge(user: User, artist: Artist, project: VideoProject): void {
    if (project.charged) return;
    const month = monthKey(ctx.now());
    const teasersUsed = [...store.videoProjects.values()].filter(
      (p) => p.artistId === artist.id && p.format === "teaser" && p.charged?.tier === "starter" && monthKey(p.createdAt) === month,
    ).length;
    const cov = coverage({
      format: project.format,
      quoteKobo: project.quote.totalKobo,
      starterTeasersUsedThisMonth: teasersUsed,
      fundingRaisedKobo: store.fundingRaised(project.id),
      artistBalanceKobo: store.balance(user.id),
      pricing: config.videoPricing,
    });
    if (!cov.covered) {
      throw new HttpError(402, "payment_required", `This video costs more than your fan funding and balance cover. Short by ₦${Math.ceil(cov.shortfallKobo / 100).toLocaleString("en-NG")}.`);
    }
    if (cov.fromBalanceKobo > 0) {
      store.ledger.push({ id: newId("led"), userId: user.id, amountKobo: -cov.fromBalanceKobo, reason: "video", orderId: null, payoutId: null, at: ctx.now() });
    }
    if (project.funding) project.funding.closedAt = ctx.now();
    project.charged = { tier: cov.tier, fromBalanceKobo: cov.fromBalanceKobo, fromFundingKobo: cov.fromFundingKobo };
  }

  app.post<{ Params: { id: string } }>("/v1/video/projects/:id/drafts", async (req) => {
    const user = requireUser(ctx, req, "artist");
    const { artist, project, release } = ownProject(ctx, user, req.params.id);
    ensureCanGenerate(project);
    if (project.takes.some((t) => t.quality === "draft")) throw conflict("drafts_exist", "Drafts were already generated");
    guard(project.treatment);
    charge(user, artist, project);
    project.status = "drafting";
    const refs = await references(project);
    for (const shot of project.shots) {
      const prompt = project.treatment.directions[shot.id] ?? "";
      const audio = await audioSlice(release, shot.startSeconds);
      for (let i = 0; i < config.videoPricing.takesPerShot; i++) {
        const seed = parseInt(sha256Hex(`${project.id}:${shot.id}:${i}`).slice(0, 8), 16);
        const job = await partners.video.generateClip({
          prompt,
          durationSeconds: shot.durationSeconds,
          aspect: project.aspect,
          quality: "draft",
          referenceImages: shot.featuresArtist ? refs : [],
          audio,
          seed,
        });
        await storeTake(project, shot.id, seed, "draft", job);
      }
    }
    project.status = "picking";
    project.updatedAt = ctx.now();
    return projectView(ctx, project);
  });

  app.post<{ Params: { id: string; shotId: string } }>("/v1/video/projects/:id/shots/:shotId/pick", async (req) => {
    const { project } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    const { takeId } = z.object({ takeId: z.string() }).parse(req.body);
    const take = project.takes.find((t) => t.id === takeId && t.shotId === req.params.shotId && t.quality === "draft");
    if (!take) throw notFound("Take");
    if (project.status !== "picking") throw conflict("not_picking", "Picks are closed");
    project.picks[req.params.shotId] = takeId;
    project.updatedAt = ctx.now();
    return projectView(ctx, project);
  });

  app.post<{ Params: { id: string } }>("/v1/video/projects/:id/finalize", async (req) => {
    const { artist, project, release } = ownProject(ctx, requireUser(ctx, req, "artist"), req.params.id);
    ensureCanGenerate(project);
    if (project.status !== "picking") throw conflict("not_ready", "Pick a take for every shot first");
    const missing = project.shots.filter((s) => !project.picks[s.id]);
    if (missing.length) throw badRequest("picks_missing", `Pick a take for ${missing.length} more shot${missing.length === 1 ? "" : "s"}`);
    project.status = "rendering";
    const refs = await references(project);
    const label = aiLabel(artist.displayName);
    const clips = [];
    for (const shot of project.shots) {
      const draft = project.takes.find((t) => t.id === project.picks[shot.id])!;
      const audio = await audioSlice(release, shot.startSeconds);
      const job = await partners.video.generateClip({
        prompt: project.treatment.directions[shot.id] ?? "",
        durationSeconds: shot.durationSeconds,
        aspect: project.aspect,
        quality: "final",
        referenceImages: shot.featuresArtist ? refs : [],
        audio,
        seed: draft.seed,
      });
      const take = await storeTake(project, shot.id, draft.seed, "final", job);
      const obj = (await partners.storage.get(take.objectKey))!;
      clips.push({ ...obj, startSeconds: shot.startSeconds, durationSeconds: shot.durationSeconds });
    }
    const first = project.shots[0]!;
    const song = await audioSlice(release, first.startSeconds);
    if (!song) throw notFound("Audio file");
    const shareUrl = `${config.webUrl}/a/${artist.handle}#${release.id}`;
    const rendered = await partners.renderer.assemble({
      clips,
      audio: { ...song, durationSeconds: project.shots.reduce((s, x) => s + x.durationSeconds, 0) },
      aspect: project.aspect,
      watermarkText: `livebic.com/@${artist.handle}`,
      label,
    });
    const exportId = newId("exp");
    const objectKey = `video/${project.id}/export/${exportId}.mp4`;
    await partners.storage.put(objectKey, rendered.body, rendered.contentType);
    project.exports.push({ id: exportId, aspect: project.aspect, objectKey, contentType: rendered.contentType, label, shareUrl, createdAt: ctx.now() });
    project.status = "done";
    project.updatedAt = ctx.now();
    return projectView(ctx, project);
  });
}
