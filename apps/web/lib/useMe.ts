"use client";

import { useEffect, useState } from "react";
import { api, getToken } from "./api";
import type { Me } from "./types";

export function useMe(): { me: Me | null; loading: boolean; reload: () => void } {
  const [me, setMe] = useState<Me | null>(null);
  const [loading, setLoading] = useState(true);
  const [n, setN] = useState(0);
  useEffect(() => {
    const onAuth = () => setN((x) => x + 1);
    window.addEventListener("livebic:auth", onAuth);
    return () => window.removeEventListener("livebic:auth", onAuth);
  }, []);
  useEffect(() => {
    if (!getToken()) {
      setMe(null);
      setLoading(false);
      return;
    }
    api<Me>("/v1/me")
      .then(setMe)
      .catch(() => setMe(null))
      .finally(() => setLoading(false));
  }, [n]);
  return { me, loading, reload: () => setN((x) => x + 1) };
}
