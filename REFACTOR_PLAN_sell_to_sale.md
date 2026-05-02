# Refactor Plan: `sell` → `sale` / `sales` (DB + Code)

> **Status:** Draft for review. Nothing has been executed.
> **Date:** 2026-05-02
> **Goal:** Rename DB tables/columns and all code references from "sell" (verb, used incorrectly as noun) to "sale/sales" (correct nouns), without losing existing data.

---

## 1. Naming Convention Decisions

| Old (current) | New (target) | Reason |
|---|---|---|
| `sells` (table) | `sales` | plural noun, Laravel convention |
| `sell_items` (table) | `sale_items` | "items belonging to a sale" |
| `sell_returns` (table) | `sale_returns` | "return entries for a sale" |
| `sell_invoices` (table) | `sale_invoices` | invoices generated for sales |
| `sell_payments` (table) | `sale_payments` | payments against a sale |
| `sell_id` (FK column) | `sale_id` | singular FK convention |
| `sell_date` | `sale_date` | a single sale's date |
| `sells_count` | `sales_count` | counting many sales |
| `cash_sell_invoice_id` | `cash_sale_invoice_id` | |
| `online_sell_invoice_id` | `online_sale_invoice_id` | |
| `mix_sell_invoice_id` | `mix_sale_invoice_id` | |
| `sell_returns_qty` | `sale_returns_qty` | |
| `products.sell_price` | `products.selling_price` | matches existing `sale_items.selling_price` (consistency fix) |

**Models:** `Sell` → `Sale`, `SellItem` → `SaleItem`, `SellReturn` → `SaleReturn`, `SellInvoice` → `SaleInvoice`, `SellPayment` → `SalePayment`

**Controllers:** `SellController` → `SaleController`, `SellInvoiceController` → `SaleInvoiceController`, `SellReturnController` → `SaleReturnController`

**Form Requests:** `StoreSellRequest` → `StoreSaleRequest`, `UpdateSellRequest` → `UpdateSaleRequest`, `AddSellPaymentRequest` → `AddSalePaymentRequest`, `UpdateSellPriceRequest` → `UpdateSellingPriceRequest`

**Services:** `SellService` → `SaleService`

**Routes / URL paths:**
- `admin.sells.*` → `admin.sales.*` (URL: `/admin/sells` → `/admin/sales`)
- `admin.sell-invoices.*` → `admin.sale-invoices.*` (URL: `/admin/sell-invoices` → `/admin/sale-invoices`)
- `admin.sell-returns.*` → `admin.sale-returns.*` (URL: `/admin/sell-returns` → `/admin/sale-returns`)

**Blade view folders:**
- `resources/views/admin/sells/` → `resources/views/admin/sales/`
- `resources/views/admin/sell-invoices/` → `resources/views/admin/sale-invoices/`
- `resources/views/admin/sell-returns/` → `resources/views/admin/sale-returns/`

**Eloquent relationships (method names):**
- `cashSellInvoice()` → `cashSaleInvoice()`
- `onlineSellInvoice()` → `onlineSaleInvoice()`
- `mixSellInvoice()` → `mixSaleInvoice()`
- `sellInvoice()` → `saleInvoice()`
- `sellItems()` → `saleItems()`
- `sellPayments()` → `salePayments()`
- `sellReturns()` → `saleReturns()`

---

## 2. Strategy & Data Safety

### Approach: Single rename migration (data-preserving)
- Existing migration files are **NOT modified** (they remain as historical record).
- One **new migration** renames tables/columns in-place using `Schema::rename()` and `renameColumn()`.
- These are metadata-only operations — **no row data is touched**.
- All code (models, controllers, views) is updated to reference the new names.

### Backup strategy
- User-provided backup `parampara_20260501.sql` is the safety net.
- If migration fails or rollback needed: drop DB, re-import backup (which has old schema), revert code.

### Restore-from-backup workflow (user's planned approach)
1. After all code changes are committed
2. User drops current DB and imports `parampara_20260501.sql` (old schema with data)
3. User runs `php artisan migrate` — only the new rename migration executes
4. Tables are renamed in-place; data is preserved automatically

### Why not modify existing migrations?
- They've already been executed in production (entries in `migrations` table)
- Modifying them creates inconsistency between code and `migrations` log
- Adding a new migration is the standard Laravel pattern for schema evolution

### Prerequisite check
Laravel 10 requires `doctrine/dbal` for `renameColumn`. Laravel 11+ does not.
- Run `composer show laravel/framework` to check version
- If <11: `composer require doctrine/dbal --dev`

---

## 3. DB Migration File (single new file)

**File:** `database/migrations/2026_05_02_140000_rename_sell_to_sale_across_schema.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop existing foreign keys (must come before column rename + table rename)
        Schema::table('sells', function (Blueprint $table) {
            $table->dropForeign(['cash_sell_invoice_id']);
            $table->dropForeign(['online_sell_invoice_id']);
            $table->dropForeign(['mix_sell_invoice_id']);
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->dropForeign(['sell_id']));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->dropForeign(['sell_id']));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->dropForeign(['sell_id']));

        // 2. Rename columns (while old table names still exist)
        Schema::table('sells', function (Blueprint $table) {
            $table->renameColumn('sell_date', 'sale_date');
            $table->renameColumn('cash_sell_invoice_id', 'cash_sale_invoice_id');
            $table->renameColumn('online_sell_invoice_id', 'online_sale_invoice_id');
            $table->renameColumn('mix_sell_invoice_id', 'mix_sale_invoice_id');
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_invoices', fn(Blueprint $t) => $t->renameColumn('sells_count', 'sales_count'));
        Schema::table('stock_closing_items', fn(Blueprint $t) => $t->renameColumn('sell_returns_qty', 'sale_returns_qty'));
        Schema::table('products',      fn(Blueprint $t) => $t->renameColumn('sell_price', 'selling_price'));

        // 3. Rename tables
        Schema::rename('sells',         'sales');
        Schema::rename('sell_items',    'sale_items');
        Schema::rename('sell_returns',  'sale_returns');
        Schema::rename('sell_invoices', 'sale_invoices');
        Schema::rename('sell_payments', 'sale_payments');

        // 4. Re-add foreign keys with new names
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('cash_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
            $table->foreign('online_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
            $table->foreign('mix_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
        });
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Reverse: drop new FKs, rename tables/columns back, re-add old FKs

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cash_sale_invoice_id']);
            $table->dropForeign(['online_sale_invoice_id']);
            $table->dropForeign(['mix_sale_invoice_id']);
        });
        Schema::table('sale_items',    fn(Blueprint $t) => $t->dropForeign(['sale_id']));
        Schema::table('sale_returns',  fn(Blueprint $t) => $t->dropForeign(['sale_id']));
        Schema::table('sale_payments', fn(Blueprint $t) => $t->dropForeign(['sale_id']));

        Schema::rename('sales',         'sells');
        Schema::rename('sale_items',    'sell_items');
        Schema::rename('sale_returns',  'sell_returns');
        Schema::rename('sale_invoices', 'sell_invoices');
        Schema::rename('sale_payments', 'sell_payments');

        Schema::table('sells', function (Blueprint $table) {
            $table->renameColumn('sale_date', 'sell_date');
            $table->renameColumn('cash_sale_invoice_id', 'cash_sell_invoice_id');
            $table->renameColumn('online_sale_invoice_id', 'online_sell_invoice_id');
            $table->renameColumn('mix_sale_invoice_id', 'mix_sell_invoice_id');
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_invoices', fn(Blueprint $t) => $t->renameColumn('sales_count', 'sells_count'));
        Schema::table('stock_closing_items', fn(Blueprint $t) => $t->renameColumn('sale_returns_qty', 'sell_returns_qty'));
        Schema::table('products',      fn(Blueprint $t) => $t->renameColumn('selling_price', 'sell_price'));

        Schema::table('sells', function (Blueprint $table) {
            $table->foreign('cash_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
            $table->foreign('online_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
            $table->foreign('mix_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
        Schema::table('sell_payments', fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
    }
};
```

---

## 4. Code Files to Update

> Existing migrations (under `database/migrations/`) are **NOT modified**. They remain as historical record.

### 4.1 Models — rename file + class + `protected $table` + relationships + fillable

| File (rename to) | Changes |
|---|---|
| `app/Models/Sell.php` → `app/Models/Sale.php` | Class `Sell` → `Sale`. Relationships: `cashSellInvoice()` → `cashSaleInvoice()`, `onlineSellInvoice()` → `onlineSaleInvoice()`, `mixSellInvoice()` → `mixSaleInvoice()`, `sellPayments()` → `salePayments()`. FK columns: `cash_sell_invoice_id` → `cash_sale_invoice_id`, etc. Fillable: `sell_date` → `sale_date`. |
| `app/Models/SellItem.php` → `app/Models/SaleItem.php` | Class `SellItem` → `SaleItem`. Fillable `sell_id` → `sale_id`. Relationship `sell()` → `sale()` returning `belongsTo(Sale::class)`. |
| `app/Models/SellReturn.php` → `app/Models/SaleReturn.php` | Class `SellReturn` → `SaleReturn`. Fillable + relationships same pattern. |
| `app/Models/SellInvoice.php` → `app/Models/SaleInvoice.php` | Class `SellInvoice` → `SaleInvoice`. Relationship `sells()` → `sales()`. Column refs `sells_count` → `sales_count`. |
| `app/Models/SellPayment.php` → `app/Models/SalePayment.php` | Class + fillable `sell_id` → `sale_id` + relationship. |
| `app/Models/Product.php` | Methods `sellItems()` → `saleItems()`, `sellReturns()` → `saleReturns()`, `sells()` → `sales()`. Fillable `sell_price` → `selling_price`. |
| `app/Models/StockClosingItem.php` | Fillable `sell_returns_qty` → `sale_returns_qty`. |

### 4.2 Controllers — rename file + class + use statements + method calls

| File (rename to) | Changes |
|---|---|
| `app/Http/Controllers/Admin/SellController.php` → `SaleController.php` | Class rename. `use App\Models\Sell;` → `use App\Models\Sale;`. Replace all `Sell::` → `Sale::`, `$sell` → `$sale`, `sell_date` → `sale_date`, route names `admin.sells.*` → `admin.sales.*`. View paths `admin.sells.*` → `admin.sales.*`. |
| `app/Http/Controllers/Admin/SellInvoiceController.php` → `SaleInvoiceController.php` | Class rename. `use App\Models\SellInvoice;` → `use App\Models\SaleInvoice;`. FK columns `cash_sell_invoice_id` → `cash_sale_invoice_id`, etc. Route names + view paths updated. |
| `app/Http/Controllers/Admin/SellReturnController.php` → `SaleReturnController.php` | Class rename. Model use statement updated. Variable + route + view paths updated. |
| `app/Http/Controllers/Admin/ProductController.php` | `UpdateSellPriceRequest` → `UpdateSellingPriceRequest`. Method `updateSellPrice()` → `updateSellingPrice()`. Column `sell_price` → `selling_price`. |
| `app/Http/Controllers/Admin/ReportController.php` | `use App\Models\Sell` → `use App\Models\Sale`. Queries: `Sell::` → `Sale::`, `sell_date` → `sale_date`. |
| `app/Http/Controllers/Admin/DashboardController.php` | Same pattern: model + column references. |
| `app/Http/Controllers/Admin/StockClosingController.php` | `SellItem`, `SellReturn` use statements → `SaleItem`, `SaleReturn`. Column `sell_returns_qty` → `sale_returns_qty`. |

### 4.3 Form Requests — rename file + class + use statements

| File (rename to) | Changes |
|---|---|
| `app/Http/Requests/Admin/StoreSellRequest.php` → `StoreSaleRequest.php` | Class rename. Field `sell_date` → `sale_date`. |
| `app/Http/Requests/Admin/UpdateSellRequest.php` → `UpdateSaleRequest.php` | Same as above. |
| `app/Http/Requests/Admin/AddSellPaymentRequest.php` → `AddSalePaymentRequest.php` | Class rename. |
| `app/Http/Requests/Admin/UpdateSellPriceRequest.php` → `UpdateSellingPriceRequest.php` | Class rename. Field `sell_price` → `selling_price`. |

### 4.4 Services — rename file + class + use statements

| File (rename to) | Changes |
|---|---|
| `app/Services/SellService.php` → `SaleService.php` | Class rename. `use App\Models\Sell, SellItem` → `Sale, SaleItem`. Methods that build sell attributes — internal references updated. |
| `app/Services/StockService.php` | Method names `addStockFromSellReturn()` → `addStockFromSaleReturn()`. Internal refs to old models updated. |

### 4.5 Routes

| File | Changes |
|---|---|
| `routes/web.php` | `Route::resource('sells', SellController::class)` → `Route::resource('sales', SaleController::class)`. Same for `sell-invoices` and `sell-returns`. Update any custom route paths (`/sells/{id}/payment-details`, `/sells/{id}/add-payment`). Use statements `App\Http\Controllers\Admin\Sell*` → `Sale*`. |

### 4.6 Blade Views — rename folders + update references

**Folder renames:**
- `resources/views/admin/sells/` → `resources/views/admin/sales/`
- `resources/views/admin/sell-invoices/` → `resources/views/admin/sale-invoices/`
- `resources/views/admin/sell-returns/` → `resources/views/admin/sale-returns/`

**Per-file content updates** (after folder rename):

| File | Changes |
|---|---|
| `resources/views/admin/sales/index.blade.php` | Route names: `admin.sells.*` → `admin.sales.*`, `admin.sell-invoices.*` → `admin.sale-invoices.*`. Variable `$sell` → `$sale`, `$sells` → `$sales`. Property accesses: `$sell->sell_date` → `$sale->sale_date`, `$sell->cashSellInvoice` → `$sale->cashSaleInvoice`, etc. Loop var `@foreach ($sells as ...)` → `@foreach ($sales as ...)`. Modal labels updated. JS function names + IDs (see Section 4.7). |
| `resources/views/admin/sales/create.blade.php` | Action route `admin.sells.store` → `admin.sales.store`. Field name `sell_date` → `sale_date`. Cancel link route. |
| `resources/views/admin/sales/edit.blade.php` | Action route `admin.sells.update` → `admin.sales.update`. Field `sell_date` → `sale_date`. Variable `$sell` → `$sale`. |
| `resources/views/admin/sales/show.blade.php` | Variable `$sell` → `$sale`. Property accesses + relationship calls updated. Route names. |
| `resources/views/admin/sale-invoices/index.blade.php` | Route names. `$sells_count` references → `$sales_count`. Links to `admin.sells.show` → `admin.sales.show`. |
| `resources/views/admin/sale-invoices/show.blade.php` | Variable `$sellInvoice` → `$saleInvoice`. Loop iterations `$sellInvoice->sells` → `$saleInvoice->sales`. Route refs. |
| `resources/views/admin/sale-invoices/create.blade.php` | Pending sales references (variable names + property accesses). |
| `resources/views/admin/sale-returns/index.blade.php` | `$sellReturn` → `$saleReturn`. Route names. Links to `admin.sells.show` → `admin.sales.show`. |
| `resources/views/admin/sale-returns/create.blade.php` | Form fields `sell_id` → `sale_id`. Display `sell_date` → `sale_date`. |
| `resources/views/admin/sale-returns/edit.blade.php` | Same pattern as create. |
| `resources/views/admin/sale-returns/show.blade.php` | Variable + relationship + route updates. |
| `resources/views/admin/products/_form.blade.php` | Field name `sell_price` → `selling_price`. ID `sell_price` → `selling_price`. |
| `resources/views/admin/products/show.blade.php` | Property access `$product->sell_price` → `$product->selling_price`. |
| `resources/views/admin/products/index.blade.php` | Property accesses. JS function names (see 4.7). Modal IDs (see 4.7). |
| `resources/views/admin/dashboard/index.blade.php` | `$totalSellReturns` → `$totalSaleReturns` (variable from controller). |
| `resources/views/layouts/admin.blade.php` | Route name checks `request()->routeIs('admin.sells.*')` → `admin.sales.*`. Same for `sell-invoices`, `sell-returns`. Route helpers `route('admin.sells.index')` → `route('admin.sales.index')`. |

### 4.7 JavaScript inside Blade views

| File | JS changes |
|---|---|
| `resources/views/admin/sales/index.blade.php` | Function: `openSellPaymentModal` → `openSalePaymentModal`, `closeSellPaymentModal` → `closeSalePaymentModal`, `loadSellPaymentDetails` → `loadSalePaymentDetails`, `addSellPayment` → `addSalePayment`, `showSellPaymentNotification` → `showSalePaymentNotification`. Variable: `currentSellId` → `currentSaleId`, `sellId` → `saleId`. ID: `#sellPaymentModal` → `#salePaymentModal`. Property accesses: `sell.seller_name` → `sale.seller_name`, `sell.sell_date` → `sale.sale_date`. Fetch URLs: `/admin/sells/${id}/...` → `/admin/sales/${id}/...`. Form selectors: `#spAddPaymentForm`, etc. — `sp` prefix can remain (it's "sale payment" abbreviation that still fits) **OR** rename to clarify; recommend keeping `sp` for minimum churn. ID `#spSellDate` → `#spSaleDate`. |
| `resources/views/admin/sales/create.blade.php` | Form selector: `form[action="{{ route('admin.sells.store') }}"]` → `admin.sales.store`. |
| `resources/views/admin/sales/edit.blade.php` | Form selector: `form[action="{{ route('admin.sells.update', $sell) }}"]` → `admin.sales.update`, `$sell` → `$sale`. |
| `resources/views/admin/products/index.blade.php` | Functions: `openSellPriceModal` → `openSellingPriceModal`, `closeSellPriceModal` → `closeSellingPriceModal`, `updateSellPrice` → `updateSellingPrice`. IDs: `#sellPriceModal` → `#sellingPriceModal`, `#sellPriceForm` → `#sellingPriceForm`, `#sellPriceInput` → `#sellingPriceInput`, `#sellPriceProductId` → `#sellingPriceProductId`, `#sellPriceModalTitle` → `#sellingPriceModalTitle`, `#sellPriceModalBaseRange` → `#sellingPriceModalBaseRange`, `#sellPriceError` → `#sellingPriceError`, `#sellPriceSuccess` → `#sellingPriceSuccess`, `#sellPriceSubmitBtn` → `#sellingPriceSubmitBtn`. Class `.sell-price-btn` → `.selling-price-btn`. Data attribute `data-sell-price` → `data-selling-price`. Field name `sell_price` (in FormData) → `selling_price`. Fetch URL `/admin/products/update-sell-price` → `/admin/products/update-selling-price`. |

### 4.8 Other files

| File | Changes |
|---|---|
| `tests/Feature/ProductSellPurchaseTest.php` (rename to `ProductSalePurchaseTest.php`) | Test class + model imports. |
| `.docs/highlight.md`, `MODULE_STATUS.md` | Documentation references — update for accuracy (not load-bearing). |
| `app/Http/Controllers/Admin/ProductController.php` route action — verify path is `update-selling-price` if URL changes |

---

## 5. Step-by-Step Execution Order

**Phase A — Code refactor (in feature branch, no DB touch yet)**
1. Run `composer require doctrine/dbal --dev` if Laravel <11
2. Add new migration file (Section 3)
3. Rename + update all model files (Section 4.1)
4. Rename + update controllers (Section 4.2)
5. Rename + update form requests (Section 4.3)
6. Rename + update services (Section 4.4)
7. Update `routes/web.php` (Section 4.5)
8. Rename Blade folders + update files (Sections 4.6, 4.7)
9. Update layout, dashboard, product views (Section 4.6)
10. Run `composer dump-autoload`

**Phase B — Verification (still on dev, before touching prod DB)**
11. Run `php artisan route:list` — confirm `admin.sales.*` routes exist
12. Run `php artisan migrate --pretend` — review SQL it would run
13. On a **dev copy of the DB**, run `php artisan migrate` and smoke-test all pages

**Phase C — Production deployment**
14. **MANDATORY:** Take a fresh DB backup
15. `php artisan down` (maintenance mode)
16. Pull updated code
17. `composer install --no-dev`
18. `php artisan migrate`
19. `php artisan view:clear && php artisan route:clear && php artisan config:clear`
20. Smoke-test admin login, list pages, create/edit forms
21. `php artisan up`

**Phase D — Restore-from-backup workflow (the one user described)**
1. Drop current DB: `DROP DATABASE parampara; CREATE DATABASE parampara;`
2. Import backup: `mysql parampara < parampara_20260501.sql` — DB now has OLD schema with all data
3. `php artisan migrate` — only the new rename migration runs, renames everything in-place
4. App now works with new code + renamed schema + preserved data

---

## 6. Smoke Test Checklist

After deployment, verify:
- [ ] Login works
- [ ] Dashboard loads (totals show correctly)
- [ ] `/admin/sales` lists all 229 records
- [ ] `/admin/sales/create` form submits successfully
- [ ] `/admin/sales/{id}` show page loads
- [ ] `/admin/sales/{id}/edit` saves changes
- [ ] Pay-later sale records correctly enforce seller name requirement
- [ ] Mix payment validates cash + online > 0
- [ ] `/admin/sale-invoices` lists 5 existing invoices
- [ ] `/admin/sale-invoices/{id}` shows linked sales
- [ ] `/admin/sale-returns` lists existing return record
- [ ] Sale payment modal records new payments
- [ ] Product "Selling Price" quick-update modal works
- [ ] Stock closing report shows `sale_returns_qty` correctly
- [ ] Reports page aggregates by `sale_date`

---

## 7. Rollback Plan

If migration succeeds but app shows errors:
- Code-only rollback: `git revert <merge-commit>` and `php artisan migrate:rollback` (executes the new migration's `down()`)

If migration itself fails partway through:
- DB may be in inconsistent state (some tables renamed, others not, FKs broken)
- Drop DB, restore from backup `parampara_20260501.sql`, revert code

---

## 8. Risk Summary

| Risk | Mitigation |
|---|---|
| Data loss | Migration uses only `Schema::rename()` and `renameColumn()` — metadata only. Backup taken before run. |
| Bookmark-breaking URLs | Routes change from `/sells` to `/sales`. Mitigation option: add `Route::redirect('/admin/sells', '/admin/sales')` etc. |
| Missed code reference | The 56-file audit (above) is the authoritative checklist. After implementation, run `grep -ri "Sell\|sell_id\|sell_date\|sell_price\|sells_count\|cashSellInvoice\|onlineSellInvoice\|mixSellInvoice\|sellPayments\|sellInvoice\|sellItems\|sellReturns" app/ resources/ routes/` to catch stragglers. |
| `doctrine/dbal` missing | Step 1 of execution covers this. |
| FK violations during migration | Order of operations in migration drops FKs before renames, re-adds after. Tested logic. |
| Cache pollution | Step 19 clears Laravel caches post-migration. |

---

## 9. What This Plan Does NOT Change

- `seller_name`, `seller_contact_number` — "seller" is the correct noun for the person; kept as-is.
- `purchases` table and related code — already correctly named.
- `selling_price` on existing `sale_items` table — already correct, no rename needed.
- The historical migration files themselves — they remain on disk as record of past schema.
- Existing entries in `migrations` table — preserved.

---

## 10. Estimated Scope

- **Files touched:** ~56 (8 models + 7 controllers + 4 form requests + 2 services + 1 route file + ~20 blade views + 1 new migration + 1-2 test files + a few config/doc references)
- **Lines changed:** ~400-600 LOC across the codebase
- **Downtime:** 2-5 minutes for DB migration + cache clear
- **Effort:** Half-day of focused work to implement, plus ~1 hour of smoke testing

---

**Ready for review. Reply with approval or requested changes; nothing will be executed until then.**
