# Parampara — Architecture (As-Built)

Generated 2026-05-09 via `bmad-document-project` (deep scan).

## Executive summary

Parampara is a **single-tenant Laravel 10 + Vue 3 (Inertia) application** for managing day-to-day operations of a small Indian retail/wholesale shop. It tracks products, purchases (from suppliers), sales (to customers), partial-payment workflows on both sides, returns (purchase + sale), expenses, daily aggregated invoices (cash/online/mix), and stock closings.

The owner uses it himself; the system is at ~95% of stated client requirements as of 2026-05-09 and is now entering a **brownfield improvement phase** focused on lists/exports/filters/UX/performance/responsive — all without regressing the existing flows.

The architecture is best described as a **server-rendered MPA with an Inertia escape hatch**: most business UI is Blade-rendered, while a few cross-cutting screens (auth, users, roles, settings, activity log) are Vue 3 SPA pages mounted via Inertia. Both share the same Vite build, the same Tailwind 3 design tokens, and the same auth session.

## Technology stack

| Layer | Tech | Version |
|---|---|---|
| Language | PHP | ^8.1 |
| Framework | Laravel | ^10.10 |
| Inertia (server) | inertiajs/inertia-laravel | ^0.6.8 |
| Routing helper (JS) | tightenco/ziggy | ^1.0 |
| Auth | laravel/breeze + sanctum | 1.24 / 3.2 |
| RBAC | spatie/laravel-permission | ^6.9 (with custom `slug` + `module` columns) |
| Audit log | spatie/laravel-activitylog | ^4.8 |
| Excel | maatwebsite/excel | ^3.1 (also direct PhpSpreadsheet usage) |
| PDF | barryvdh/laravel-dompdf | ^3.0 |
| Frontend framework | Vue | ^3.2 |
| Inertia (client) | @inertiajs/vue3 | ^1.0 |
| Build | Vite | ^4.0 |
| CSS | TailwindCSS | ^3.4 + @tailwindcss/forms |
| Charts | chart.js + vue-chartjs | ^4.4 / ^5.3 |
| Editors | @vueup/vue-quill, @yaireo/tagify | ^1.2 / ^4.33 |
| PDF/canvas (client) | html2canvas + jspdf | ^1.4 / ^2.5 |
| Forms | laravel-precognition-vue-inertia | ^0.5 |
| HTTP | axios | ^1.1 |
| Testing | phpunit | ^10.1 |
| DB | MySQL/MariaDB (XAMPP) | — |

> Floor / ceiling notes are captured in [_bmad-output/project-context.md](../_bmad-output/project-context.md).

## Architectural pattern

**MVC + Service Layer + FormRequest validation**, riding on Laravel conventions. Two non-default twists:

1. **Hybrid rendering** — Inertia/Vue for some screens, Blade for others. The bootstrap in [resources/js/app.js](resources/js/app.js#L11) gates `createInertiaApp` on the presence of `[data-page]` so Blade pages can `@vite([...])` the same JS bundle without booting an SPA. This is **load-bearing** — refactoring the bootstrap to be unconditional breaks every Blade admin page.
2. **Config-driven enums** — `config/payment.php` is the single source of truth for valid `payment_mode`/`payment_method` keys across sales, purchases, expenses, and sale_payments. Code references `array_keys(config('payment.sale_modes'))` instead of hardcoding the list. DB enum values must match config keys exactly.

## Layered structure

```
┌──────────────────────────────────────────────────────────────────────┐
│ Browser                                                              │
│ ├─ Inertia/Vue pages (auth, users, roles, settings, activity)        │
│ └─ Blade pages (admin business modules)  ─── Tailwind via @vite      │
└────────────────────────────┬─────────────────────────────────────────┘
                             │  HTTP (session + CSRF)
┌────────────────────────────▼─────────────────────────────────────────┐
│ Routes                                                               │
│   web.php  + auth.php  + api.php (minimal)                           │
│   Named: admin.<module>.<action> for business; bare for cross-cutting│
└────────────────────────────┬─────────────────────────────────────────┘
                             │
┌────────────────────────────▼─────────────────────────────────────────┐
│ Controllers (thin)                                                   │
│   App\Http\Controllers\Admin\<Module>Controller  (Blade)             │
│   App\Http\Controllers\<Cross-cutting>Controller (Inertia)           │
│   Auth\* (Breeze)                                                    │
│                                                                      │
│   Validation: FormRequest classes in Http\Requests\Admin\            │
└────────────────────────────┬─────────────────────────────────────────┘
                             │
┌────────────────────────────▼─────────────────────────────────────────┐
│ Services (business logic)                                            │
│   SaleService    — total calc, mix-payment resolution, item creation │
│   PurchaseService— total calc, attribute builders                    │
│   StockService   — static stock add/remove from txn types            │
│   ProductService — photo storage helper                              │
└────────────────────────────┬─────────────────────────────────────────┘
                             │
┌────────────────────────────▼─────────────────────────────────────────┐
│ Eloquent Models                                                      │
│   Product, Sale, SaleItem, SalePayment, SaleInvoice, SaleReturn,     │
│   Purchase, PurchaseItem, PurchaseReturn, Payment,                   │
│   Expense, ExpenseCategory, StockClosing, StockClosingItem,          │
│   CompanyProfile, User, Role, Document, Stock(legacy)                │
│                                                                      │
│   Cross-cutting traits: ActivityTrait (logs), QueryTrait (search)    │
└────────────────────────────┬─────────────────────────────────────────┘
                             │
┌────────────────────────────▼─────────────────────────────────────────┐
│ MySQL/MariaDB                                                        │
└──────────────────────────────────────────────────────────────────────┘
```

## Key invariants (must-not-break)

These are the quiet rules that make the system work. Breaking any one of them produces silent data corruption, not a crash.

1. **Sale payment state**
   - `Sale::recalculatePaymentStatus()` is the **only** writer of `payment_status` and `pending_amount` on a Sale. Don't update those columns directly.
   - `pending_amount` is the source of truth; `total_paid` is derived (`total_amount - pending_amount`).
   - `payment_mode` may be null on sale creation; if so, `recalculatePaymentStatus()` auto-fills it from `sale_payments` (single method → that method; multiple methods → `mix`).

2. **Mix payment split**
   - When `payment_mode == 'mix'`, `Sale::cash_amount + Sale::online_amount = Sale::amount_paid`. `SaleService::resolveMixPayment()` enforces this on store/update.

3. **SaleInvoice 3-FK split**
   - One Sale row can be linked to up to 3 daily invoices (cash, online, mix) via 3 separate FK columns: `cash_sale_invoice_id`, `online_sale_invoice_id`, `mix_sale_invoice_id`.
   - `SaleInvoice::sales()` is FK-aware (`hasMany` keyed off `invoiceForeignKey()`).
   - `SaleInvoice::loadSalesForDisplay()` queries all 3 FK columns to defend against type/FK desync (a real bug class observed in this code).
   - Soft-delete cascade: `SaleInvoice::unlinkSalesFromInvoice()` clears all 3 FK columns before deleting.
   - At most ONE invoice of each type per (date) — enforced in [SaleInvoiceController::store](app/Http/Controllers/Admin/SaleInvoiceController.php#L113).

4. **Stock truth**
   - `Product::stock_quantity` is the source of truth for current stock (since migration `2026_01_11_000000_add_stock_quantity_to_products_table`).
   - All purchase/sale/return code paths flow through `StockService::*` static methods which call `Product::incrementStock()` / `decrementStock()`.
   - The legacy `stocks` table + `App\Models\Stock` model are NOT linked to Product. Reading from them gives stale/parallel data. **DashboardController::index() and ::inventory() read from the wrong table** — see [gaps-and-risks.md](gaps-and-risks.md).

5. **Purchase payment state**
   - Unlike Sale, Purchase has no DB-stored `payment_status`. It's computed live via `Purchase::getPaymentStatus()` from the `payments` relation. So filtering by payment_status requires loading all purchases into a Collection and filtering in PHP — see `PurchaseController::index`. This is a performance time-bomb.

6. **Schema rename in flight**: `sell → sale` is mid-sweep. New code must use `sale*`. Search before adding.

## Data architecture

See [data-models.md](data-models.md) for the full schema.

Domain map in one paragraph:
> A **Product** is bought via **Purchase** (containing **PurchaseItem** lines, with **Payment** records on the supplier side and **PurchaseReturn** records when items go back). The same Product is sold via **Sale** (containing **SaleItem** lines, with **SalePayment** records when sales are settled later, **SaleReturn** records when items come back, and links to up to three **SaleInvoice** rows that aggregate per-day sales by payment-mode bucket — cash, online, or mix). **Expenses** are categorized via **ExpenseCategory** and recorded against payment methods. **StockClosing** snapshots periodically reconcile expected vs actual inventory. **CompanyProfile** is a single-row branding/GST table loaded into every admin Blade layout.

## Auth & RBAC

- **Auth**: Laravel Breeze (session + Sanctum tokens). Login redirects to `/admin/dashboard`.
- **RBAC**: `spatie/laravel-permission` v6 with custom `slug` and `module` columns added to the `permissions` table via migrations.
- **Today's reality**: every controller's permission middleware (`$this->middleware('permission:...')`) is **commented out** — see `ExpenseController`, `PaymentController`, `SaleReturnController`, `PurchaseReturnController`, `ExpenseCategoryController`, `UserController`, `RoleController`, `SettingController`. The `auth()->user()->can(...)` checks in some controllers feed UI flags but don't gate execution. **Effectively, any authenticated user can hit any route.** This is the single biggest improvement-initiative target on the security axis.

## API surface

Almost nil — see [api-contracts.md](api-contracts.md). The `/api/` namespace exposes only `/api/logo` (public) and `/api/user` (Sanctum-protected). All other routes are `web` middleware (sessions + CSRF), and several "AJAX" endpoints under `/admin/` return JSON for in-page interactions (sales/purchases payment-details, product toggle-status, etc.).

There is no "API to be consumed by external clients" — it's all internal.

## Frontend architecture

- **Vite 4** as the bundler. Single entry `resources/js/app.js` for both Inertia and Blade pages.
- **Tailwind 3.4** as the styling system. No design tokens / theme config beyond defaults.
- **Vue 3.2** + **`@inertiajs/vue3` 1.0** for SPA pages.
- **Ziggy** generates a `Ziggy` global with route names → URL helpers.
- **`can` plugin** ([resources/js/helpers/can.js](resources/js/helpers/can.js)) wraps permission checks for Vue templates.
- **Charts** via `chart.js` + `vue-chartjs` for dashboard widgets.
- **Quill, Tagify, html2canvas, jspdf** are present in `dependencies` — usage is sparse; verify before assuming they're hot.

### Layouts
- Inertia: `AuthenticatedLayout.vue`, `AdminLayout.vue`, `GuestLayout.vue`
- Blade: [resources/views/layouts/admin.blade.php](resources/views/layouts/admin.blade.php) — single layout for all admin business modules; sidebar inline; uses `CompanyProfile::first()` for branding

### Permission-aware UI
Inertia controllers pre-compute permission flags and pass to pages, e.g.:
```php
$permissions = [
    'canCreateUser' => auth()->user()->can('Create User'),
    'canEditUser'   => auth()->user()->can('Edit User'),
    ...
];
return Inertia::render('User/List', compact('data', 'permissions'));
```
Vue pages then use `v-if="permissions.canCreateUser"`. **Server-side enforcement is missing** (see RBAC note above).

## Testing strategy

- PHPUnit feature tests under `tests/Feature/`, unit tests under `tests/Unit/`
- Coverage today is sparse — Breeze auth tests (Login, Register, ResetPassword, etc.) + a couple of stub feature tests. The only real business-logic coverage is `ProductSalePurchaseTest`.
- Reading the test files reveals that `BusinessTest` and `LocationTest` are scaffolds, not real tests.

For the improvement initiative, **add at least one feature test per module flow** (Sale create → recalc → return → invoice; Purchase create → payment → return; StockClosing snapshot; Expense create with category cascade).

## Deployment architecture

See [deployment-guide.md](deployment-guide.md). TL;DR: Windows + XAMPP, manual deploys, no CI, no Docker, hardcoded D-drive paths. Productionizing is a follow-on, not a blocker.

## Notable strengths (worth preserving)

- **Service layer is consistent for sales/purchases/stock** — controllers stay thin
- **FormRequest validation is consistent for the main write paths** (StoreSaleRequest, StorePurchaseRequest, etc.)
- **Config-driven payment enums** prevent drift between dropdowns and validation
- **Sale payment recalc invariant** is explicit and well-named
- **SaleInvoice 3-FK design** is unusual but solves a real domain problem (one sale day = three invoice flavors) and the model handles the desync defensively
- **DB::transaction wrapping** is consistent across multi-step writes
- **Activity logging** via Spatie + custom trait is wired across business models

## Notable weaknesses (the improvement initiative's targets)

Detailed in [gaps-and-risks.md](gaps-and-risks.md). High-level:

1. **RBAC is wired but not enforced** (commented middleware everywhere)
2. **Lookup-then-filter-in-PHP performance pattern** (`PurchaseController::index`)
3. **Dashboard reads from the legacy `stocks` table** (wrong source of truth)
4. **Status string literals throughout** (the user's stated rule)
5. **Inconsistent FormRequest usage** (Returns/Payments/Stock skip them)
6. **Mixed Modal/Pagination/Dropdown component variants** (consolidation pending)
7. **Sparse test coverage** for business logic
8. **Dead controller code** — `App\Http\Controllers\DashboardController` references nonexistent models (Invoice, Customer, Jobcard, etc.)
9. **`User` model has dead relationships** to `Invoice`/`Challan`/`Quotation` — calling `User::destroy()` will fatal
10. **GET-as-DELETE routes** on `removedocument`, `removeproduct`, etc.
11. **Hardcoded backup path** `D:\etc\Batsal\Parampara\DB Backup\`
12. **No CI / no automated backups / no .env.example**

## Component overview

See [component-inventory.md](component-inventory.md).
