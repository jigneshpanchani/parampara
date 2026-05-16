# Gaps, Risks & Improvement Targets

Generated 2026-05-09 via `bmad-document-project` (deep scan).

> **Purpose**: this is the prioritized as-built audit punch-list. It feeds directly into the **brownfield improvement initiative** kicked off 2026-05-09. Each finding is sized for severity and mapped to the owner's stated improvement goals.
>
> Constraint inherited from the initiative: **no regressions to existing functionality**. Items marked **HIGH** are either correctness or security risks. Items marked **MEDIUM** are tech-debt with growing cost. Items marked **LOW** are polish.

---

## Severity legend

- **🔴 HIGH** — correctness, security, or near-term failure risk
- **🟡 MEDIUM** — performance / maintainability / inconsistency that will compound
- **🟢 LOW** — polish, dead code, naming inconsistencies

---

## 🔴 HIGH — correctness & security

### G-01. RBAC is wired but not enforced — every controller has commented-out permission middleware

**Where**: every `Admin\*Controller` constructor + cross-cutting `UserController`, `RoleController`, `SettingController`. Example:
```php
// app/Http/Controllers/Admin/ExpenseController.php:14
public function __construct() {
    //$this->middleware('permission:List Expense')->only(['index']);
    //$this->middleware('permission:Create Expense')->only(['create', 'store']);
    ...
}
```

**Impact**: any authenticated user can hit any route. The `auth()->user()->can(...)` calls in some Inertia controllers are passed as UI flags only — they don't gate execution. Spatie permission tables are seeded and Vue UI hides buttons appropriately, but the back-end is open.

**Fix**: uncomment the middleware. Then audit each module — confirm permission slugs exist in seeders. Add a feature test that proves an unauthorized user hits 403.

**Maps to owner goal**: _"Role/permission handling if needed"_ — yes, needed.

---

### G-02. `User` model has dead relationships to nonexistent models — `User::destroy()` will fatal

**Where**: [app/Models/User.php:73-99](app/Models/User.php#L73) declares relationships:
```php
public function invoicecreate()    { return $this->hasMany(Invoice::class, 'created_by', 'id'); }
public function invoiceby()        { return $this->hasMany(Invoice::class, 'sales_user_id', 'id'); }
public function challancreate()    { return $this->hasMany(Challan::class, ...); }
public function challanby()        { return $this->hasMany(Challan::class, ...); }
public function quotationcreate()  { return $this->hasMany(Quotation::class, ...); }
public function quotationby()      { return $this->hasMany(Quotation::class, ...); }
```
None of `App\Models\Invoice`, `App\Models\Challan`, `App\Models\Quotation` exist. [UserController::destroy](app/Http/Controllers/UserController.php#L120) calls all six.

**Impact**: deleting a user will throw `Class "App\Models\Invoice" not found`. Latent bug — only fires on the delete code path.

**Fix**: delete the dead relationships and the corresponding guard logic in `UserController::destroy()`. Replace with a real check (e.g., does the user have any soft-delete-protected related rows?). Or just allow user soft-delete unconditionally if there are no business constraints.

---

### G-03. `App\Http\Controllers\DashboardController` is dead code referencing nonexistent models

**Where**: [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php) — imports `App\Models\Invoice`, `Company`, `Customer`, `Jobcard`, `Orders`, `Quotation`, `Organization`, `PurchaseOrder`, `PurchaseOrderReceives`. None of these classes exist.

**Impact**: not loaded at runtime (no route registers it; admin dashboard uses `Admin\DashboardController`), but the class fails to autoload if anyone instantiates it. Likely a residue from forking another project.

**Fix**: delete the file, plus `resources/js/Pages/Dashboard.vue` if it's the orphaned Inertia page tied to it. Verify no references in seeders or config first.

---

### G-04. Dashboard reads "low stock" / "inventory value" from the wrong table

**Where**: [Admin\DashboardController::index](app/Http/Controllers/Admin/DashboardController.php#L51) and [::inventory](app/Http/Controllers/Admin/DashboardController.php#L120):
```php
$lowStockProducts = Stock::where('quantity', '<', 10)->count();
$lowStockProducts = Stock::where('quantity', '<', 10)->get();
$totalInventoryValue = Stock::sum(\DB::raw('quantity * unit_price'));
```
The `stocks` table is **not** the canonical source — the rest of the app uses `Product::stock_quantity`. So the dashboard "low stock" count and "total inventory value" come from a parallel, unmaintained table.

**Impact**: KPIs on the main dashboard are wrong/stale. Compounds as the Product stock numbers diverge from the legacy Stock table.

**Fix**: rewrite both methods to query `Product` (`Product::where('stock_quantity', '<', 10)->where('is_active', true)`, etc.). After confirming the dashboard is correct, drop the `Stock` model and `stocks` migration in a follow-up cleanup.

---

### G-05. Status string literals scattered through code (the user's stated rule)

**Where**: many places use raw strings instead of model constants:

- [SaleController::index](app/Http/Controllers/Admin/SaleController.php#L43): `in_array($request->payment_status, ['paid', 'pending', 'partial'], true)`
- [Sale::recalculatePaymentStatus](app/Models/Sale.php#L77-L80): `$status = 'pending';`, `'paid'`, `'partial'`
- [Purchase::getPaymentStatus](app/Models/Purchase.php#L101-L111): same `'paid'`/`'partial'`/`'pending'`
- [Purchase::getPaymentStatusColor](app/Models/Purchase.php#L116): same
- [Purchase::getBillTypeLabelAttribute](app/Models/Purchase.php#L131): `'gst'`, `'without_gst'`
- [Payment::isPaid / isPending / markAsPaid / markAsPending](app/Models/Payment.php#L51-L77): `'paid'`, `'pending'`
- [PurchaseController::index](app/Http/Controllers/Admin/PurchaseController.php#L39,L45): `['gst', 'without_gst']`, `['paid', 'partial', 'pending']`
- [PaymentController::store / update](app/Http/Controllers/Admin/PaymentController.php#L48,L86): `'required|in:pending,paid,failed,cancelled'`
- [SaleInvoiceController::store](app/Http/Controllers/Admin/SaleInvoiceController.php#L106): `'required|in:cash,online,mix'` — **these have constants** (`SaleInvoice::TYPE_CASH/ONLINE/MIX`), use them
- [SaleInvoiceController::eligibleSalesQuery](app/Http/Controllers/Admin/SaleInvoiceController.php#L280): `where('payment_mode', 'cash')`, `where('payment_mode', 'mix')`
- [DashboardController::index](app/Http/Controllers/Admin/DashboardController.php#L45): `where('payment_status', '!=', 'paid')`
- [ReportController::index](app/Http/Controllers/Admin/ReportController.php#L22): same
- [ReportController::sales / exportSales](app/Http/Controllers/Admin/ReportController.php#L72,L338): `where('payment_mode', 'cash')`, `'mix'`
- [SettingController::getAllLogs](app/Http/Controllers/SettingController.php#L79): `where(['status' => '1'])` — there's `User::STATUS_ACTIVE` for this

**Fix path** (the right pattern is already in `SaleInvoice` — model-defined `TYPE_CASH`, `TYPE_ONLINE`, `TYPE_MIX`):
1. Add constants on each model:
   - `Sale::PAYMENT_MODE_CASH`, `Sale::PAYMENT_MODE_UPI`, `Sale::PAYMENT_MODE_GPAY`, `Sale::PAYMENT_MODE_MIX`
   - `Sale::PAYMENT_STATUS_PAID`, `Sale::PAYMENT_STATUS_PARTIAL`, `Sale::PAYMENT_STATUS_PENDING`
   - `Purchase::BILL_TYPE_GST`, `Purchase::BILL_TYPE_WITHOUT_GST`
   - `Payment::PAYMENT_STATUS_PAID/PENDING/FAILED/CANCELLED`
   - `Product::STATUS_ACTIVE` (string `'active'`) and `STATUS_INACTIVE` (or keep boolean — but document the choice)
2. Replace literals across the listed sites.
3. Where validation rules need the full list, build it from the constants (e.g., `[Sale::PAYMENT_STATUS_PAID, Sale::PAYMENT_STATUS_PARTIAL, Sale::PAYMENT_STATUS_PENDING]`).

**Maps to user's explicit rule** (saved as `feedback_status_constants.md` memory).

---

### G-06. GET routes that mutate state — `removedocument`, `removeproduct`, etc.

**Where**: [routes/web.php:69-73](routes/web.php#L69):
```php
Route::get('/removedocument/{id}/{name}', ...);
Route::get('/removeproduct/{id}/{model}', ...);
Route::get('/removetransferproduct/{id}', ...);
Route::get('/removechallantransferproduct/{id}', ...);
Route::get('/removeinvoicedetails/{id}', ...);
```

**Impact**:
- GET requests bypass CSRF protection.
- They're idempotent-by-protocol but destructive-by-intent — not REST-safe; preview links, search bots, and prefetchers can fire deletions.
- The names reference legacy concepts (`transferproduct`, `challantransferproduct`, `invoicedetails`) that may not even map to active models.

**Fix**: convert to `Route::delete(...)` with CSRF + permission middleware. Verify each one is still in use; if not, delete.

---

### G-07. `StockClosingController::store` saves zeros for reconciliation values

**Where**: [StockClosingController::store](app/Http/Controllers/Admin/StockClosingController.php#L131-L162) — when saving a closing, the controller writes `actual_stock` from form input but sets `opening_stock`, `purchased_qty`, `purchase_returns_qty`, `sold_qty`, `sale_returns_qty`, `expected_stock`, `difference` all to **0**.

**Impact**: the Stock Closing reconciliation feature is half-implemented. Saved closings show 0s in every reconciliation column. The values ARE computed in `index()` for display, but never persisted on save. So the History view + Show view of a saved closing show zeros only.

**Fix**: persist the same reconciliation data computed in `index()` at save time. This is more than a bug fix — the data flow needs to be:
1. User opens the index for a date range, sees expected vs actual computed live.
2. User clicks "Save Closing" — payload includes the computed numbers, OR the controller recomputes them from purchases/sales/returns within the period and persists.

Verify with the owner whether the snapshot should reflect the current live numbers at save time or the user-edited overrides.

---

### G-08. No `.env.example` committed; hardcoded Windows paths in code

**Where**:
- No `.env.example` at repo root.
- [SettingsController::downloadDbBackup:28](app/Http/Controllers/Admin/SettingsController.php#L28): `$backupPath = 'D:\\etc\\Batsal\\Parampara\\DB Backup\\';`

**Impact**:
- Cloning/onboarding a new dev requires reverse-engineering required env keys.
- The DB backup download endpoint silently breaks on any other machine.

**Fix**:
- Commit `.env.example` with safe defaults and no secrets.
- Move `backupPath` into `config/backup.php` with an env override (`BACKUP_DB_PATH`).

---

## 🟡 MEDIUM — performance, consistency, maintainability

### G-09. `PurchaseController::index` loads ALL purchases, then filters in PHP

**Where**: [PurchaseController::index:43-48](app/Http/Controllers/Admin/PurchaseController.php#L43):
```php
$purchases = $query->get();    // ← all rows
if ($request->filled('payment_status') && ...) {
    $purchases = $purchases->filter(...)->values();  // ← filter in PHP
}
$totalPurchases = $purchases->sum('total_amount');   // ← compute in PHP
```
Plus aggregates by `bill_type` are computed in PHP.

**Why it works today**: `Purchase` doesn't store `payment_status` — it's computed from related `payments` rows. So filtering at SQL level requires a JOIN/subquery.

**Impact**: degrades roughly linearly with purchase count. For a small shop, this might be tolerable for years, but the same pattern appears in `ReportController::getPurchasesForReport` and `ReportController::stock`. As soon as you have 5k+ purchases, page renders slow noticeably.

**Fix path**:
- Persist `payment_status` on `Purchase` (computed and updated whenever payments change — same pattern as `Sale::recalculatePaymentStatus()`).
- Then move filters/sums to SQL.
- Add an index on `purchases.payment_status` and `purchases.bill_type`.

This is a moderately invasive change — schema migration + new method on Purchase + update `Payment` create/update/delete to call it. Worth the work because it unlocks server-side sorting/filtering for the improvement initiative's "Better list views" goal.

---

### G-10. `ReportController::stock` re-aggregates from items — duplicate truth with `Product.stock_quantity`

**Where**: [ReportController::stock:592-610](app/Http/Controllers/Admin/ReportController.php#L592):
```php
$products = Product::with('purchaseItems', 'saleItems', 'purchaseReturns', 'saleReturns')->get();
$stockData = $products->map(function ($product) {
    $totalPurchase = $product->purchaseItems->sum('quantity');
    ...
    $availableStock = $totalPurchase - $purchaseReturn - $totalSales + $saleReturn;
    ...
});
```
Recomputes available stock from line items every time the report is loaded — even though `Product::stock_quantity` is the source of truth and `StockService::*` methods keep it in sync.

**Impact**:
- Eager-loads four large relations across all products → memory and query cost grows linearly with history.
- If `Product::stock_quantity` and the line-item recomputation disagree, the dashboard / sale forms / stock report will silently diverge. There's currently no reconciliation check.

**Fix**: report directly from `Product::stock_quantity`. If you also want a "history reconciliation" view that shows running totals from items, make it a separate report with explicit purpose and period filters.

---

### G-11. `SaleReturnController` / `PurchaseReturnController` / `PaymentController` skip FormRequest classes

**Where**: All three controllers use inline `$request->validate(...)` instead of dedicated `Store<X>Request` / `Update<X>Request` classes — inconsistent with the rest of the codebase (`SaleController`, `PurchaseController`, `ProductController` all use FormRequests).

**Impact**: validation rules are duplicated in store + update methods, harder to test, harder to enforce permission-aware validation. Also, `SaleReturnController` and `PurchaseReturnController` re-declare the same 7 validation rules in store and update.

**Fix**: extract `StoreSaleReturnRequest`, `UpdateSaleReturnRequest`, etc. Same pattern as the existing `Admin/Store*Request.php` classes.

---

### G-12. `SaleInvoiceController::store` validates `invoice_type` as raw `'in:cash,online,mix'`

**Where**: [SaleInvoiceController::store:106](app/Http/Controllers/Admin/SaleInvoiceController.php#L106):
```php
'invoice_type' => 'required|in:cash,online,mix',
```
Should derive from `[SaleInvoice::TYPE_CASH, SaleInvoice::TYPE_ONLINE, SaleInvoice::TYPE_MIX]`. Same for [eligibleSalesQuery:282](app/Http/Controllers/Admin/SaleInvoiceController.php#L282).

**Fix**: replace literals with constants. Trivial.

---

### G-13. `ReportController::exportSales` hardcodes `['upi','gpay','cash','mix']`

**Where**: [ReportController::exportSales:472-482](app/Http/Controllers/Admin/ReportController.php#L472) — hardcoded payment-mode list for the breakdown sections. Should derive from `array_keys(config('payment.sale_modes'))`.

**Impact**: adding a new payment mode requires touching this file separately. Drift hazard.

**Fix**: build the list from config keys; iterate dynamically.

---

### G-14. `SettingController::buildLogQuery` hardcodes a date filter and uses unindexable REGEXP

**Where**: [SettingController:48-54](app/Http/Controllers/SettingController.php#L48):
```php
->whereDate('activity_log.created_at', '>', '2025-02-16')   // hardcoded
...
$q->whereRaw("REGEXP_REPLACE(description, '<[^>]+>', '') LIKE ?", ['%' . $search . '%']);
```

**Impact**:
- Hardcoded date is silently filtering out older logs. Probably a temporary fix that became permanent.
- `REGEXP_REPLACE` over the description column cannot use any index — full table scan as activity_log grows.

**Fix**:
- Move the cutoff to config or remove if no longer needed.
- Either store a cleaned description column on insert, or denormalize searchable fields, or just accept slower search and add UI feedback (loading state) — pragmatically, for a small shop, the table won't get huge.

---

### G-15. Three `SearchableDropdown` variants and two `Modal` / `Pagination` variants

**Where**:
- `Components/SearchableDropdown.vue` + `SearchableDropdownNew.vue` + `SearchableMultiselect.vue`
- `Components/Modal.vue` + `NewModal.vue`
- `Components/Pagination.vue` + `PaginationNew.vue`

**Impact**: each duplicate is a separate bug surface. Visual inconsistencies between screens depending on which variant a developer reached for.

**Fix**: pick one canonical version of each, deprecate the others. Aligns with the owner's "UI/UX enhancements" goal.

---

### G-16. Sparse test coverage for business logic

**Coverage today**: `tests/Feature/ProductSalePurchaseTest.php` is the only real business test. Auth tests cover Breeze. `BusinessTest`, `LocationTest`, `ExampleTest`, `Unit/ExampleTest` are stubs.

**Highest-value tests to add** (sized for the no-regressions constraint):
- `Sale::recalculatePaymentStatus()` invariant — pending_amount / total_paid stay in sync after add-payment, edit-payment, delete-sale flows
- SaleInvoice 3-FK consistency — creating cash/online/mix invoices for the same date doesn't double-count, soft-delete unlinks all 3 FK columns
- Mix-payment validation — `cash + online == amount_paid`, fails when not
- Stock invariant — purchase → stock_quantity increments; sale → decrements; return paths reverse
- Permission middleware enforcement — once G-01 is fixed, lock it down with tests

---

### G-17. Mixed amount casts (float vs decimal:2)

**Where**: most amount columns are cast to `float` on the model (`Sale.total_amount`, `Purchase.total_amount`, etc.) but `SalePayment.amount` and `Payment.amount` cast to `decimal:2`. Inconsistent — float can produce floating-point rounding artifacts on sums.

**Impact**: small-shop scale rarely shows symptoms but will eventually produce `₹1234.5699999` style display bugs. Also makes Excel exports look wrong.

**Fix**: standardize on `decimal:2` casts for all currency columns. Verify all `number_format()` call sites still produce identical output.

---

### G-18. Migrations span Dec-2025 → May-2026 with several "fix the previous migration" entries

**Where**: `database/migrations/` has migrations like:
- `2026_01_01_084422_make_expense_fields_optional.php`
- `2026_01_01_085156_add_payment_method_to_expenses_table.php`
- `2026_01_01_093940_migrate_category_string_to_id.php`
- `2026_05_02_110000_make_payment_mode_nullable_in_sells_table.php`
- `2026_05_09_130000_drop_category_and_description_from_expenses.php`

This is normal evolution but it means a fresh `migrate:fresh` may differ subtly from the "real" production schema. Verify the seeders.

**Fix**: not urgent. Eventually consolidate via a `schema dump` once the rename sweep finishes (`php artisan schema:dump` exists for this exact case).

---

## 🟢 LOW — polish, dead code, naming inconsistencies

### G-19. `resources/js/Pages/Welcome.vue` and `Pages/Dashboard.vue` are likely dead

**Why**: no route renders Welcome (Laravel default replaced by login redirect at `/`). Old `App\Http\Controllers\DashboardController::dashboard` would render `Dashboard.vue` but isn't registered as a route.

**Fix**: confirm + delete.

---

### G-20. `Pages/Auth/Register.vue` AND `Pages/Auth/Registerv2.vue` both exist

**Fix**: pick one; delete the other after confirming which one Breeze's `RegisteredUserController@create` returns.

---

### G-21. `App\Models\Stock` is legacy

**Why**: only used by buggy `Admin\DashboardController` (G-04). Schema-canonical stock lives on `Product::stock_quantity`.

**Fix**: after fixing G-04, drop the model + the `stocks` table migration in a follow-up.

---

### G-22. `App\Models\Role` is unused at runtime

**Why**: app uses `Spatie\Permission\Models\Role` everywhere. The custom `App\Models\Role` is just `name`-only and isn't referenced in active code paths.

**Fix**: confirm, then remove.

---

### G-23. README.md is default Laravel boilerplate

**Fix**: replace with a project-specific README that points to `docs/index.md`.

---

### G-24. `orignal_name` typo in `Document` model fillable + log description

**Where**: [app/Models/Document.php:30,40](app/Models/Document.php#L30) — `orignal_name` instead of `original_name`. Carries through to the DB column (verify migration).

**Fix**: low priority; renaming touches DB. Leave unless there's other reason to migrate the table.

---

### G-25. `App\Helpers\BladeHelpers.php` autoloaded as a `files` entry

**Why**: this is fine, but if it grows beyond a handful of helpers, consider a real service provider with explicit Blade directives.

**Fix**: not yet — flag for revisit when helpers cross ~100 lines.

---

## Mapping to owner's stated improvement goals

| Owner's goal | Findings that address it |
|---|---|
| Better list views | G-09, G-10, G-11, G-15, G-16 |
| PDF/XLS export options | Already implemented in places (`SaleInvoiceController::export`, `ReportController::exportSales/Purchases`); `G-13` improves correctness; consolidating + adding missing exports is straightforward |
| Column sorting | Requires G-09 (server-side filters/sort) |
| Search and filter improvements | G-09, G-14 (activity log search perf), G-13 (config-driven filter dropdowns) |
| UI/UX enhancements | G-15 (component consolidation), G-23 (README), responsive table improvements per module |
| Performance optimization | G-09, G-10, G-14 |
| Responsive design improvements | Per-module audit of `index.blade.php` — every module reimplements its own table; consolidating into a shared list-table partial would fix responsive behavior in one place |
| Role/permission handling | G-01 (the big one), G-06 (CSRF + permission on destructive routes), G-16 (tests for enforcement) |

## Recommended sequencing

When this audit feeds into a PRD + epics:

1. **Epic 0 — Safety net** (do first; un-blocks everything else):
   - G-01 (RBAC enforcement — uncomment + audit + tests)
   - G-02, G-03 (delete dead code that can fatal at runtime)
   - G-06 (GET-as-DELETE → POST/DELETE with CSRF)
   - G-08 (`.env.example` + move backup path to config)
   - Add the smallest feature-test scaffold so we can run regression checks (G-16 starter)

2. **Epic 1 — Status constants sweep** (the user's explicit rule, low-risk, broad reach):
   - G-05 — add constants to all relevant models, replace literals
   - G-12 (use `SaleInvoice::TYPE_*`)
   - G-13 (config-driven mode list in exportSales)

3. **Epic 2 — Lists / sorting / filtering / exports** (the bulk of the owner's goals):
   - G-09 (persist `Purchase.payment_status`)
   - G-10 (report from `Product::stock_quantity`)
   - Build a shared list-table component (Blade partial + Vue equivalent) → use across modules
   - Server-side sort + multi-filter support
   - Standardize XLSX/PDF export options across modules

4. **Epic 3 — Component consolidation + responsive pass**:
   - G-15 (Modal / Pagination / SearchableDropdown unification)
   - Tailwind responsive breakpoints audit on `admin.blade.php` + every `index.blade.php`

5. **Epic 4 — Stock closing reconciliation**:
   - G-07 (persist computed values; verify with owner)

6. **Epic 5 — Test coverage**:
   - G-16 — invariants + permission enforcement

7. **Epic 6 — Tech debt cleanup**:
   - G-04 dashboard fixes
   - G-17 (decimal cast standardization)
   - G-19, G-20, G-21, G-22, G-23, G-24 polish
