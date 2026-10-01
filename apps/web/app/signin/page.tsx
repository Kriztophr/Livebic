"use client";

import { useRouter, useSearchParams } from "next/navigation";
import { Suspense, useState } from "react";
import { api, setToken } from "../../lib/api";

function SignIn() {
  const router = useRouter();
  const params = useSearchParams();
  const next = params.get("next") ?? "/";
  const [mode, setMode] = useState<"signup" | "signin">(params.get("mode") === "signin" ? "signin" : "signup");
  const [role, setRole] = useState<"fan" | "artist">("fan");
  const [email, setEmail] = useState("");
  const [name, setName] = useState("");
  const [handle, setHandle] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    try {
      const res =
        mode === "signup"
          ? await api<{ token: string }>("/v1/auth/signup", { method: "POST", json: { email, name, role, handle: role === "artist" ? handle : undefined } })
          : await api<{ token: string }>("/v1/auth/login", { method: "POST", json: { login: email, password: password || undefined } });
      setToken(res.token);
      router.push(role === "artist" && mode === "signup" ? "/studio" : next.startsWith("/") ? next : "/");
    } catch (err) {
      setError((err as Error).message);
    }
  }

  return (
    <>
      <h1>{mode === "signup" ? "Join Livebic" : "Welcome back"}</h1>
      <form className="card" onSubmit={submit}>
        {mode === "signup" && (
          <div className="tabs" role="tablist">
            <button type="button" className="tab" aria-selected={role === "fan"} onClick={() => setRole("fan")}>I&apos;m a fan</button>
            <button type="button" className="tab" aria-selected={role === "artist"} onClick={() => setRole("artist")}>I&apos;m an artist</button>
          </div>
        )}
        <label htmlFor="email">{mode === "signup" ? "Email" : "Email or username"}</label>
        <input id="email" type={mode === "signup" ? "email" : "text"} required autoComplete={mode === "signup" ? "email" : "username"} value={email} onChange={(e) => setEmail(e.target.value)} />
        {mode === "signin" && (
          <>
            <label htmlFor="password">Password</label>
            <input id="password" type="password" autoComplete="current-password" value={password} onChange={(e) => setPassword(e.target.value)} />
            <p className="muted">Had an account on the old Livebic? Use the same username and password.</p>
          </>
        )}
        {mode === "signup" && (
          <>
            <label htmlFor="name">{role === "artist" ? "Artist name" : "Your name"}</label>
            <input id="name" required value={name} onChange={(e) => setName(e.target.value)} />
          </>
        )}
        {mode === "signup" && role === "artist" && (
          <>
            <label htmlFor="handle">Page link: livebic.com/@</label>
            <input id="handle" required pattern="[a-z0-9_]{2,30}" value={handle} onChange={(e) => setHandle(e.target.value.toLowerCase())} />
          </>
        )}
        {error && <p className="error">{error}</p>}
        <p>
          <button className="btn" type="submit">{mode === "signup" ? "Create account" : "Sign in"}</button>
        </p>
        <button type="button" className="linkish" onClick={() => setMode(mode === "signup" ? "signin" : "signup")}>
          {mode === "signup" ? "Already have an account? Sign in" : "New here? Create an account"}
        </button>
        <p className="notice">Preview build: passkey sign-in arrives before launch.</p>
      </form>
    </>
  );
}

export default function Page() {
  return (
    <Suspense>
      <SignIn />
    </Suspense>
  );
}
