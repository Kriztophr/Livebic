"use client";

import Link from "next/link";
import { useState } from "react";
import { formatNaira } from "@livebic/core/money";
import { api } from "../lib/api";
import type { Factor, ReleaseView } from "../lib/types";

const ACCESS_LABEL = { public: "Free", supporters: "Supporters only", paid: "Unlock" } as const;

export function FeedCard({ release, why, feed, rank }: { release: ReleaseView; why?: Factor[]; feed?: string; rank?: number }) {
  const [showWhy, setShowWhy] = useState(false);
  const [src, setSrc] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function play() {
    setError(null);
    try {
      const { url } = await api<{ url: string }>(`/v1/releases/${release.id}/stream`);
      setSrc(url);
      void api("/v1/events", { method: "POST", json: { type: "play", itemId: release.id } }).catch(() => {});
    } catch (e) {
      setError((e as Error).message);
    }
  }

  return (
    <article className="card">
      <div className="row">
        {rank !== undefined && <span className="muted">#{rank}</span>}
        <div className="grow">
          <strong>{release.title}</strong>
          {release.artist && (
            <div className="muted">
              <Link href={`/a/${release.artist.handle}`}>{release.artist.displayName}</Link>
            </div>
          )}
        </div>
        <span className="badge">
          {ACCESS_LABEL[release.access]}
          {release.access === "paid" && release.priceKobo ? ` · ${formatNaira(release.priceKobo)}` : ""}
        </span>
      </div>
      {release.edition && (
        <p className="muted">
          Own a piece: {release.edition.remaining} of {release.edition.size} left at {formatNaira(release.edition.priceKobo)}
        </p>
      )}
      <div className="row" style={{ marginTop: 8 }}>
        {release.canListen ? (
          <button className="btn secondary" onClick={play}>Play</button>
        ) : (
          release.artist && <Link className="btn secondary" href={`/a/${release.artist.handle}`}>Support to listen</Link>
        )}
        <span className="grow" />
        {why && (
          <button className="linkish" aria-expanded={showWhy} onClick={() => setShowWhy((s) => !s)}>
            Why this?
          </button>
        )}
      </div>
      {error && <p className="error">{error}</p>}
      {src && <audio src={src} controls autoPlay />}
      {showWhy && why && (
        <div className="why">
          <div>Top reasons this is here{feed ? ` in ${feed}` : ""}:</div>
          <ol>
            {why.map((f) => (
              <li key={f.key}>
                {f.label} <span className="muted">({f.points.toFixed(1)} pts)</span>
              </li>
            ))}
          </ol>
          <Link href="/how-ranking-works" className="muted">See all ranking rules</Link>
        </div>
      )}
    </article>
  );
}
