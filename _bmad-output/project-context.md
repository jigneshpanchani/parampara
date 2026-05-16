---
project_name: parampara
user_name: "250213"
date: 2026-05-09
sections_completed: ['technology_stack', 'language_rules', 'framework_rules', 'testing_rules', 'quality_rules', 'workflow_rules', 'anti_patterns']
status: complete
rule_count: 38
optimized_for_llm: true
---

# Project Context for AI Agents

_This file contains critical rules and patterns that AI agents must follow when implementing code in this project. Focus on unobvious details that agents might otherwise miss._

---

## Technology Stack & Versions

**Backend**
- PHP `^8.1`, Laravel `^10.10`
- Inertia.js (Laravel side) `^0.6.8`, Ziggy `^1.0`
- Auth: Laravel Breeze `^1.24`, Sanctum `^3.2`
- Authorization: spatie/laravel-permission `^6.9`
- Audit: spatie/laravel-activitylog `^4.8`
- Exports: maatwebsite/excel `^3.1`
- PDF: barryvdh/laravel-dompdf `^3.0`
- HTTP client: guzzlehttp/guzzle `^7.2`
- Dev/Test: phpunit `^10.1`, mockery, fakerphp/faker, doctrine/dbal `^3.10`, laravel/pint, laravel/sail

**Frontend**
- Vue `^3.2.41` + `@inertiajs/vue3 ^1.0.0` (note: also has legacy `@inertiajs/inertia ^0.11.1` dep)
- Vite `^4.0.0` + `laravel-vite-plugin ^0.8.0`
- Tailwind CSS `^3.4.1` + `@tailwindcss/forms`
- UI libs: `@vueup/vue-quill`, `@yaireo/tagify`, `chart.js ^4.4.3` + `vue-chartjs ^5.3.1`, `html2canvas`, `jspdf`
- Forms: `laravel-precognition-vue-inertia`
- HTTP: `axios ^1.1.2`

**Database**
- MySQL/MariaDB (XAMPP local stack on Windows)

## Critical Implementation Rules

### Architecture: Hybrid Inertia + Blade

- **This is a hybrid app, not pure Inertia.** `resources/js/Pages/` covers Auth, Users, Roles, top-level Dashboard, Settings, Activity Log only. The core admin business modules (Sales, Purchases, Products, Sale-Returns, Purchase-Returns, Sale-Invoices, Expenses, Expense-Categories, Payments, Stock, Stock-Closings, Reports) are **Blade views** under `resources/views/admin/<module>/`.
- `resources/js/app.js` guards Inertia bootstrap with `document.querySelector('[data-page]') ?? document.getElementById('app')`. Blade pages `@vite(['resources/css/app.css','resources/js/app.js'])` to get Tailwind compiled CSS without booting Inertia. **Don't break this dual-mount.**
- Admin Blade pages use `resources/views/layouts/admin.blade.php` (sidebar + Tailwind). Inertia pages use `resources/js/Layouts/AuthenticatedLayout.vue` / `AdminLayout.vue`.
- When adding/changing a screen, **match the module's existing pattern** (Blade → Blade, Vue → Vue). Don't migrate piecemeal without an ADR.

### Language-Specific Rules

**PHP (8.1 floor)**
- OK: typed properties, readonly properties, enums, first-class callable syntax, intersection/union types, never return.
- **NOT safe** (>= 8.2): `readonly class`, `null|false|true` standalone types, DNF types — would break the `^8.1` floor.
- Constructor property promotion is the project norm (see `SaleController::__construct(private SaleService $saleService)`).
- Helper file `app/Helpers/BladeHelpers.php` is autoloaded via composer `files`. Add new global helpers there only — don't sprinkle helper files.
- Currency rendering uses `₹` + `number_format($x, 2)`. Use `app/Helpers/CurrencyHelper.php` or the `<x-currency>` Blade component.

**JavaScript / Vue 3**
- Vue 3.2 floor → no `defineModel` macro, no Reactivity Transform. Use `defineProps`/`defineEmits`/`ref`/`computed`.
- Single-file components in `resources/js/Components/` (PascalCase.vue) and pages in `resources/js/Pages/<Module>/<Action>.vue`.
- Composables go in `resources/js/Composables/` (camelCase.js, e.g. `sortAndSearch.js`).
- Inertia: import from `@inertiajs/vue3` only. **Do not import from `@inertiajs/inertia`** (legacy 0.11 dep still in `package.json` but not the active runtime).
- Permission checks in Vue use the `can` directive/helper from `resources/js/helpers/can.js` (registered in `app.js`).
- Routing in JS uses Ziggy: `route('admin.sales.index')`. Keep route names additive — JS depends on them.

### Framework-Specific Rules

**Laravel 10 patterns this project follows**
- **Service layer is mandatory for write paths.** Controllers stay thin; logic lives in `app/Services/<Domain>Service.php` (`SaleService`, `PurchaseService`, `ProductService`, `StockService`). New write logic for Sale/Purchase/Stock/Product **must** go through the corresponding service.
- **Validate via FormRequest only** for create/update. Place under `app/Http/Requests/Admin/Store<Model>Request.php` and `Update<Model>Request.php`. Inline `$request->validate()` is discouraged for write actions.
- Multi-step writes are wrapped in `DB::transaction(...)` (see `SaleController::store`). New code that touches multiple tables must do the same.
- Resource routes under `Route::prefix('admin')->name('admin.')` group. Custom verbs (e.g., `payments.mark-as-paid`, `products.toggle-status`, `purchases.addPayment`) follow `<resource>.<dash-or-camel-action>` naming — keep consistency with neighbors.
- **Config-driven enums (`config/payment.php`) are the single source of truth** for payment_mode/payment_method dropdowns and validation. DB enum keys MUST match config keys exactly. When adding a payment mode/method, update **both** `config/payment.php` and the relevant migration.
- Eloquent scopes are favored: `Product::active()`, `Product::orderByName()`. Add new query scopes on the model rather than inline `where()` chains in controllers.
- `Sale::recalculatePaymentStatus()` is the **invariant guardian** for sale payment state — `pending_amount` is the source of truth, `total_paid` is derived (`total_amount - pending_amount`). Never set `payment_status` directly; always go through this method after touching `sale_payments` or `amount_paid`.
- Spatie Permission v6 is installed with **custom columns** (`slug`, `module`) added via migrations `2024_06_29_120229` and `2024_07_02_060502`. Don't assume vanilla Spatie schema. Activity Log uses `spatie/laravel-activitylog` v4 — log via the `LogsActivity` trait, not manual writes.

**Inertia/Blade layouts**
- `CompanyProfile::first()` is the **branding singleton** loaded in admin Blade layout for logo/favicon/company name. New admin pages should not re-query it; rely on layout-provided context or use a shared composer.
- Pagination uses Laravel's default + Tailwind paginator views in `resources/views/vendor/pagination/tailwind.blade.php`. Keep `->paginate(N)->withQueryString()` so filters survive page nav.

### Testing Rules

- Test runner is **PHPUnit 10.1** (configured in `phpunit.xml`). Tests live in `tests/Feature/` and `tests/Unit/`. Pest is allowed in plugins but **not in use** — don't switch.
- Coverage today is sparse: Breeze auth tests + a few feature tests (`BusinessTest`, `LocationTest`, `ProductSalePurchaseTest`). New features for Sale/Purchase/Stock/Payment **should ship with at least one feature test** covering the happy path through the service layer.
- Use `RefreshDatabase` trait for feature tests touching the DB. Faker is available via `fakerphp/faker`.
- Mocks: project keeps Mockery (`mockery/mockery`) but prefers integration-style feature tests over heavy mocking — match that style.

### Code Quality & Style Rules

- **Formatter:** Laravel Pint (default preset; no project-level `pint.json`). Run before commit if changing PHP.
- **No JS linter configured** (no `.eslintrc`/`.prettierrc` at repo root). Match surrounding Vue/JS style: 4-space indent, single quotes, trailing commas, no semicolons inside template/script tags where existing files omit them. Don't introduce ESLint without an explicit decision.
- **Naming**:
  - PHP: `PascalCase` classes, `camelCase` methods, `snake_case` DB columns, plural snake_case table names.
  - Routes: `admin.<module>.<action>` for admin; bare `<module>.<action>` for cross-cutting (users, roles, profile).
  - Vue Pages: PascalCase folders + files; Components likewise.
  - Blade views: `kebab-case` folders + files (`admin/sale-invoices/show.blade.php`).
- **Defaults**: write no comments unless behavior is non-obvious. Don't add docblocks that just restate the signature.
- **Currency display**: always `₹` + `number_format($x, 2)`. Don't introduce locale-aware formatters without an ADR — the system is single-tenant Indian retail.

### Development Workflow Rules

- Active branch convention seen: `code-improve`, fix-style topic branches. Default branch is `main`.
- Commit style is short imperative ("Fix issue", "Add seeder", "edit payment history"). Match it. No conventional-commit prefix is enforced.
- **Schema rename in flight**: `sell → sale` (migration `2026_05_02_140000_rename_sell_to_sale_across_schema`). New code MUST use `sale*` naming. Old `sell*` references in any new file are a regression.
- DB backups before destructive migrations are taken manually as `parampara_pre_<change>_<timestamp>.sql` at repo root and **must not be committed** (they aren't in git, but verify before staging).
- Vite dev: `npm run dev`. Build: `npm run build`. PHP server: standard XAMPP / `php artisan serve`.

### Critical Don't-Miss Rules

**Anti-patterns / gotchas**
1. **NEVER hardcode status/state string literals.** Use class constants defined on the relevant model — e.g., `Product::STATUS_ACTIVE`, `Sale::PAYMENT_STATUS_PAID`, `Sale::PAYMENT_STATUS_PENDING`, `Sale::PAYMENT_STATUS_PARTIAL`. Inline strings like `'active'`, `'paid'`, `'pending'`, `'partial'` in controllers, services, scopes, validation rules, or views are forbidden going forward. If the constant doesn't exist yet on the model, add it first, then use it. (Existing inline literals — e.g., in `SaleController::index`, `Sale::recalculatePaymentStatus`, `Product::active()` — are tech debt to retire when touching adjacent code.)
2. **Permission middleware is commented out in `ExpenseController::__construct` (and likely other controllers).** RBAC is *wired* (Spatie permission v6 + custom slug/module columns) but **not enforced** on at least the Expense module. Treat any "add permission check" task as an audit task across all `app/Http/Controllers/Admin/*Controller.php` constructors, not a single-file change.
3. **Don't double-write `payment_status`/`pending_amount` on Sale.** Always call `Sale::recalculatePaymentStatus()` after touching payments. Direct updates will desync the invariant.
4. **`payment_mode` keys must match `config/payment.php`.** Adding a new mode requires: config entry, DB enum migration, validation rule reference (`array_keys(config('payment.sale_modes'))`), and any UI dropdown.
5. **Never reintroduce `sell*` naming** (tables, models, routes, blade folders). The rename is mid-sweep — always grep before adding code that resembles old patterns.
6. **Inertia mount node check is load-bearing.** Don't refactor `app.js` to unconditionally `createInertiaApp` — that breaks every Blade admin page.
7. **CompanyProfile is a singleton (`->first()`).** Don't write code assuming multiple rows.
8. **`->paginate()->withQueryString()`** preserves filter params across pagination — keep this on every list view, or filter UX silently breaks.
9. **N+1 risk in list views**: SaleController/ExpenseController eagerly load relationships (`with(['items.product', 'cashSaleInvoice', ...])`, `with('expenseCategory')`). New list views must include eager loads, especially when adding sortable columns that pull from relations.

**Edge cases this project handles specifically**
- Mix payments (Cash + Online): `Sale::cash_amount` + `Sale::online_amount` must sum to `amount_paid`; `SaleService::resolveMixPayment()` computes this.
- Three SaleInvoice flavors per sale (`cash_sale_invoice_id`, `online_sale_invoice_id`, `mix_sale_invoice_id`) — eager-load all three when displaying a sale.
- `payment_mode` is now nullable (migration `2026_05_02_110000`). `Sale::recalculatePaymentStatus()` auto-fills it from sale_payments when single-method or sets `mix` for multi-method.

**Security**
- CSRF token in `<meta name="csrf-token">` (admin Blade layout). Inertia handles automatically; AJAX from Blade pages must include it.
- Sanctum is installed but no API guard usage detected in routes — if adding API routes, choose `auth:sanctum` middleware explicitly and document the choice.
- Storage uploads use `asset('storage/' . $path)` with the `public` disk — `php artisan storage:link` is required on every deploy/clone.
- Permission slugs (Spatie custom column) are referenced as strings (`'List Expense'`, etc.). Avoid typos — there's no enum/constant guarding them today.

**Performance gotchas**
- `ExpenseController::index` clones the query for `sum('amount')` BEFORE pagination — preserve this pattern for any list with aggregate totals; don't naively call `->sum()` after `->paginate()`.
- Default page size `paginate(20)` for sales, `paginate(10)` for expenses — match the existing module when adding new lists, or coordinate a UX-wide change.
- No query caching layer detected. If introducing caching, document invalidation rules per model in this file.

---

## Usage Guidelines

**For AI Agents**
- Read this file before implementing any code in this repo.
- Treat the **Critical Don't-Miss Rules** as hard rules; deviating requires an ADR.
- When a rule is ambiguous in context, prefer the more restrictive option and ask.
- If you discover a new pattern that should be a rule, propose it back to this file.

**For Humans**
- Keep lean. Each rule must justify its line by preventing a specific class of agent mistake.
- Update on stack/version bumps and on completion of the `sell→sale` rename.
- Remove rules that become obvious once the related code is gone (e.g., the legacy `@inertiajs/inertia` dep, the commented-out permission middleware, etc.).
- Re-review after the brownfield improvement initiative (kicked off 2026-05-09) lands.

Last Updated: 2026-05-09


