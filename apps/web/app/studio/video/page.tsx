"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { formatNaira, naira } from "@livebic/core/money";
import { api } from "../../../lib/api";
import { useMe } from "../../../lib/useMe";

interface Shot {
  id: string;
  index: number;
  section: string;
  startSeconds: number;
  durationSeconds: number;
  type: string;
  featuresArtist: boolean;
  direction: string;
  pickedTakeId: string | null;
}

interface Take {
  id: string;
  shotId: string;
  quality: "draft" | "final";
  url: string;
  thumbnailUrl: string | null;
}

interface Project {
  id: string;
  releaseId: string;
  format: "teaser" | "full";
  aspect: string;
  castMode: "likeness" | "character";
  status: string;
  song: { durationSeconds: number; bpm: number; energy: string };
  shots: Shot[];
  treatment: { title: string; concept: string; look: string; locations: string[]; palette: string[] };
  notes: string[];
  consent: { id: string; revoked: boolean } | null;
  takes: Take[];
  quote: { draftKobo: number; finalKobo: number; totalKobo: number };
  funding: { goalKobo: number; raisedKobo: number; open: boolean } | null;
  charged: { tier: string; fromBalanceKobo: number; fromFundingKobo: number } | null;
  exports: { id: string; aspect: string; label: string; shareUrl: string; downloadUrl: string }[];
}

interface Release {
  id: string;
  title: string;
}

const STATUS_LABEL: Record<string, string> = {
  treatment: "Treatment",
  casting: "Casting",
  drafting: "Generating drafts",
  picking: "Pick your takes",
  rendering: "Rendering",
  done: "Done",
  cancelled: "Cancelled",
};

export default function VideoStudio() {
  const { me, loading } = useMe();
  const [projects, setProjects] = useState<Project[]>([]);
  const [releases, setReleases] = useState<Release[]>([]);
  const [open, setOpen] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const list = await api<Project[]>("/v1/video/projects");
    setProjects(list);
    if (me?.artist) {
      const page = await api<{ releases: Release[] }>(`/v1/artists/${me.artist.handle}`);
      setReleases(page.releases);
    }
  }

  useEffect(() => {
    if (me?.artist) load().catch((e) => setError((e as Error).message));
  }, [me?.artist?.id]);

  if (loading) return <p className="muted">Loading…</p>;
  if (!me?.artist) return <p>The video studio is for artists. <Link href="/signin">Create an artist account</Link>.</p>;
  if (me.artist.verification !== "verified") return <p>Get verified in <Link href="/studio">Studio</Link> before making videos.</p>;

  const current = projects.find((p) => p.id === open);
  if (current) {
    return <ProjectView project={current} artistName={me.artist.displayName} balanceKobo={me.balanceKobo} onBack={() => setOpen(null)} onChange={(p) => setProjects((ps) => ps.map((x) => (x.id === p.id ? p : x)))} />;
  }

  return (
    <>
      <h1>Video studio</h1>
      <p className="muted">
        An AI director plans a video for your song, cut on the beat. One 30-second teaser a month is free; fans can fund full videos.
      </p>
      {error && <p className="error">{error}</p>}
      <NewProject releases={releases} onCreated={(p) => { setProjects((ps) => [p, ...ps]); setOpen(p.id); }} />
      <h2>Your videos</h2>
      {projects.length === 0 && <p className="muted">No videos yet.</p>}
      {projects.map((p) => (
        <button key={p.id} className="card" style={{ width: "100%", textAlign: "left", cursor: "pointer" }} onClick={() => setOpen(p.id)}>
          <strong>{p.treatment.title || "Untitled"}</strong>
          <div className="muted">
            {p.format === "teaser" ? "30s teaser" : "Full video"} · {STATUS_LABEL[p.status] ?? p.status}
            {p.funding?.open && ` · fans raised ${formatNaira(p.funding.raisedKobo)} of ${formatNaira(p.funding.goalKobo)}`}
          </div>
        </button>
      ))}
    </>
  );
}

function NewProject({ releases, onCreated }: { releases: Release[]; onCreated: (p: Project) => void }) {
  const [releaseId, setReleaseId] = useState("");
  const [format, setFormat] = useState<"teaser" | "full">("teaser");
  const [castMode, setCastMode] = useState<"likeness" | "character">("likeness");
  const [notes, setNotes] = useState("");
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);
  return (
    <form
      className="card"
      onSubmit={async (e) => {
        e.preventDefault();
        setBusy(true);
        setError(null);
        try {
          onCreated(await api<Project>("/v1/video/projects", { method: "POST", json: { releaseId, format, castMode, notes: notes || undefined } }));
        } catch (err) {
          setError((err as Error).message);
        } finally {
          setBusy(false);
        }
      }}
    >
      <strong>Plan a new video</strong>
      <label htmlFor="rel">Song</label>
      <select id="rel" required value={releaseId} onChange={(e) => setReleaseId(e.target.value)}>
        <option value="">Choose a release</option>
        {releases.map((r) => (
          <option key={r.id} value={r.id}>{r.title}</option>
        ))}
      </select>
      <label htmlFor="fmt">Format</label>
      <select id="fmt" value={format} onChange={(e) => setFormat(e.target.value as "teaser" | "full")}>
        <option value="teaser">30-second vertical teaser (TikTok, Reels, Shorts)</option>
        <option value="full">Full video, widescreen (YouTube)</option>
      </select>
      <label htmlFor="cast">Who&apos;s on screen</label>
      <select id="cast" value={castMode} onChange={(e) => setCastMode(e.target.value as "likeness" | "character")}>
        <option value="likeness">Me, from my photos (needs your consent)</option>
        <option value="character">An animated character standing in for me</option>
      </select>
      <label htmlFor="notes">Tell the director what you want (optional)</label>
      <textarea id="notes" rows={3} placeholder="Lagos at night, danfo buses, more dancing in the hook" value={notes} onChange={(e) => setNotes(e.target.value)} />
      {error && <p className="error">{error}</p>}
      <p><button className="btn" disabled={busy || !releaseId}>{busy ? "Planning…" : "Plan the video"}</button></p>
    </form>
  );
}

function ProjectView({ project: p, artistName, balanceKobo, onBack, onChange }: { project: Project; artistName: string; balanceKobo: number; onBack: () => void; onChange: (p: Project) => void }) {
  const [notes, setNotes] = useState("");
  const [editing, setEditing] = useState<{ shotId: string; direction: string } | null>(null);
  const [photos, setPhotos] = useState(0);
  const [agree, setAgree] = useState(false);
  const [goal, setGoal] = useState("");
  const [busy, setBusy] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function run(label: string, fn: () => Promise<Project | void>) {
    setBusy(label);
    setError(null);
    try {
      const next = await fn();
      if (next) onChange(next);
    } catch (e) {
      setError((e as Error).message);
    } finally {
      setBusy(null);
    }
  }

  const needsConsent = p.castMode === "likeness" && !p.consent;
  const canEdit = p.status === "treatment" || p.status === "casting";
  const picked = p.shots.filter((s) => s.pickedTakeId).length;
  const canGenerate = !needsConsent && (p.status === "treatment" || p.status === "casting");
  const studioShortfall = Math.max(0, p.quote.totalKobo - (p.funding?.raisedKobo ?? 0) - balanceKobo);

  return (
    <>
      <button className="linkish" onClick={onBack}>← All videos</button>
      <h1>{p.treatment.title || "Untitled"}</h1>
      <p className="muted">
        {p.format === "teaser" ? "30s teaser" : "Full video"} · {p.aspect} · {p.song.bpm} bpm · {STATUS_LABEL[p.status] ?? p.status}
      </p>
      {error && <p className="error">{error}</p>}

      <section className="card">
        <strong>Treatment</strong>
        <p>{p.treatment.concept}</p>
        <p className="muted">{p.treatment.look}</p>
        <p className="muted">Locations: {p.treatment.locations.join(" · ")}</p>
        {canEdit && (
          <form
            onSubmit={(e) => {
              e.preventDefault();
              run("revise", async () => {
                const next = await api<Project>(`/v1/video/projects/${p.id}/revise`, { method: "POST", json: { notes } });
                setNotes("");
                return next;
              });
            }}
          >
            <label htmlFor="rev">Ask the director for changes</label>
            <div className="row">
              <input id="rev" className="grow" placeholder="More dancing in the hook, colder colours…" value={notes} onChange={(e) => setNotes(e.target.value)} />
              <button className="btn secondary" disabled={busy !== null || notes.length < 2}>{busy === "revise" ? "Rewriting…" : "Revise"}</button>
            </div>
          </form>
        )}
      </section>

      <h2>Shots ({p.shots.length})</h2>
      {p.shots.map((s) => {
        const takes = p.takes.filter((t) => t.shotId === s.id && t.quality === "draft");
        return (
          <div key={s.id} className="card">
            <div className="row">
              <span className="muted">#{s.index + 1}</span>
              <span className="badge">{s.section} · {s.type}</span>
              <span className="muted">{s.startSeconds}s · {s.durationSeconds}s</span>
            </div>
            {editing?.shotId === s.id ? (
              <form
                onSubmit={(e) => {
                  e.preventDefault();
                  run(`edit-${s.id}`, async () => {
                    const next = await api<Project>(`/v1/video/projects/${p.id}/revise`, { method: "POST", json: { shotId: s.id, direction: editing.direction } });
                    setEditing(null);
                    return next;
                  });
                }}
              >
                <textarea rows={2} value={editing.direction} onChange={(e) => setEditing({ shotId: s.id, direction: e.target.value })} />
                <div className="row" style={{ marginTop: 6 }}>
                  <button className="btn secondary" disabled={busy !== null}>Save</button>
                  <button type="button" className="linkish" onClick={() => setEditing(null)}>Cancel</button>
                </div>
              </form>
            ) : (
              <p style={{ margin: "6px 0" }}>
                {s.direction}{" "}
                {canEdit && <button className="linkish" onClick={() => setEditing({ shotId: s.id, direction: s.direction })}>edit</button>}
              </p>
            )}
            {takes.length > 0 && (
              <div className="row">
                {takes.map((t, i) => (
                  <button
                    key={t.id}
                    className="btn secondary"
                    aria-pressed={s.pickedTakeId === t.id}
                    style={s.pickedTakeId === t.id ? { borderColor: "var(--accent)", color: "var(--accent)" } : undefined}
                    disabled={p.status !== "picking" || busy !== null}
                    onClick={() => run(`pick-${s.id}`, () => api<Project>(`/v1/video/projects/${p.id}/shots/${s.id}/pick`, { method: "POST", json: { takeId: t.id } }))}
                  >
                    {t.thumbnailUrl && <img src={t.thumbnailUrl} alt="" width={27} height={48} className="cover" style={{ verticalAlign: "middle", marginRight: 6 }} />}
                    Take {String.fromCharCode(65 + i)}{s.pickedTakeId === t.id ? " ✓" : ""}
                  </button>
                ))}
                {takes.map((t, i) => (
                  <a key={`${t.id}-dl`} className="linkish" href={t.url} target="_blank" rel="noreferrer">watch {String.fromCharCode(65 + i)}</a>
                ))}
              </div>
            )}
          </div>
        );
      })}

      {needsConsent && (
        <section className="card">
          <strong>Your likeness</strong>
          <p className="muted">Upload at least 3 clear photos of yourself. Only you can appear in your video; the consent you sign is recorded permanently and you can withdraw it at any time.</p>
          <label htmlFor="photo">Photo</label>
          <input
            id="photo"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            onChange={(e) => {
              const file = e.target.files?.[0];
              if (!file) return;
              run("photo", async () => {
                const r = await api<{ photos: number }>(`/v1/video/projects/${p.id}/photos`, { method: "PUT", body: file, headers: { "content-type": file.type } });
                setPhotos(r.photos);
              });
              e.target.value = "";
            }}
          />
          <p className="muted">{photos} uploaded</p>
          <label style={{ display: "flex", gap: 8, alignItems: "flex-start" }}>
            <input type="checkbox" checked={agree} onChange={(e) => setAgree(e.target.checked)} style={{ width: "auto", marginTop: 4 }} />
            I am the person in these photos. I allow Livebic to make this video and short clips of it with my likeness. Every output will say &ldquo;AI-generated video, approved by {artistName}&rdquo;.
          </label>
          <p>
            <button className="btn" disabled={!agree || photos < 3 || busy !== null} onClick={() => run("consent", async () => {
              await api(`/v1/video/projects/${p.id}/consent`, { method: "POST", json: { isSelf: true, allowedUses: ["music-video", "teaser", "social-clip"] } });
              return api<Project>(`/v1/video/projects/${p.id}`);
            })}>
              Sign and continue
            </button>
          </p>
        </section>
      )}

      {canGenerate && (
        <section className="card">
          <strong>Cost</strong>
          <p>
            Drafts {formatNaira(p.quote.draftKobo)} + final {formatNaira(p.quote.finalKobo)} = <strong>{formatNaira(p.quote.totalKobo)}</strong>
          </p>
          <p className="muted">
            {p.format === "teaser" ? "Your first teaser each month is free. " : ""}
            Fans have raised {formatNaira(p.funding?.raisedKobo ?? 0)}; your balance is {formatNaira(balanceKobo)}.
            {studioShortfall > 0 && p.format !== "teaser" && ` You're ${formatNaira(studioShortfall)} short — ask your fans to fund it.`}
          </p>
          {!p.funding?.open && (
            <form
              className="row"
              onSubmit={(e) => {
                e.preventDefault();
                run("fund", () => api<Project>(`/v1/video/projects/${p.id}/funding`, { method: "POST", json: { goalKobo: naira(Number(goal)) } }));
              }}
            >
              <input className="grow" type="number" min={200} placeholder="Funding goal (₦)" value={goal} onChange={(e) => setGoal(e.target.value)} />
              <button className="btn secondary" disabled={busy !== null || !goal}>Let fans fund it</button>
            </form>
          )}
          {p.funding?.open && <p className="muted">Campaign open: {formatNaira(p.funding.raisedKobo)} of {formatNaira(p.funding.goalKobo)} raised. Backers are credited in the video.</p>}
          <p>
            <button className="btn" disabled={busy !== null} onClick={() => run("drafts", () => api<Project>(`/v1/video/projects/${p.id}/drafts`, { method: "POST" }))}>
              {busy === "drafts" ? "Generating drafts…" : "Generate drafts"}
            </button>
          </p>
        </section>
      )}

      {p.status === "picking" && (
        <section className="card">
          <strong>Pick a take for every shot</strong>
          <p className="muted">{picked} of {p.shots.length} picked. Only the takes you pick are rendered in full quality.</p>
          <button className="btn" disabled={picked < p.shots.length || busy !== null} onClick={() => run("finalize", () => api<Project>(`/v1/video/projects/${p.id}/finalize`, { method: "POST" }))}>
            {busy === "finalize" ? "Rendering…" : "Render the video"}
          </button>
        </section>
      )}

      {p.exports.length > 0 && (
        <section className="card">
          <strong>Your video</strong>
          {p.exports.map((x) => (
            <div key={x.id} style={{ marginTop: 8 }}>
              <div className="muted">{x.aspect} · {x.label}</div>
              <div className="row" style={{ marginTop: 6 }}>
                <a className="btn" href={x.downloadUrl}>Download</a>
                <button className="btn secondary" onClick={() => navigator.clipboard?.writeText(x.shareUrl)}>Copy Livebic link</button>
              </div>
              <p className="muted">Post it to TikTok, Reels or Shorts with the link so fans land on your page.</p>
            </div>
          ))}
        </section>
      )}

      {p.consent && !p.consent.revoked && p.status !== "done" && (
        <p>
          <button className="linkish" onClick={() => { if (confirm("Withdraw consent? Your photos are deleted at our video partner and this project stops.")) run("revoke", async () => { await api(`/v1/video/projects/${p.id}/consent`, { method: "DELETE" }); return api<Project>(`/v1/video/projects/${p.id}`); }); }}>
            Withdraw my consent
          </button>
        </p>
      )}
    </>
  );
}
