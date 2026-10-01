# Livebic Rebuild — Product Spec & System Architecture

Oct 1, 2026 · @Christopher Nwonu

## Overview

Livebic is rebuilt from scratch as a Nigeria-first home base where musicians and content creators are paid directly by fans, own their audience, and are discovered through an open, published ranking algorithm. Blockchain stays in the product (stablecoin settlement, automatic revenue splits, proof of authorship) but disappears from the user experience: fans pay by card, bank transfer or local wallet; creators choose naira or USD stablecoin payouts.

The 2022 build was a music NFT platform ([livebic.webflow.io](https://livebic.webflow.io/)): mint songs, albums and artwork as NFTs, fractional ownership, fan collectibles. The rebuild keeps that ownership idea, drops the visible NFT framing, adds the payment and discovery layers, and widens the creator base beyond music in a second phase.

Why now: in 2026 YouTube (PYUSD via PayPal, US creators) and Meta (USDC on Solana and Polygon, Colombia and Philippines pilot with Stripe) began paying creators in stablecoins, and Nigeria's SEC has said it wants Lagos to be the stablecoin capital of the global South. Payments are becoming commodity infrastructure; Livebic's differentiation is the open algorithm and creator ownership on top of it.

## Goals and non-goals

The rebuild succeeds if, within six months of launch, Nigerian artists are receiving real fan income through Livebic and new artists are being discovered through its feeds.

**Goals**

- Direct fan-to-creator payment with settlement in minutes, not weeks, and a platform cut under 10%.
- A fan never sees a wallet, seed phrase, gas fee or the word "blockchain".
- Published ranking rules and switchable feeds, so creators can see why they surface.
- Creators own a portable supporter list they can export at any time.
- Launch with musicians in Lagos; add other content creators in Phase 2.

**Non-goals (for this rebuild)**

- Competing with TikTok, Instagram or YouTube on discovery of brand-new audiences. Livebic is the home base those platforms funnel into.
- Speculative NFT trading or secondary-market royalties as a core feature.
- Issuing Livebic's own token or stablecoin.
- Native iOS and Android apps at launch. The product is a web app (PWA); native apps come in Phase 3 for viewing and notifications only.

## Users and core jobs

Three user types share one product; artists and creators share one account model with different content types.

| User | Who they are | Core jobs on Livebic |
| --- | --- | --- |
| Artist (Phase 1) | Independent Nigerian musicians and small labels | Publish releases and exclusives; run "own a piece" drops; collect tips and subscriptions; see who their true fans are; get paid in naira or USD |
| Creator (Phase 2) | Video, podcast, comedy and writing creators who already post on TikTok, Instagram, YouTube | Same as artist, plus clip export with link-backs, collaborator splits, membership tiers |
| Fan | Nigerian and diaspora supporters of specific artists | Discover rising artists; pay to support, unlock exclusives or own a share of a release; keep a collection; follow across platforms |
| Platform admin | Livebic team | Moderation, payout approvals, fraud review, feed rule changes, revenue reporting |

## Product principles

Every feature decision is tested against four principles.

1. **Hidden blockchain.** The ledger is plumbing. Sign-up is email or passkey; a wallet is created silently; prices show in naira; payouts arrive as naira or USD stablecoin by the creator's choice. No crypto vocabulary in fan-facing copy.
2. **Open algorithm.** Every feed's scoring rules are published in-product. A creator can open any post and see the factors that ranked it. Users can switch feeds. Rule changes are announced with a changelog.
3. **Home base, not replacement.** Creators keep posting to TikTok, Instagram, YouTube and Snapchat for reach. Livebic is where true fans pay and where the creator owns the relationship. Every export carries a link back.
4. **Aligned revenue.** The platform earns primarily from transaction fees, so it wins when creators earn. Ads are secondary, opt-in, and their revenue is pooled and shared with creators under published rules.

## Feature spec by phase

Phase 1 ships a tight music product; Phase 2 generalises it to all creators; Phase 3 adds ads, native apps and expansion. Durations are estimates for a team of three to four engineers.

### Phase 1 — Core (musicians, Lagos; 8–12 weeks)

| Feature | Requirement | Acceptance criteria |
| --- | --- | --- |
| Accounts | Email or passkey sign-up; roles: fan, artist, admin; artist verification (ID + social proof) | New fan signs up in under 60 seconds; wallet created in background with no user action |
| Artist profile | Bio, links, releases, exclusives, supporter count, "own a piece" drops | Public page loads in under 2 s on 3G; shareable short link |
| Content | Audio upload (MP3/WAV), cover art, lyrics, demos; public, supporters-only, or paid tiers | Files stored off-chain; SHA-256 fingerprint recorded on-chain as proof of authorship |
| Support actions | Tip (fixed amounts in naira), monthly membership, one-off unlock, "own a piece" purchase (limited editions) | Payment completes in under 10 s; receipt emailed; supporter added to artist's list |
| Payments in | Card, bank transfer, USSD, and Nigerian wallets via a licensed local processor; diaspora card payments in USD/GBP | 95% payment success rate on supported methods |
| Payouts | Artist chooses naira (to bank) or USD stablecoin (to embedded or external wallet); automatic splits for collaborators | Payout available within 24 h of sale; split percentages set once per release |
| Feeds | Following, Rising, Most-supported, Newest; scoring rules page; "why am I seeing this" on every item | Rules page updated in same release as any ranking change |
| Supporter list | Artist sees and exports supporters (name, tier, total support) as CSV | Export works regardless of platform status |
| Admin | Moderation queue, payout approval, fraud flags, feed rule editor | All payouts over a set threshold require manual approval |

### Phase 2 — Creators (8 weeks)

- Generalise "release" to "content": video, audio, text, image posts use the same tiers, drops and tips.
- Collaborator splits across content types (creator + editor, host + guest).
- Clip export: trim a video, add a Livebic link and watermark, share to TikTok, Instagram, YouTube Shorts, Snapchat.
- Membership tiers with recurring billing.
- Creator analytics: where supporters came from, which exports converted.

### Phase 3 — Scale

- Opt-in ads with a transparent revenue pool shared by published rules.
- Thin native iOS and Android apps for viewing, notifications and clip export; purchases stay on web.
- Fan collections and resale of limited editions (only if regulatory review allows).
- Expansion to Ghana, Kenya and diaspora markets.

### Phase 3 — Digital twins (Higgsfield-powered)

Artists and creators can create a consented, licensed AI version of themselves: a trained visual identity, a cloned voice, and a persona they control. Fans pay for twin interactions and the artist earns from every use. Generation runs on the Higgsfield API; Livebic builds the consent, licensing and payment layer around it.

**What the twin can do**

| Product | Higgsfield capability | Fan or artist value |
| --- | --- | --- |
| Personalised shout-outs and messages | Voice clone + text-to-speech, lip-synced avatar video | Fans buy a message in the artist's voice; artist approves templates once |
| Clip and ad-read generation | Image and video generation with the artist's trained identity model, motion control | Artist records once, exports many clips to TikTok, Instagram, YouTube |
| Language versions for diaspora fans | Dubbing and voice change | Same clip in Yoruba, Igbo, Hausa, Pidgin, French, Portuguese |
| Licensed voice features | Voice model made available to vetted producers | Producer pays per use; split contract pays the artist automatically |
| Virtual performance | Avatar video generation with the artist's motion | Ticketed stream for fans who cannot attend live shows |

**Creation flow**

1. Only a verified artist can start a twin; a second identity check is required.
2. Artist signs a consent and licence record stating what the twin may do; its hash is written to the chain registry alongside their content hashes.
3. Artist uploads reference audio and video; Livebic's Twin service trains a Higgsfield identity model and voice.
4. Artist reviews sample outputs and sets allowed uses, prices and blocked topics.
5. Every generated output carries a provenance label and watermark and is logged against the licence.
6. Artist can pause or revoke the twin at any time; revocation deletes the models from Higgsfield and blocks new generations.

**Guardrails**

- No twin without verified identity and signed consent; no twins of third parties.
- Outputs are generated server-side only, never from a fan-supplied prompt without a template the artist approved.
- Blocked categories enforced before generation: political endorsement, sexual content, health or financial claims, impersonation of other people.
- Fan-facing labelling: "AI message approved by \[artist\]" on every twin output.

**Architecture additions**

- Twin service (Livebic-owned): consent records, licence terms, template library, generation queue, usage logs.
- Higgsfield API as a licensed partner beside the payment and social partners; generated media stored in object storage with the same tiering as uploads.
- Chain registry extended with licence hashes so a producer's use of a voice model is provable.

**Pricing**

- Fan products: fixed naira prices set by the artist, platform fee as for other support actions.
- Producer licences: per-use or per-track fee, paid through the same split contracts.
- Generation cost (Higgsfield credits) is passed through inside the price so twins never run at a loss.

## System architecture

&#91;embedded content: Livebic system architecture · 5 layers, partners hold custody\]

Requests flow top-down: clients call one API gateway, which routes to five Livebic-owned services. Services read and write Livebic's own data stores on the left and call licensed partners on the right; the Ranking service (highlighted) is the differentiator, and no stablecoin custody sits inside Livebic.

| Component | Build choice | Notes |
| --- | --- | --- |
| Web app | Next.js PWA, served from CDN | Add-to-home-screen, push notifications, offline shell; marketing site stays on Webflow |
| API gateway | Node/TypeScript (NestJS or Fastify) | Auth, rate limits, idempotency keys on payment calls, partner webhooks |
| Core services | Modular monolith at launch, split later | Identity, Content, Payments, Ranking, Export as modules with clear interfaces |
| Postgres | Managed Postgres (e.g. Neon, Supabase, RDS) | Source of truth for users, releases, orders, splits, supporter lists |
| Object storage + CDN | S3-compatible storage with CDN | Media off-chain; signed URLs for paid tiers; transcoding pipeline for audio and video |
| Event stream | Kafka-compatible or managed queue | Every engagement and payment event; feeds the Ranking job and analytics |
| Feed cache | Redis | Precomputed scores and factor breakdowns per item, refreshed every 15 minutes |
| Ranking job | Scheduled worker | Reads events, applies published weights, writes to cache; weights stored in versioned config |
| Naira processor | Licensed Nigerian processor | Card, bank transfer, USSD, mobile wallets; webhooks confirm payment |
| Payout partner | Licensed partner with stablecoin rails | Holds USDC balances, converts to naira, runs KYC; Livebic calls its API, never holds funds |
| Wallet provider | Embedded-wallet SDK | Passkey-secured wallet per user; address hidden unless the creator opts to see it |
| Chain | USDC on Base or Solana (decide with partner) | Split contracts and content-hash registry; fees sponsored so users never pay gas |
| Social APIs | TikTok, Instagram, YouTube, Snapchat | Clip publishing with link-back; rate limits and approval processes per platform |

## Payments, wallets and ownership model

Fans pay in familiar ways, the platform settles in a USD stablecoin on a low-fee chain, and creators cash out in naira or keep stablecoin. Livebic never holds customer crypto itself; a licensed partner does.

**Money in**

1. Fan pays in naira (card, bank transfer, USSD, mobile wallet) through a licensed Nigerian processor, or in USD/GBP by card for diaspora fans.
2. The processor settles to Livebic's partner account; the partner converts to USDC and credits the sale on-chain.
3. A split contract divides the amount by the release's preset percentages: creator(s), collaborators, platform fee.

**Money out**

- Naira: creator requests payout; partner converts USDC to naira and pays to the creator's bank account.
- Stablecoin: creator receives USDC to their embedded wallet (hidden by default) or exports to an external wallet.
- Payouts above a set threshold go through admin approval and the partner's KYC checks.

**Wallets**

- Embedded wallet created at sign-up by an embedded-wallet provider (passkey-secured, no seed phrase shown).
- Fans never need to touch it. Creators see a plain balance in naira and USD; an "advanced" toggle reveals the wallet address.

**Ownership**

| Model | What the fan gets | Pros | Cons |
| --- | --- | --- | --- |
| A. Verified receipt | A signed, on-chain record that fan X supported release Y at tier Z | Simple, cheap, no securities question | Not tradable; weaker "ownership" story |
| B. Limited edition token | A transferable token for a numbered edition of a release | Collectible value; matches the original Livebic vision | Transferability invites speculation and SEC scrutiny |
| C. Revenue-sharing token | A token that pays a share of the release's future income | Strongest fan upside | Almost certainly a security under Nigerian law; requires licensing |

Recommendation: launch with A, add B only for selected drops after legal review, and do not build C in this rebuild. This is the key open decision below.

## Discovery and ranking engine

Ranking runs off-chain for speed, but every rule is published and every item can explain its own rank. Weights below are starting values to tune after launch, not fixed.

**Feeds at launch**

| Feed | Purpose | Main signals |
| --- | --- | --- |
| Following | Chronological posts from artists the fan supports or follows | Recency only |
| Rising | New and small artists gaining real support | Supporter growth rate (7 days), paid actions per view, account age under 90 days |
| Most-supported | What the community is paying for this week | Paid support count and value (7 days), unique supporters |
| Newest | Everything, latest first | Recency only |

**Scoring rules**

- Paid actions (tips, unlocks, memberships, drops) weigh more than free actions (plays, likes). One verified supporter counts more than many anonymous plays.
- Diversity cap: no artist occupies more than two of the top 20 slots in Rising or Most-supported.
- Decay: all signals use a 7-day half-life so the feeds refresh weekly.
- Anti-gaming: signals from unverified or newly created accounts are discounted; self-support and circular tipping are excluded; velocity spikes trigger review.

**Transparency**

- A public "How ranking works" page lists each feed's signals and weights, with a dated changelog.
- Every feed item has a "Why this?" link showing its top three scoring factors.
- Weight changes require an admin change record and appear in the changelog before taking effect.

**Implementation**

- Events (play, follow, tip, unlock, purchase) stream into an event store.
- A scoring job recomputes feed scores every 15 minutes and writes them to a cache.
- The feed API reads from cache; the explanation API returns the stored factor breakdown per item.

## Compliance and Nigeria regulatory considerations

The architecture is designed so Livebic is a content platform using licensed partners, not a Virtual Asset Service Provider (VASP) itself. This must be confirmed with Nigerian counsel before build starts.

**What the SEC proposed in August 2026** (consultation closed 3 September 2026; final rules pending)

- Crypto operators must be incorporated in Nigeria, keep a registered Nigerian office, and have a resident CEO ([Nairametrics summary](https://brokerchooser.com/news/nigerias-sec-lays-down-capital-and-fee-rules-for-crypto-operators--c3ecd7de)). Livebic's Lagos entity and CEO already meet this.
- Minimum capital from ₦200 million for VASPs up to ₦2 billion for exchanges and custodians; 80% cold storage for custodians ([KuCoin summary](https://www.kucoin.com/news/flash/nigeria-s-sec-proposes-local-office-and-capital-requirements-for-crypto-firms)).
- Foreign stablecoin issuers need a local representative and must meet reserve and redemption requirements.
- A 2026 Presidential Executive Order established cross-agency oversight of virtual assets, and the SEC's director-general has said Nigeria is open for stablecoin business within a compliant framework ([Nairametrics](https://nairametrics.com/?p=519843)).

**Design choices that keep Livebic outside VASP licensing**

- Custody: a licensed partner holds all stablecoin balances; Livebic holds none.
- Conversion: naira-to-USDC and back is done by the partner, not by Livebic.
- Ownership: launch with verified receipts (model A), not transferable tokens, to avoid securities classification.
- Consumer protection: naira pricing, clear receipts, refund policy, and no investment language anywhere in the product.

**Other obligations**

- Nigeria Data Protection Act 2023: privacy notice, consent for marketing, data stored with an approved processor.
- KYC on creators before first payout (handled by the payout partner); tiered KYC on fans only above a spending threshold.
- Content: music licensing confirmation at upload (creator warrants ownership), takedown process, moderation for prohibited content.
- Tax: creators receive annual statements; platform withholds where required.

* [ ] Engage Nigerian fintech counsel to confirm the partner model keeps Livebic outside the VASP regime.
* [ ] Shortlist licensed processor and payout partners and confirm their SEC/CBN status.
* [ ] Register with the Nigeria Data Protection Commission.

## Success metrics

The north-star metric is monthly creator income paid out through Livebic. Targets are for six months after Phase 1 launch and should be revised once the first cohort is live.

| Metric | Target (6 months) | Why it matters |
| --- | --- | --- |
| Creator income paid out per month | ₦50 million | The platform only matters if creators earn |
| Verified artists with at least one paying fan | 500 | Proves the funnel from onboarding to income |
| Paying fans per artist (median) | 25 | Shows true-fan depth, not vanity reach |
| Payment success rate | 95% | Nigerian payment rails fail often; this is the main UX risk |
| Time from sale to payout available | Under 24 hours | The stablecoin promise made tangible |
| Share of supporters who discovered the artist on Livebic | 20% | Tests whether the open feeds create discovery, not just collection |
| Platform take rate | Under 10% | Keeps the "full value" promise |
| Fraud and chargeback rate | Under 1% of volume | Protects the payout partner relationship |

## Open decisions and risks

Three decisions block the start of build; the risks below need owners before Phase 1.

**Decisions needed**

| Decision | Options | Recommendation | Owner |
| --- | --- | --- | --- |
| Payment and payout partner | Licensed Nigerian processors with stablecoin payout support; shortlist to be built | Pick one partner that handles both naira collection and USDC payout to reduce integrations | CEO |
| Settlement chain | Base, Solana, Polygon (all low-fee, USDC-native) | Choose the chain the selected partner supports best; Meta's pilot uses Solana and Polygon | CTO |
| Ownership model | A verified receipt, B limited-edition token, C revenue-share token | A at launch; B after legal review; not C | Founders + counsel |
| Build team | In-house hire vs. agency vs. AI-assisted founders plus one senior engineer | Prototype with AI tooling, then one senior full-stack engineer for payments and security | Founders |

**Risks**

| Risk | Likelihood | Impact | Mitigation |
| --- | --- | --- | --- |
| SEC final rules classify Livebic as a VASP | Medium | High | Partner-custody model; counsel opinion before build; model A ownership |
| Payment failures on Nigerian rails hurt conversion | High | Medium | Multiple payment methods; retry flows; USSD fallback |
| Cold start: too few fans for artists to earn | High | High | Launch with 20 to 30 committed artists who bring existing fans; clip export from day one |
| Feed gaming by bot accounts | Medium | Medium | Paid-signal weighting; verification discounts; velocity alerts |
| Naira volatility erodes fan spending | Medium | Medium | Naira pricing with USD settlement; diaspora card payments |
| Scope creep from adding creators too early | Medium | Medium | Hold Phase 2 until Phase 1 metrics are met |
