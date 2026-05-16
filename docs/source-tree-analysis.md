# Source Tree Analysis

Generated 2026-05-09 via `bmad-document-project` (deep scan).

## Top-level

```
parampara/
├── app/                   # Laravel application code
├── bootstrap/             # Framework bootstrap (cache, providers)
├── config/                # Laravel + package configs
├── database/              # Migrations, factories, seeders
├── public/                # Web root, compiled assets
├── resources/
│   ├── css/               # Tailwind entry
│   ├── js/                # Vue 3 + Inertia SPA source
│   └── views/             # Blade templates (admin business modules)
├── routes/
│   ├── web.php            # Main route file (Inertia + admin Blade)
│   ├── auth.php           # Breeze auth routes
│   ├── api.php            # Sanctum API (minimal — 2 routes)
│   └── channels.php
├── storage/               # Laravel storage (logs, cache, uploads)
├── tests/                 # PHPUnit feature + unit tests
├── vendor/                # Composer deps (gitignored)
├── node_modules/          # NPM deps (gitignored)
├── _bmad/                 # BMad installation (this tooling)
├── _bmad-output/
│   └── project-context.md # AI agent rules (from bmad-generate-project-context)
├── docs/                  # ← THIS FOLDER (bmad-document-project output)
├── composer.json          # PHP deps
├── package.json           # NPM deps
├── vite.config.js         # Vite build config
├── tailwind.config.js     # Tailwind config
├── phpunit.xml            # PHPUnit config
└── .env.example           # NOT FOUND — only .env (consider adding example)
```

> No Dockerfile, no `docker-compose.yml`, no `.github/workflows/`, no Procfile, no Terraform. **Local XAMPP only.** See [deployment-guide.md](deployment-guide.md).

---

## `app/` — Laravel application

```
app/
├── Console/
│   └── Kernel.php
├── Exceptions/
│   └── Handler.php
├── Exports/                            # Maatwebsite Excel export classes
│   └── UsersExport.php (referenced)
├── Helpers/
│   ├── BladeHelpers.php                # Autoloaded via composer files
│   └── CurrencyHelper.php
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php              # Base controller (AuthorizesRequests, ValidatesRequests)
│   │   ├── DashboardController.php     # ⚠ DEAD: references nonexistent models (Invoice, Customer, etc.)
│   │   ├── ProfileController.php       # Inertia profile edit
│   │   ├── RoleController.php          # Inertia roles CRUD (uses Spatie Role)
│   │   ├── SettingController.php       # Cross-cutting settings + activity log Inertia
│   │   ├── UserController.php          # Inertia users CRUD
│   │   ├── Auth/                       # Breeze-generated auth controllers
│   │   │   ├── AuthenticatedSessionController.php
│   │   │   ├── ConfirmablePasswordController.php
│   │   │   ├── EmailVerificationNotificationController.php
│   │   │   ├── EmailVerificationPromptController.php
│   │   │   ├── NewPasswordController.php
│   │   │   ├── PasswordController.php
│   │   │   ├── PasswordResetLinkController.php
│   │   │   ├── RegisteredUserController.php
│   │   │   └── VerifyEmailController.php
│   │   └── Admin/                      # ★ Business module controllers (Blade)
│   │       ├── DashboardController.php       # KPI dashboard
│   │       ├── ProductController.php
│   │       ├── PurchaseController.php
│   │       ├── PurchaseReturnController.php
│   │       ├── SaleController.php
│   │       ├── SaleReturnController.php
│   │       ├── SaleInvoiceController.php
│   │       ├── PaymentController.php          # Purchase-side payments
│   │       ├── ExpenseController.php
│   │       ├── ExpenseCategoryController.php
│   │       ├── StockController.php
│   │       ├── StockClosingController.php
│   │       ├── ReportController.php
│   │       └── SettingsController.php          # Company profile + DB backup
│   ├── Middleware/                     # Standard Laravel middleware
│   └── Requests/
│       ├── ProfileUpdateRequest.php
│       ├── UserStoreRequest.php
│       ├── UserUpdateRequest.php
│       ├── Auth/
│       │   └── LoginRequest.php
│       └── Admin/                      # ★ FormRequest classes for write actions
│           ├── StoreProductRequest.php
│           ├── UpdateProductRequest.php
│           ├── UpdateSellingPriceRequest.php
│           ├── StorePurchaseRequest.php
│           ├── UpdatePurchaseRequest.php
│           ├── AddPurchasePaymentRequest.php
│           ├── StoreSaleRequest.php
│           ├── UpdateSaleRequest.php
│           └── AddSalePaymentRequest.php
├── Models/                             # ★ Eloquent models
│   ├── CompanyProfile.php
│   ├── Document.php
│   ├── Expense.php
│   ├── ExpenseCategory.php
│   ├── Payment.php                     # Purchase-side
│   ├── Product.php
│   ├── Purchase.php
│   ├── PurchaseItem.php
│   ├── PurchaseReturn.php
│   ├── Role.php                        # Custom (note: app uses Spatie Role, not this)
│   ├── Sale.php
│   ├── SaleInvoice.php
│   ├── SaleItem.php
│   ├── SalePayment.php
│   ├── SaleReturn.php
│   ├── Stock.php                       # ⚠ Legacy — NOT linked to Product
│   ├── StockClosing.php
│   ├── StockClosingItem.php
│   └── User.php                        # ⚠ References nonexistent Invoice/Challan/Quotation
├── Providers/                          # Empty stubs except RouteServiceProvider
│   ├── AppServiceProvider.php          # No bindings
│   ├── AuthServiceProvider.php         # No policies, no Gates
│   ├── BroadcastServiceProvider.php
│   ├── EventServiceProvider.php
│   └── RouteServiceProvider.php
├── Services/                           # ★ Service layer (business logic)
│   ├── ProductService.php              # Just photo storage helper
│   ├── PurchaseService.php             # Calculations + attribute builders
│   ├── SaleService.php                 # Calculations + payment resolution
│   └── StockService.php                # Static stock add/remove operations
├── Support/                            # Empty (mapped under composer autoload Support\)
└── Traits/
    ├── ActivityTrait.php               # Wraps spatie/activitylog with project conventions
    └── QueryTrait.php                  # searchAndSort helper used by UserController
```

## `database/`

```
database/
├── factories/
├── seeders/
└── migrations/                          # 35 migration files
    ├── 2014_10_*.php                    # Laravel defaults (users, password_reset_tokens)
    ├── 2024_01_30_*.php                 # roles
    ├── 2024_02_22_*.php                 # documents
    ├── 2024_04_06_*.php                 # activity_log + batch_uuid
    ├── 2024_05_20_*.php                 # notifications
    ├── 2024_06_29_*.php                 # spatie permission tables + slug column
    ├── 2024_07_02_*.php                 # spatie permissions module column
    ├── 2025_12_*.php                    # core business schema (stocks, products, purchases, sells, etc.)
    ├── 2026_01_*.php                    # expense schema + category migration + seller fields on sells
    ├── 2026_02_*.php                    # mix payment mode, gpay, bill_type
    ├── 2026_03_*.php                    # sell_invoices + soft deletes + types
    ├── 2026_04_*.php                    # sell_payments
    ├── 2026_05_02_140000_rename_sell_to_sale_across_schema.php   # ⚡ schema-wide rename
    ├── 2026_05_02_*.php                 # is_active on products, payment_mode nullable, stock_closings
    └── 2026_05_09_*.php                 # latest: drop_category_and_description_from_expenses
```

## `resources/js/` — Frontend SPA source

```
resources/js/
├── app.js                  # ★ Inertia bootstrap with mount-node guard (allows Blade pages to share bundle)
├── bootstrap.js            # Axios + CSRF setup
├── ziggy.js                # Generated route table for Ziggy
├── helpers/
│   └── can.js              # Vue plugin for permission checks
├── Composables/
│   └── sortAndSearch.js    # List sorting helper
├── Utils/
│   └── ResponsiveUtils.js
├── Components/             # 47 Vue components — see component-inventory.md
├── Layouts/
│   ├── AdminLayout.vue
│   ├── AuthenticatedLayout.vue
│   └── GuestLayout.vue
└── Pages/                  # 17 Inertia pages — see component-inventory.md
    ├── Dashboard.vue       # ⚠ Possibly dead (legacy Inertia dashboard route not registered)
    ├── Welcome.vue         # ⚠ Default Laravel welcome — likely dead
    ├── Auth/
    ├── Role/
    ├── User/
    ├── Settings/
    └── Activity/
```

## `resources/views/` — Blade templates

```
resources/views/
├── app.blade.php
├── components/
│   ├── currency.blade.php
│   └── delete-confirmation-modal.blade.php
├── layouts/
│   └── admin.blade.php           # ★ The admin layout (used by all business modules)
├── errors/
│   └── 403.blade.php
├── admin/                        # ★ All admin business UI
│   ├── dashboard/
│   ├── expense-categories/
│   ├── expenses/
│   ├── payments/
│   ├── products/
│   ├── purchase-returns/
│   ├── purchases/
│   ├── reports/
│   ├── sale-invoices/
│   ├── sale-returns/
│   ├── sales/
│   ├── settings/
│   ├── stock/
│   ├── stock-closings/
│   └── stocks/
└── vendor/
    └── pagination/               # Tailwind paginator overrides
```

## `config/`

Standard Laravel configs plus:
- `payment.php` — ★ single source of truth for sale/purchase/expense payment modes & methods (DB enum keys must match)
- `permission.php` — Spatie Permission config
- `activitylog.php` — Spatie Activitylog config
- `sanctum.php` — Laravel Sanctum
- `auth.php`, `database.php`, `cache.php`, `queue.php`, `session.php`, `services.php`, `mail.php`, `logging.php`, `filesystems.php`, `app.php`

## `routes/`

```
routes/
├── web.php       # ★ Main routes (Inertia auth + cross-cutting + admin Blade business)
├── auth.php      # Breeze auth routes
├── api.php       # Minimal — /logo + /user only
├── channels.php  # Broadcasting (default)
└── console.php   # Artisan command closures
```

## `tests/`

```
tests/
├── CreatesApplication.php
├── TestCase.php
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   ├── EmailVerificationTest.php
│   │   ├── PasswordConfirmationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── PasswordUpdateTest.php
│   │   └── RegistrationTest.php
│   ├── BusinessTest.php             # Stub
│   ├── ExampleTest.php              # Default Laravel stub
│   ├── LocationTest.php             # Stub
│   ├── ProductSalePurchaseTest.php  # Real coverage of one business flow
│   └── ProfileTest.php
└── Unit/
    └── ExampleTest.php              # Default stub
```

> **Coverage gap**: only `ProductSalePurchaseTest` covers business logic. Sales payments (recalculate invariant), stock closings, sale invoices (3-type FK trick), expense flows, returns — all untested. See [gaps-and-risks.md](gaps-and-risks.md).

## Critical entry points

| Entry | File | Purpose |
|---|---|---|
| HTTP front controller | [public/index.php](public/index.php) | Laravel boot |
| Inertia SPA bootstrap | [resources/js/app.js](resources/js/app.js) | Mounts Vue app if `[data-page]` exists |
| CSS entry | [resources/css/app.css](resources/css/app.css) | Tailwind directives |
| Build config | [vite.config.js](vite.config.js) | Vite + laravel-vite-plugin + vue plugin |
| Tailwind config | [tailwind.config.js](tailwind.config.js) | |
| Auth route table | [routes/auth.php](routes/auth.php) | |
| Main route table | [routes/web.php](routes/web.php) | |
| Admin layout | [resources/views/layouts/admin.blade.php](resources/views/layouts/admin.blade.php) | All admin Blade pages extend this |
| Composer autoload entry | [composer.json](composer.json) | `App\`, `Database\Factories\`, `Database\Seeders\`, `Support\` + `BladeHelpers.php` |

## Critical folders for the improvement initiative

If you're touching list views, sorting, filtering, or exports — these are the files you'll most often edit:

| Concern | Files to touch |
|---|---|
| List views | `resources/views/admin/<module>/index.blade.php` (Blade) + `app/Http/Controllers/Admin/<Module>Controller.php@index` |
| Filters & search | Same pair — every controller `index()` method has its own filter logic |
| Pagination | `->paginate(N)->withQueryString()` calls in controllers + `vendor/pagination/tailwind.blade.php` |
| Exports | `ReportController` (sales + purchases XLSX), `SaleInvoiceController::export` (XLSX), `UserController::exportExcel`, `SettingController::exportExcel`, `app/Exports/UsersExport.php` |
| RBAC | `app/Http/Controllers/Admin/*Controller.php` constructors (currently commented out) + `database/seeders/*` permission seeders |
| Performance | `PurchaseController::index` (loads all then filters in PHP), `ReportController::stock` (recomputes from items), `DashboardController::index` (reads legacy `Stock` table) |
| Responsive UI | `resources/views/layouts/admin.blade.php` (sidebar) + every `index.blade.php` table |
