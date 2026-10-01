# Retiring the PHP app (DeepSound → Livebic)

The 2022 site in `Script/` is DeepSound, a PHP script bought from CodeCanyon, backed by MySQL. This runbook
moves its users, artists, catalogue and history into the new build once, then switches the PHP app off.

## What moves, what doesn't

| DeepSound | Livebic | Notes |
| --- | --- | --- |
| `users` | accounts | One per email (lowest id wins on duplicates; users with no valid email are skipped). Passwords carry over: PHP `password_hash()` bcrypt hashes are checked as-is. Sign-in accepts the old username or email. |
| `users.artist`, `users.verified` | artist pages | Handle from the username (`Tobi.Lagos` → `tobi_lagos`). Old verified badge → verified. Anyone who uploaded songs also gets an artist page. Payout details are **not** copied: artists re-enter them and pass the payout partner's KYC. |
| `songs` | releases | Public → published, private → draft. Price > 0 → paid unlock, converted to naira at the rate you pass (minimum ₦100). Audio and cover art are copied and fingerprinted; a release whose audio file is missing comes in as a draft. |
| `followers` | follows | Only follows of artists. |
| `likes`, `views` | ranking history | Feeds rank the old catalogue from day one. |
| `purchases` (tracks) | paid orders | Fans keep access to what they bought, and they appear in the artist's supporter list. **Not** credited to anyone's balance: that money was already settled in DeepSound. |
| `users.balance`, `users.wallet`, pending `withdrawal_requests` | — | **Not imported.** Listed in `settlement.csv` to pay out or refund before cutover. |
| albums, playlists, comments, blog, events, store, messages, stories, ads | — | No Livebic equivalent yet. Counted in the report. Kept in the archived dump. |

Old links keep working after the switch: `/track/<audio_id>` and `/<username>` go to the new pages with a
permanent redirect, and `/login`, `/signup`, `/discover` and `/new-releases` go to their new equivalents.

## Running the import

The importer only reads from MySQL. Give it a read-only user, ideally on a copy of the database.

```sh
npm run migrate:deepsound -w @livebic/api -- \
  --mysql mysql://readonly:PASSWORD@HOST:3306/DBNAME \
  --media /path/to/Script            # or the URL uploads are served from, e.g. https://livebic.example
  --out ./deepsound-export \
  --ngn-per-unit 1550                # only needed if the old site priced in a currency other than NGN
```

`--media` is the folder that contains `upload/` (the old `Script/` folder on the server), or the base URL
the files are served from if the site used S3, Spaces, Wasabi or FTP storage.

It writes three files to `--out`:
- `bundle.json` plus `media/`: the data to load. Loading is idempotent, so you can re-run it.
- `report.md`: counts, everything skipped and why, missing files, prices raised to the minimum.
- `settlement.csv`: every account still holding money in the old system.

To look at the result locally: `LIVEBIC_IMPORT_BUNDLE=./deepsound-export npm run dev:api`.

## Cutover

**Do not start until these are done.** They are the launch blockers in `docs/ARCHITECTURE.md`:
- [ ] The Postgres-backed store. Today the API keeps data in memory, so a production import would not survive a restart.
- [ ] A real payment processor, payout partner and wallet provider, and counsel sign-off.
- [ ] Production hosting for `apps/api` and `apps/web`. The current Azure workflow only deploys PHP.
- [ ] Email-link or passkey sign-in, for users the report lists as needing a password reset.

Then:
1. **Dry run.** Run the import against a copy of production. Read `report.md` and fix anything you can at the source (missing files, duplicate accounts).
2. **Stop new money in DeepSound.** In its admin panel, turn off purchases, wallet top-ups, pro upgrades and ads. Announce the switch date to artists.
3. **Settle `settlement.csv`.** Pay out artist balances and pending withdrawals, and refund or spend down fan wallet credit. Keep a record of each settlement.
4. **Freeze.** Put DeepSound into maintenance mode, so no writes happen after the final export.
5. **Final import.** Re-run the importer and load the bundle into production.
6. **Switch DNS** to the new web app. Check that a few old `/track/...` and `/<username>` links redirect correctly.
7. **Tell users.** The same username and password work. Users flagged in the report reset their password by email. Artists add payout details and complete KYC.
8. **Archive, then delete.** Keep a read-only MySQL dump and the `upload/` folder for disputes and tax records (at least 6 years for financial records; confirm with your accountant). Then:
   - delete `Script/` and `.github/workflows/main_livebic.yml` from the repo;
   - shut down the Azure PHP App Service and its MySQL database.

Step 8 is deliberately last. Until step 6, the PHP app is the live site. Deleting `Script/` or its workflow
on `main` before then would take the site down.

## Before anything else merges to `main`

`Script/config.php` in the repo has no database settings and redirects to `./install`. The Azure workflow
deploys the whole repo on every push to `main`. If the live site's working `config.php` only exists on the
server, the next push to `main` could replace it with this one. Check how production gets its config before
merging.
