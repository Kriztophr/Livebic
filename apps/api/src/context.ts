import { createHash } from "node:crypto";
import type { FastifyRequest } from "fastify";
import { RulesRegistry, generateReceiptKeys, type FeedName, type FeedSnapshot } from "@livebic/core";
import type { Config } from "./config";
import { badRequest, forbidden, HttpError, unauthorized } from "./errors";
import type { Partners } from "./partners/types";
import type { MemoryStore, Role, User } from "./store";

/** Redis in production: precomputed scores and factor breakdowns per item. */
export interface FeedCache {
  get(feed: Exclude<FeedName, "following">): Promise<FeedSnapshot | null>;
  set(snapshot: FeedSnapshot): Promise<void>;
}

export function memoryFeedCache(): FeedCache {
  const m = new Map<string, FeedSnapshot>();
  return {
    async get(feed) {
      return m.get(feed) ?? null;
    },
    async set(s) {
      m.set(s.feed, s);
    },
  };
}

export interface AppContext {
  config: Config;
  store: MemoryStore;
  partners: Partners;
  rules: RulesRegistry;
  cache: FeedCache;
  receiptKeys: { privateKeyPem: string; publicKeyPem: string };
  now: () => Date;
}

export function createContext(input: Omit<AppContext, "rules" | "cache" | "receiptKeys" | "now"> & Partial<AppContext>): AppContext {
  return {
    rules: new RulesRegistry(),
    cache: memoryFeedCache(),
    receiptKeys: generateReceiptKeys(),
    now: () => new Date(),
    ...input,
  };
}

export function currentUser(ctx: AppContext, req: FastifyRequest): User | null {
  const header = req.headers.authorization;
  if (!header?.startsWith("Bearer ")) return null;
  const userId = ctx.store.sessions.get(header.slice(7));
  return userId ? (ctx.store.users.get(userId) ?? null) : null;
}

export function requireUser(ctx: AppContext, req: FastifyRequest, role?: Role): User {
  const user = currentUser(ctx, req);
  if (!user) throw unauthorized();
  if (role && user.role !== role && user.role !== "admin") throw forbidden();
  return user;
}

export function requireAdmin(ctx: AppContext, req: FastifyRequest): User {
  const user = requireUser(ctx, req);
  if (user.role !== "admin") throw forbidden();
  return user;
}

const inFlight = new Map<string, Promise<{ statusCode: number; body: unknown }>>();

/**
 * Idempotency keys on payment calls (spec: API gateway). Replays with the same key return the
 * stored response; reusing a key with a different body is rejected.
 */
export async function withIdempotency(
  ctx: AppContext,
  req: FastifyRequest,
  scope: string,
  userId: string,
  run: () => Promise<{ statusCode: number; body: unknown }>,
): Promise<{ statusCode: number; body: unknown }> {
  const key = req.headers["idempotency-key"];
  if (typeof key !== "string" || key.length < 8 || key.length > 128) {
    throw badRequest("idempotency_key_required", "Send an Idempotency-Key header (8–128 characters)");
  }
  const storeKey = `${scope}:${userId}:${key}`;
  const fingerprint = createHash("sha256").update(JSON.stringify(req.body ?? null)).digest("hex");
  const existing = ctx.store.idempotency.get(storeKey);
  if (existing) {
    if (existing.fingerprint !== fingerprint) throw new HttpError(422, "idempotency_key_reused", "This Idempotency-Key was used with a different request");
    return { statusCode: existing.statusCode, body: existing.body };
  }
  const pending = inFlight.get(storeKey);
  if (pending) return pending;
  const p = run()
    .then((res) => {
      ctx.store.idempotency.set(storeKey, { fingerprint, ...res });
      return res;
    })
    .finally(() => inFlight.delete(storeKey));
  inFlight.set(storeKey, p);
  return p;
}
