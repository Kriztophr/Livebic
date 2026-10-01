"use client";

export const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:4000";

const TOKEN_KEY = "livebic.session";

export function getToken(): string | null {
  try {
    return localStorage.getItem(TOKEN_KEY);
  } catch {
    return null;
  }
}

export function setToken(token: string | null) {
  try {
    if (token) localStorage.setItem(TOKEN_KEY, token);
    else localStorage.removeItem(TOKEN_KEY);
  } catch {
    /* private mode: session lasts for this tab only */
  }
  window.dispatchEvent(new Event("livebic:auth"));
}

export class ApiError extends Error {
  constructor(public status: number, public code: string, message: string) {
    super(message);
  }
}

export async function api<T = unknown>(path: string, init: RequestInit & { json?: unknown; idempotent?: boolean } = {}): Promise<T> {
  const headers = new Headers(init.headers);
  const token = getToken();
  if (token) headers.set("authorization", `Bearer ${token}`);
  if (init.json !== undefined) headers.set("content-type", "application/json");
  if (init.idempotent) headers.set("idempotency-key", crypto.randomUUID());
  const res = await fetch(`${API_URL}${path}`, { ...init, headers, body: init.json !== undefined ? JSON.stringify(init.json) : init.body });
  if (res.status === 204) return undefined as T;
  const type = res.headers.get("content-type") ?? "";
  const body = type.includes("json") ? await res.json() : await res.text();
  if (!res.ok) throw new ApiError(res.status, body?.error ?? "error", body?.message ?? "Something went wrong");
  return body as T;
}

/** Start a support payment and hand the fan to the processor's checkout page. */
export async function startCheckout(payload: { kind: string; artistId: string; releaseId?: string | null; amountKobo?: number; currency?: string }) {
  const { checkoutUrl } = await api<{ checkoutUrl: string }>("/v1/orders", { method: "POST", json: payload, idempotent: true });
  window.location.href = checkoutUrl;
}
