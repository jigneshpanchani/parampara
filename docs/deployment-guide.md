# Deployment Guide

Generated 2026-05-09 via `bmad-document-project` (deep scan).

> **Reality check**: this project has no Dockerfile, no docker-compose, no CI/CD pipelines, no Terraform/Pulumi, no Procfile. Today it deploys **only** to a local Windows + XAMPP environment (`D:\xammp8.1\htdocs\parampara`). This guide documents the as-built setup; productionizing it is an improvement-initiative target.

## As-built deployment

### Environment
- **OS**: Windows 11 (per `os_version` in working environment)
- **Stack**: XAMPP 8.1 (Apache + MariaDB + PHP 8.1)
- **Web root**: `D:\xammp8.1\htdocs\parampara\public` — serves through XAMPP's Apache
- **DB**: MariaDB shipped with XAMPP, configured via `.env` (`DB_HOST=127.0.0.1`, `DB_DATABASE=parampara`)
- **Storage**: `public/storage` symlink → `storage/app/public` (must run `php artisan storage:link` once)

### Manual deploy steps (today)
1. `git pull` on the server box
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build`
4. `php artisan migrate --force`
5. `php artisan optimize:clear` (or `optimize` for prod caches)
6. Restart XAMPP Apache if PHP-OPcache caching is on

### Backups
- Manual SQL dumps to `D:\etc\Batsal\Parampara\DB Backup\`
- "Download DB Backup" button in admin settings ([SettingsController:downloadDbBackup](app/Http/Controllers/Admin/SettingsController.php#L26)) downloads the most recently modified file from that folder
- **The path is hardcoded.** If anyone clones this repo onto a different machine, that endpoint breaks. Move to config/env. See [gaps-and-risks.md](gaps-and-risks.md).

### Pre-destructive-migration backups
By convention, before a destructive migration the developer runs a backup and saves it to repo root as `parampara_pre_<change>_<YYYYMMDD>_<HHMMSS>.sql`. These are NOT committed (gitignored implicitly — verify `.gitignore`). The most recent example: `parampara_pre_expense_drop_20260509_132407.sql`.

## Required configuration

### Environment variables (minimum viable)

```ini
APP_NAME=Parampara
APP_ENV=production            # or local for dev
APP_KEY=base64:...            # php artisan key:generate
APP_DEBUG=false               # MUST be false in production
APP_URL=https://your-host

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parampara
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file           # or database, redis
SESSION_LIFETIME=120

LOG_CHANNEL=daily
LOG_LEVEL=warning             # info/debug too chatty for prod
```

> No `.env.example` is committed. Adding one (with safe defaults, no secrets) is a small improvement.

## Production checklist (when productionizing)

The following are NOT in place today and would be standard for a production deployment:

- [ ] `.env.example` committed
- [ ] CI pipeline (GitHub Actions / similar) running `php artisan test` + `npm run build` on PR
- [ ] Automated DB backups (cron job, not manual) with off-machine retention
- [ ] HTTPS with redirect from HTTP (Apache config)
- [ ] CSP / HSTS / X-Frame-Options security headers
- [ ] Queue worker if any background jobs are added (none today)
- [ ] Log rotation (Laravel `daily` channel + log retention policy)
- [ ] Monitoring (errors → Sentry/Bugsnag/Flare; uptime ping)
- [ ] APP_DEBUG=false confirmed in production
- [ ] DB user with limited privileges (not root)
- [ ] Storage backups (uploaded photos, logos in `storage/app/public/`)
- [ ] Regression test suite expanded to cover business invariants (Sale payment recalc, SaleInvoice 3-FK consistency, StockClosing reconciliation)

## Recommended productionization path

If/when this app needs to live somewhere other than the owner's XAMPP:

1. **Containerize** — Dockerfile (PHP-FPM + nginx + supervisor for queue) + docker-compose for local mariadb. Cleaner than XAMPP-everywhere.
2. **CI** — GitHub Actions: install, test, build, optionally publish image.
3. **Backups** — switch the "download DB backup" to use Laravel's `Storage::disk('backups')` against an environment-configured path (so it works on any host).
4. **Move secrets** — out of `D:\etc\Batsal\...` paths into env config.
5. **Performance work** — see [gaps-and-risks.md](gaps-and-risks.md) for the load-everything-then-filter patterns in `PurchaseController::index` and `ReportController::stock`.

These are NOT prerequisites for the current improvement initiative — they're noted here because someone will eventually ask.
