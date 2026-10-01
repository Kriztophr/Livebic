-- Livebic Postgres schema (source of truth for users, releases, orders, splits, supporter lists).
-- Money is stored as integer kobo. The sandbox MemoryStore (src/store.ts) mirrors these shapes.

create extension if not exists citext;

create type user_role as enum ('fan', 'artist', 'admin');
create type verification_status as enum ('unverified', 'pending', 'verified', 'rejected');
create type release_access as enum ('public', 'supporters', 'paid');
create type release_status as enum ('draft', 'published', 'removed');
create type support_kind as enum ('tip', 'unlock', 'membership', 'drop');
create type order_status as enum ('pending', 'paid', 'failed', 'refund_required');
create type payout_method as enum ('bank', 'stablecoin');
create type payout_status as enum ('pending_approval', 'submitted', 'rejected');

create table users (
  id text primary key,
  email citext not null unique,
  name text not null,
  role user_role not null default 'fan',
  verified boolean not null default false,
  wallet_id text not null,          -- embedded-wallet provider reference; address never shown to fans
  wallet_address text not null,
  created_at timestamptz not null default now()
);

create table artists (
  id text primary key,
  owner_user_id text not null unique references users(id),
  handle text not null unique check (handle ~ '^[a-z0-9_]{2,30}$'),
  display_name text not null,
  bio text not null default '',
  links text[] not null default '{}',
  verification verification_status not null default 'unverified',
  verification_request jsonb,
  membership_price_kobo bigint check (membership_price_kobo >= 10000),
  payout_method payout_method,
  payout_destination text,
  show_wallet_address boolean not null default false,
  created_at timestamptz not null default now()
);

create table releases (
  id text primary key,
  artist_id text not null references artists(id),
  title text not null,
  description text not null default '',
  lyrics text not null default '',
  access release_access not null default 'public',
  price_kobo bigint check (price_kobo >= 10000),
  edition_size int check (edition_size between 1 and 10000),
  edition_price_kobo bigint,
  edition_sold int not null default 0 check (edition_sold <= edition_size),
  audio_object_key text,
  audio_content_type text,
  audio_sha256 char(64),
  audio_registry_tx text,           -- content-hash registry record (proof of authorship)
  status release_status not null default 'draft',
  rights_warranted_at timestamptz not null,
  created_at timestamptz not null default now(),
  published_at timestamptz,
  check (access <> 'paid' or price_kobo is not null)
);
create index releases_artist_idx on releases (artist_id, published_at desc);

-- Split percentages are set once per release; the application never updates these rows.
create table release_splits (
  release_id text not null references releases(id),
  payee_user_id text not null references users(id),
  role text not null check (role in ('creator', 'collaborator')),
  bps int not null check (bps > 0 and bps <= 10000),
  primary key (release_id, payee_user_id)
);

create table orders (
  id text primary key,
  fan_id text not null references users(id),
  artist_id text not null references artists(id),
  release_id text references releases(id),
  kind support_kind not null,
  amount_kobo bigint not null check (amount_kobo > 0),
  charge_minor bigint not null,
  currency char(3) not null check (currency in ('NGN', 'USD', 'GBP')),
  status order_status not null default 'pending',
  checkout_ref text not null unique,
  settlement_ref text,
  edition_number int,
  membership_ends_at timestamptz,
  receipt jsonb,                    -- signed verified receipt (ownership model A)
  receipt_hash char(64),
  receipt_registry_tx text,
  created_at timestamptz not null default now(),
  paid_at timestamptz,
  unique (release_id, edition_number)
);
create index orders_artist_paid_idx on orders (artist_id) where status = 'paid';
create index orders_fan_idx on orders (fan_id);

create table payouts (
  id text primary key,
  user_id text not null references users(id),
  amount_kobo bigint not null check (amount_kobo > 0),
  method payout_method not null,
  destination text not null,
  status payout_status not null,
  partner_ref text,
  decided_by text references users(id),
  created_at timestamptz not null default now()
);

-- Append-only ledger mirroring the payout partner's balances; balance = sum(amount_kobo).
create table ledger_entries (
  id text primary key,
  user_id text not null,            -- 'platform' for the platform fee
  amount_kobo bigint not null,
  reason text not null check (reason in ('sale', 'platform_fee', 'payout', 'payout_reversal')),
  order_id text references orders(id),
  payout_id text references payouts(id),
  at timestamptz not null default now()
);
create index ledger_user_idx on ledger_entries (user_id);

create table follows (
  user_id text not null references users(id),
  artist_id text not null references artists(id),
  created_at timestamptz not null default now(),
  primary key (user_id, artist_id)
);

-- Engagement events also go to the event stream; this table is the durable copy the ranking job reads.
create table engagement_events (
  id text primary key,
  type text not null check (type in ('view', 'play', 'like', 'follow', 'tip', 'unlock', 'membership', 'drop')),
  actor_id text not null,
  artist_id text not null references artists(id),
  item_id text references releases(id),
  amount_kobo bigint,
  at timestamptz not null default now()
);
create index engagement_events_at_idx on engagement_events (at);

create table ranking_rules (
  version int primary key,
  effective_from timestamptz not null unique,
  summary text not null,
  weights jsonb not null,
  change_id text not null,
  change_author text not null,
  change_reason text not null,
  announced_at timestamptz not null,
  check (version = 1 or effective_from >= announced_at + interval '24 hours')
);

create table reports (
  id text primary key,
  reporter_id text not null references users(id),
  release_id text not null references releases(id),
  reason text not null,
  status text not null default 'open' check (status in ('open', 'actioned', 'dismissed')),
  created_at timestamptz not null default now()
);

create table fraud_flags (
  id text primary key,
  kind text not null,
  item_id text not null,
  artist_id text not null,
  events_in_window int not null,
  baseline_per_window numeric not null,
  status text not null default 'open' check (status in ('open', 'cleared')),
  raised_at timestamptz not null default now()
);

create table idempotency_keys (
  scope text not null,
  user_id text not null,
  key text not null,
  fingerprint char(64) not null,
  status_code int not null,
  body jsonb not null,
  created_at timestamptz not null default now(),
  primary key (scope, user_id, key)
);
