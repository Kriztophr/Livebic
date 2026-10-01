"use client";

import { useEffect, useState } from "react";
import { api, getToken } from "../lib/api";
import type { FeedResponse } from "../lib/types";
import { FeedCard } from "./FeedCard";

const FEEDS = [
  { id: "rising", label: "Rising" },
  { id: "most-supported", label: "Most supported" },
  { id: "following", label: "Following" },
  { id: "newest", label: "Newest" },
] as const;

type FeedId = (typeof FEEDS)[number]["id"];
const FEED_KEY = "livebic.feed";

export function FeedView() {
  const [feed, setFeed] = useState<FeedId>("rising");
  const [data, setData] = useState<FeedResponse | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    try {
      const saved = (new URLSearchParams(window.location.search).get("feed") ?? localStorage.getItem(FEED_KEY)) as FeedId | null;
      if (saved && FEEDS.some((f) => f.id === saved)) setFeed(saved);
    } catch {}
  }, []);

  useEffect(() => {
    setError(null);
    setData(null);
    if (feed === "following" && !getToken()) {
      setError("Sign in to see artists you follow and support.");
      return;
    }
    api<FeedResponse>(`/v1/feeds/${feed}?limit=20`)
      .then(setData)
      .catch((e) => setError((e as Error).message));
  }, [feed]);

  function choose(id: FeedId) {
    setFeed(id);
    try {
      localStorage.setItem(FEED_KEY, id);
    } catch {}
  }

  const label = FEEDS.find((f) => f.id === feed)!.label;
  return (
    <section>
      <div className="tabs" role="tablist">
        {FEEDS.map((f) => (
          <button key={f.id} role="tab" className="tab" aria-selected={f.id === feed} onClick={() => choose(f.id)}>
            {f.label}
          </button>
        ))}
      </div>
      {error && <p className="muted">{error}</p>}
      {data && data.items.length === 0 && <p className="muted">Nothing here yet.</p>}
      {data?.items.map((item) => (
        <FeedCard key={item.release.id} release={item.release} why={item.why} feed={label} rank={item.rank} />
      ))}
      {data && (
        <p className="muted">
          Ranked with published rules v{data.rulesVersion}, updated {new Date(data.computedAt).toLocaleTimeString("en-NG")}.
        </p>
      )}
    </section>
  );
}
