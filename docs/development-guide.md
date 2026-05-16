# Development Guide

Generated 2026-05-09 via `bmad-document-project` (deep scan).

## Prerequisites

- **PHP 8.1+** (project floor; do not assume 8.2+ syntax)
- **Composer 2.x**
- **Node.js 18+** with npm (Vite 4 + Vue 3.2 dependencies)
- **MySQL 5.7+ / MariaDB 10.3+** (XAMPP-shipped MariaDB is fine)
- **Local web server** — XAMPP/Laragon on Windows or `php artisan serve` works for development
- A **public storage symlink** is required for product photos / company logo / favicons (see "Storage" below)

> No Docker / Compose setup is provided. The `D:\xammp8.1\htdocs\parampara` path in the working directory + the `'D:\\etc\\Batsal\\Parampara\\DB Backup\\'` hardcoded path in [SettingsController](app/Http/Controllers/Admin/SettingsController.php#L28) imply this is a Windows-XAMPP-only deployment today.

## First-time setup

```bash
# 1. Install PHP deps
composer install

# 2. Install JS deps
npm install

# 3. Copy env (no .env.example committed — copy from a teammate or create one)
# Required keys at minimum:
#   APP_NAME, APP_ENV, APP_KEY, APP_URL, APP_DEBUG
#   DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#   SESSION_DRIVER, SESSION_LIFETIME

# 4. Generate app key
php artisan key:generate

# 5. Run migrations + seeders
php artisan migrate --seed

# 6. Symlink storage (required for product photos and uploaded logos to load)
php artisan storage:link
```

> **Note**: there is no `.env.example` committed. Adding one is a small but high-value improvement. See [gaps-and-risks.md](gaps-and-risks.md).

## Running the app

```bash
# Terminal 1: Vite dev server (HMR for Vue + Tailwind)
npm run dev

# Terminal 2: Laravel app server (or use XAMPP virtual host pointing to /public)
php artisan serve
```

Open `http://localhost:8000` (or your XAMPP host). You'll be redirected to `/login`.

## Production build

```bash
npm run build      # Compiles assets to public/build/
```

`npm run watch` rebuilds on change without dev-server (useful when serving from XAMPP and you don't want HMR).

## Database management

```bash
# Run pending migrations
php artisan migrate

# Roll back last batch (dev only)
php artisan migrate:rollback

# Wipe + re-migrate + re-seed (destructive — dev only)
php artisan migrate:fresh --seed

# Inspect schema
php artisan db:show
php artisan db:table sales
```

> **Schema-rename caution**: migration `2026_05_02_140000_rename_sell_to_sale_across_schema` is part of an active rename sweep. If you create a new migration, never reintroduce `sell*` naming.

## Backups

A SQL backup is taken before destructive migrations and saved at the repo root with the convention `parampara_pre_<change>_<YYYYMMDD>_<HHMMSS>.sql`. These are gitignored — verify before staging. Manual backup process today; consider automating (see [gaps-and-risks.md](gaps-and-risks.md)).

The "Download DB Backup" button in admin settings reads from the hardcoded Windows path `D:\etc\Batsal\Parampara\DB Backup\` and serves the most recently modified file. This won't work on any other machine.

## Common dev commands

| Task | Command |
|---|---|
| List all routes | `php artisan route:list` |
| Tinker (REPL) | `php artisan tinker` |
| Clear caches | `php artisan optimize:clear` |
| Dump-autoload | `composer dump-autoload` |
| Format PHP | `./vendor/bin/pint` (Laravel Pint, default preset) |
| Run all tests | `php artisan test` (or `./vendor/bin/phpunit`) |
| Run one test | `php artisan test --filter=ProductSalePurchaseTest` |

## Testing

- Test runner: **PHPUnit 10.1** (config in [phpunit.xml](phpunit.xml))
- Pest is allowed in plugins but **not in use** — don't switch
- Test directories: `tests/Feature/`, `tests/Unit/`
- Use `RefreshDatabase` trait for tests touching the DB
- Faker available via `fakerphp/faker`
- Mocks: Mockery is installed; project prefers integration-style feature tests
- **Coverage today is sparse** — Breeze auth tests + a couple feature-test stubs. See [gaps-and-risks.md](gaps-and-risks.md).

When adding a feature, ship at least one feature test that drives it through the service layer (`SaleService`, `PurchaseService`, etc.).

## Code style

- **PHP**: Laravel Pint (default preset, no project-level `pint.json`). Run before commit.
- **JS/Vue**: no ESLint/Prettier configured at repo root. Match surrounding style — 4-space indent, single quotes, trailing commas.
- **Comments**: project default is to write none unless the WHY is non-obvious. Don't add comments that just restate code.

## Project conventions

- **Service layer** for write paths — controllers stay thin; logic lives in `app/Services/<Domain>Service.php`
- **FormRequest validation** for create/update — under `app/Http/Requests/Admin/Store<Model>Request.php` and `Update<Model>Request.php`
- **Multi-step writes** wrapped in `DB::transaction(...)`
- **Resource routes** under `Route::prefix('admin')->name('admin.')` group
- **Status/state values** must use model-defined constants, never string literals (`Sale::PAYMENT_STATUS_PAID`, `SaleInvoice::TYPE_CASH`, etc.)
- **Currency**: always `₹` + `number_format($x, 2)`
- **Pagination**: always `->paginate(N)->withQueryString()` so filter params survive page nav
- **Eager loading**: list views must include `with([...])` to avoid N+1; see existing examples in `SaleController::index`, `ExpenseController::index`

See [_bmad-output/project-context.md](../_bmad-output/project-context.md) for the full rule set used by AI agents.

## Hybrid Inertia/Blade — when to use which

- **Inertia (Vue 3 SPA)** — auth pages, users, roles, settings, top-level dashboard, activity log
- **Blade (server-rendered)** — every admin business module (sales, purchases, products, expenses, etc.)

`resources/js/app.js` checks for `[data-page]` mount node so Blade pages can `@vite([...])` the same JS bundle without booting Inertia. **Don't break this guard.** Match the module's existing pattern when adding screens.

## Vue / Inertia conventions

- Import only from `@inertiajs/vue3`. **Don't** import from `@inertiajs/inertia` — that's a stale legacy dep still in `package.json`.
- Permission checks in Vue: use the `can` helper registered in [resources/js/app.js](resources/js/app.js) and defined in [resources/js/helpers/can.js](resources/js/helpers/can.js).
- Routes in JS: use Ziggy (`route('admin.sales.index')`).

## Helpers / globals

- `app/Helpers/BladeHelpers.php` is autoloaded via composer `files` (see [composer.json:39](composer.json#L39)). New global helpers go here only.
- `app/Helpers/CurrencyHelper.php` — currency formatting helper used by some views (the `<x-currency>` Blade component is preferred for new code).
