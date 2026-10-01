# Livebic

Nigeria-first home base where musicians are paid directly by fans, own their supporter list,
and are discovered through an open, published ranking algorithm. Product spec: [docs/SPEC.md](docs/SPEC.md).
What's built and what's next: [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

This is the Phase 1 rebuild. The 2022 PHP app (DeepSound) in `Script/` still runs the live site and deploys via
`.github/workflows/main_livebic.yml`. It is being retired: `npm run migrate:deepsound -w @livebic/api` imports its
users, artists, songs and history into the new build. See [docs/MIGRATION.md](docs/MIGRATION.md) for the cutover runbook.

## Layout

| Path | What it is |
| --- | --- |
| `packages/core` | Pure domain logic: money in kobo, revenue splits, ranking engine and published rules, signed receipts, supporter export, copy guard |
| `apps/api` | Fastify modular monolith: Identity, Content, Payments, Ranking, Supporters, Admin. Partners behind interfaces with sandbox mocks |
| `apps/web` | Next.js PWA: feeds with "Why this?", artist pages, checkout, receipts, ranking rules page, artist Studio |
| `apps/api/db/schema.sql` | Postgres schema (source of truth for production) |
| `apps/api/src/migrate` | One-off DeepSound (MySQL) importer and the bundle loader |

## Run it locally

Node 22+.

```sh
npm install
npm run dev:api   # http://localhost:4000, sandbox mode with seeded Lagos artists and fans
npm run dev:web   # http://localhost:3000
```

Sandbox mode uses mock partners: checkout opens a local "Pay / Decline" page and no money moves.
Seed accounts sign in by email with no password (sandbox only): `tobilagos@livebic.test` (artist),
`fan1@livebic.test` (fan), `admin@livebic.test` (admin).

## Checks

```sh
npm run typecheck
npm test
npm run build -w @livebic/web
```
