"use client";

import { useParams, useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { formatNaira } from "@livebic/core/money";
import { FeedCard } from "../../../components/FeedCard";
import { api, getToken, startCheckout } from "../../../lib/api";
import type { ReleaseView } from "../../../lib/types";

interface Profile {
  artist: {
    id: string;
    handle: string;
    displayName: string;
    bio: string;
    links: string[];
    verified: boolean;
    supporterCount: number;
    followerCount: number;
    membershipPriceKobo: number | null;
    shortLink: string;
  };
  releases: { id: string }[];
  tipAmountsKobo: number[];
  viewer: { following: boolean; supporter: boolean } | null;
}

export default function ArtistPage() {
  const { handle } = useParams<{ handle: string }>();
  const router = useRouter();
  const [profile, setProfile] = useState<Profile | null>(null);
  const [releases, setReleases] = useState<ReleaseView[]>([]);
  const [currency, setCurrency] = useState<"NGN" | "USD" | "GBP">("NGN");
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  async function load() {
    const p = await api<Profile>(`/v1/artists/${handle}`);
    setProfile(p);
    setReleases(await Promise.all(p.releases.map((r) => api<ReleaseView>(`/v1/releases/${r.id}`))));
  }

  useEffect(() => {
    load().catch((e) => setError((e as Error).message));
  }, [handle]);

  async function support(payload: { kind: string; releaseId?: string; amountKobo?: number }) {
    if (!getToken()) return router.push(`/signin?next=/a/${handle}`);
    setBusy(true);
    setError(null);
    try {
      await startCheckout({ ...payload, artistId: profile!.artist.id, currency });
    } catch (e) {
      setError((e as Error).message);
      setBusy(false);
    }
  }

  async function toggleFollow() {
    if (!getToken()) return router.push(`/signin?next=/a/${handle}`);
    await api(`/v1/artists/${profile!.artist.id}/follow`, { method: profile!.viewer?.following ? "DELETE" : "POST" });
    await load();
  }

  if (error && !profile) return <p className="error">{error}</p>;
  if (!profile) return <p className="muted">Loading…</p>;
  const { artist } = profile;

  return (
    <>
      <h1>
        {artist.displayName} {artist.verified && <span className="badge">Verified</span>}
      </h1>
      <p>{artist.bio}</p>
      <p className="muted">
        {artist.supporterCount} supporters · {artist.followerCount} followers ·{" "}
        <button className="linkish" onClick={() => navigator.clipboard?.writeText(artist.shortLink)}>Copy link</button>
      </p>
      <div className="row">
        <button className="btn secondary" onClick={toggleFollow}>{profile.viewer?.following ? "Following" : "Follow"}</button>
        {artist.links.map((l) => (
          <a key={l} href={l} className="muted" target="_blank" rel="noreferrer">{new URL(l).hostname.replace("www.", "")}</a>
        ))}
      </div>

      <h2>Support {artist.displayName}</h2>
      <div className="card">
        <div className="row">
          {profile.tipAmountsKobo.map((amt) => (
            <button key={amt} className="btn" disabled={busy} onClick={() => support({ kind: "tip", amountKobo: amt })}>
              Tip {formatNaira(amt)}
            </button>
          ))}
        </div>
        {artist.membershipPriceKobo !== null && (
          <div className="row" style={{ marginTop: 12 }}>
            <div className="grow">
              <strong>Monthly membership</strong>
              <div className="muted">Every supporters-only release and paid unlock while you&apos;re a member.</div>
            </div>
            <button className="btn" disabled={busy} onClick={() => support({ kind: "membership" })}>
              Join · {formatNaira(artist.membershipPriceKobo)}/month
            </button>
          </div>
        )}
        <div className="row" style={{ marginTop: 12 }}>
          <label htmlFor="cur" style={{ margin: 0 }}>Paying from abroad?</label>
          <select id="cur" value={currency} onChange={(e) => setCurrency(e.target.value as typeof currency)} style={{ width: "auto" }}>
            <option value="NGN">Pay in naira</option>
            <option value="USD">Pay by card in USD</option>
            <option value="GBP">Pay by card in GBP</option>
          </select>
        </div>
        {error && <p className="error">{error}</p>}
      </div>

      <h2>Releases</h2>
      {releases.map((r) => (
        <div key={r.id}>
          <FeedCard release={r} />
          <div className="row" style={{ marginTop: -4, marginBottom: 12 }}>
            {r.access === "paid" && !r.canListen && r.priceKobo && (
              <button className="btn" disabled={busy} onClick={() => support({ kind: "unlock", releaseId: r.id })}>
                Unlock · {formatNaira(r.priceKobo)}
              </button>
            )}
            {r.edition && r.edition.remaining > 0 && (
              <button className="btn secondary" disabled={busy} onClick={() => support({ kind: "drop", releaseId: r.id })}>
                Own a piece · {formatNaira(r.edition.priceKobo)}
              </button>
            )}
          </div>
        </div>
      ))}
    </>
  );
}
