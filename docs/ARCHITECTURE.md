# Livebic Phase 1 — architecture and build status

Status as of 1 Oct 2026. Maps the [spec](SPEC.md) to code and lists what is still open.

## Shape

One API gateway in front of a modular monolith (spec: "Core services — modular monolith at launch").
Modules talk through exported functions, so any one can be split into a service later.

```
apps/web (Next.js PWA) ──► apps/api (Fastify)
                              ├─ identity    sign-up, silent wallet, artist profile + verification, follows
                              ├─ content     releases, audio upload → SHA-256 → registry, tiers, signed media URLs, reports
                              ├─ payments    orders + idempotency keys, processor webhook, splits, ledger, receipts, payouts
                              ├─ ranking     feeds from cache, "Why this?", published rules + changelog, engagement events
                              ├─ supporters  supporter list JSON + CSV export
                              └─ admin       verification, payout approval, moderation, fraud flags, rule changes
                           packages/core: all scoring, split and receipt maths (no I/O, fully unit-tested)
                           partners/types.ts: processor, payout partner, wallet, chain registry, storage, mail, FX
```

## Spec → code

| Spec requirement | Where | Notes |
| --- | --- | --- |
| Wallet created at sign-up, no user action | `modules/identity.ts` | Address hidden from fans; creators reveal it via `showWalletAddress` |
| SHA-256 fingerprint on-chain | `modules/content.ts`, `ChainRegistry` | Recorded on audio upload |
| Tips at fixed naira amounts | `core/money.ts` `TIP_AMOUNTS_KOBO` | ₦500 – ₦10,000 |
| Unlock, membership, own-a-piece | `modules/payments.ts` | Editions are numbered at payment; sold out after checkout → `refund_required` |
| Diaspora USD/GBP | `modules/payments.ts` | Price fixed in naira, card charge converted via `FxRates` |
| Idempotency keys on payment calls | `context.ts` `withIdempotency` | Required on `POST /v1/orders` and `POST /v1/payouts` |
| Webhooks confirm payment | `POST /v1/webhooks/processor` | HMAC over the raw body; replays are no-ops |
| Automatic splits, fee < 10% | `core/splits.ts` | Basis points, exact to the kobo; fee capped at 9.99% (default 8%) |
| Payout within 24 h, threshold → approval | `modules/payments.ts`, `modules/admin.ts` | Balance available on confirmation; held on request; rejection reverses |
| KYC before first payout | `PayoutPartner.kycStatus` | Plus artist verification |
| Ownership model A (verified receipt) | `core/receipts.ts` | Ed25519-signed, hash written to registry, public `GET /v1/receipts/:id/verify` |
| Four feeds | `core/ranking/engine.ts` | Following, Rising, Most-supported, Newest |
| Paid > free, verified > anonymous | `core/ranking/rules.ts` | `validateWeights` refuses any free weight ≥ a paid action |
| 7-day half-life | `engine.ts` `decay` | |
| Diversity cap 2 of top 20 | `engine.ts` `applyDiversityCap` | |
| Anti-gaming | `engine.ts` | Self-support and circular tipping excluded; unverified ×0.25, new accounts ×0.5; velocity spikes → admin fraud flags |
| "Why this?" top three factors | `GET /v1/feeds/:feed/items/:id/why` | Feed responses also include them inline |
| Rules page + dated changelog | `GET /v1/ranking/rules`, `/how-ranking-works` | Plain-language rules generated from live weights |
| Changes logged before taking effect | `RulesRegistry.propose` | Needs a change record and ≥ 24 h notice |
| Scores refreshed every 15 min | `jobs/ranking.ts` | Interval in `server.ts`; admins can force a recompute |
| Supporter CSV export, regardless of platform status | `modules/supporters.ts` | Formula-injection safe |
| No crypto vocabulary for fans | `core/copy.ts` | Tests scan fan-facing screens, emails and the rules text |
| PWA: add-to-home-screen, offline shell | `apps/web/public` | Push notifications not wired yet |

## Sandbox vs real

Everything behind `apps/api/src/partners/types.ts` is a mock today (`partners/sandbox.ts`), and data
lives in memory (`store.ts`). `server.ts` refuses to start with `LIVEBIC_SANDBOX=false` until the real
partners are wired. That is on purpose: the partner and chain choices are open decisions in the spec.

## Still to do before launch

Blocked on the spec's open decisions:
- **Payment + payout partner.** Implement `PaymentProcessor` and `PayoutPartner` against the chosen partner's API (one partner for both is the spec's recommendation).
- **Settlement chain** (Base / Solana / Polygon). Implement `ChainRegistry` and the split contract on it; sponsor fees.
- **Embedded-wallet provider.** Implement `WalletProvider`.
- **Counsel sign-off** on the partner-custody model before real money flows.

Engineering work, not blocked:
- Postgres-backed store implementing the same shapes as `MemoryStore` (schema in `db/schema.sql`, validated against Postgres 16), plus migrations.
- Passkey (WebAuthn) and email-link sign-in. Sandbox sign-in by email alone is for local use only.
- Redis `FeedCache` and a Kafka-compatible event stream in place of the in-memory versions.
- Object storage + CDN in place of in-memory storage; audio transcoding pipeline.
- Recurring billing for memberships (today a membership is a 30-day pass).
- Refund flow for `refund_required` orders and chargebacks.
- Admin UI (the admin API exists; there are no admin screens yet).
- Push notifications; email delivery provider; annual creator tax statements.
- Load-test the artist page against the "under 2 s on 3G" target.
