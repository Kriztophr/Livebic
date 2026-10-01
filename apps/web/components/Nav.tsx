"use client";

import Link from "next/link";
import { setToken } from "../lib/api";
import { useMe } from "../lib/useMe";

export function Nav() {
  const { me } = useMe();
  return (
    <nav className="nav">
      <Link href="/" className="brand">livebic</Link>
      <Link href="/how-ranking-works">How ranking works</Link>
      <span className="spacer" />
      {me?.artist && <Link href="/studio">Studio</Link>}
      {me ? (
        <button className="linkish" onClick={() => setToken(null)}>Sign out</button>
      ) : (
        <Link href="/signin">Sign in</Link>
      )}
    </nav>
  );
}
