# Data Models

Generated 2026-05-09 via `bmad-document-project` (deep scan). Authoritative schema lives in `database/migrations/*` and Eloquent model classes under `app/Models/`. This document catalogs the as-built domain.

**Naming note** — schema migration `2026_05_02_140000_rename_sell_to_sale_across_schema.php` renamed `sell*` → `sale*` across the entire schema. New code MUST use `sale*`. Any `sell*` references in app code are bugs left behind by the rename sweep.

---

## Domain entity map (active)

```
Product ─< PurchaseItem      >── Purchase ─< Payment        (purchase-side payments)
        ─< SaleItem          >── Sale     ─< SalePayment    (sale-side follow-up payments)
        ─< PurchaseReturn    >── Purchase
        ─< SaleReturn        >── Sale
        ─< StockClosingItem  >── StockClosing

Sale ─── cash_sale_invoice_id   ──>  SaleInvoice (TYPE_CASH)
Sale ─── online_sale_invoice_id ──>  SaleInvoice (TYPE_ONLINE)
Sale ─── mix_sale_invoice_id    ──>  SaleInvoice (TYPE_MIX)

Expense >── ExpenseCategory (category_id)
CompanyProfile (singleton — branding/GST/contact)
User ─< Spatie Permission tables (Role, Permission, model_has_*)
Document (polymorphic file attachments via entity_id + entity_type)
```

## Tables

### `products`
File: [app/Models/Product.php](app/Models/Product.php)
- `id`, `product_name`, `product_code`, `description`
- `base_price_min` (float), `base_price_max` (float), `selling_price` (float)
- `photo` (path on `public` disk — uploads under `products/`)
- `stock_quantity` (int) — **single source of truth for current stock**
- `is_active` (bool) — toggled via `products.toggle-status` route
- Scopes: `active()`, `orderByName()`, `filterByStatus(string|null $status)`
- Methods: `incrementStock(int)`, `decrementStock(int)`, `hasTransactionHistory()`, `normalizeStatus(?string)`
- Relationships: hasMany `PurchaseItem`, `SaleItem`, `PurchaseReturn`, `SaleReturn`; hasManyThrough `Sale` via `SaleItem`

### `sales`
File: [app/Models/Sale.php](app/Models/Sale.php)
- `id`, `sale_date`, `seller_name`, `seller_contact_number`
- `total_amount`, `amount_paid`, `cash_amount`, `online_amount`, `pending_amount` (all float)
- `payment_mode` (nullable enum: `cash` | `upi` | `gpay` | `mix`) — keys MUST match `config/payment.sale_modes`
- `payment_status` (enum: `paid` | `partial` | `pending`)
- `cash_sale_invoice_id`, `online_sale_invoice_id`, `mix_sale_invoice_id` (nullable FK → `sale_invoices.id`)
- `notes`
- **Invariant**: `pending_amount` is the source of truth; `total_paid` is derived (`total_amount - pending_amount`). Always call `Sale::recalculatePaymentStatus()` after touching `sale_payments` or `amount_paid`.
- Relationships: hasMany `SaleItem`, `SaleReturn`, `SalePayment`; belongsTo three `SaleInvoice` (cash/online/mix)

### `sale_items`
File: [app/Models/SaleItem.php](app/Models/SaleItem.php)
- `id`, `sale_id`, `product_id`, `quantity` (int), `selling_price` (float), `total_price` (float)

### `sale_payments`
File: [app/Models/SalePayment.php](app/Models/SalePayment.php) — follow-up payments for partial-paid sales
- `id`, `sale_id`, `payment_date`, `amount` (decimal:2), `payment_method`, `reference_number`, `notes`
- `payment_method` keys come from `config/payment.sale_payment_methods` (cash/upi/gpay/bank_transfer/cheque/other)

### `sale_invoices`
File: [app/Models/SaleInvoice.php](app/Models/SaleInvoice.php) — daily aggregated invoices, **one per (date, type)**
- Uses `SoftDeletes`
- `invoice_number` (e.g., `SINV-20260509-CASH`), `invoice_date`, `invoice_type` (`cash` | `online` | `mix`)
- `total_amount`, `cash_total`, `online_total`, `sales_count`, `notes`
- **Constants**: `TYPE_CASH`, `TYPE_ONLINE`, `TYPE_MIX` — use these instead of string literals
- Relationship: hasMany `Sale` via dynamic FK based on `invoice_type` (see `invoiceForeignKey()`)
- Static helpers: `aggregateTotalsFromSales`, `lineAmountForInvoiceType`, `totalAmountForSale`, `makeInvoiceNumber`, `loadSalesForDisplay` (queries all 3 FK columns to avoid type/FK desync)
- Soft-delete cascade: `unlinkSalesFromInvoice()` clears all 3 FK columns on related sales

### `sale_returns`
File: [app/Models/SaleReturn.php](app/Models/SaleReturn.php)
- `id`, `sale_id`, `product_id`, `return_date`, `quantity`, `return_price`, `total_return_amount`, `reason`, `notes`
- Logged via `ActivityTrait` (custom `LogsActivity` integration). Log name: `Sale Return`.
- On store/update/destroy: `StockService::addStockFromSaleReturn()` increments product stock_quantity

### `purchases`
File: [app/Models/Purchase.php](app/Models/Purchase.php)
- `id`, `purchase_date`, `supplier_name`, `bill_type` (enum: `gst` | `without_gst`), `bill_details`, `bill_due_date`
- `transportation_cost` (float), `total_amount` (float), `expense` (float), `expense_details`, `status`
- **No DB-stored `payment_status`** — derived live via `Purchase::getPaymentStatus()` from related `payments` (returns `paid` / `partial` / `pending`)
- Computed methods: `getSubtotal()`, `getTotalPayableAmount()`, `getTotalPaidAmount()`, `getRemainingAmount()`, `isFullyPaid()`
- `getBillTypeLabelAttribute()` for display
- Relationships: hasMany `PurchaseItem`, `PurchaseReturn`, `Payment`

### `purchase_items`
File: [app/Models/PurchaseItem.php](app/Models/PurchaseItem.php)
- `id`, `purchase_id`, `product_id`, `quantity` (int), `purchase_price` (float), `total_price` (float)

### `purchase_returns`
File: [app/Models/PurchaseReturn.php](app/Models/PurchaseReturn.php)
- `id`, `purchase_id`, `product_id`, `return_date`, `quantity`, `return_price`, `total_return_amount`, `reason`, `notes`
- Logged via `ActivityTrait`. Log name: `Purchase Return`.
- On store/update/destroy: `StockService::deductStockFromPurchaseReturn()` decrements stock

### `payments` (purchase-side)
File: [app/Models/Payment.php](app/Models/Payment.php)
- `id`, `purchase_id`, `payment_date`, `amount` (decimal:2), `payment_method`, `payment_status`, `reference_number`, `notes`
- `payment_method` keys: `config/payment.purchase_payment_methods` (cash/cheque/bank_transfer/credit_card/other)
- `payment_status` values used in code: `paid`, `pending`, `failed`, `cancelled` (no enum constraint declared on model)
- Helpers: `isPaid()`, `isPending()`, `markAsPaid()`, `markAsPending()`, `getStatusBadgeColor()`, `getPaymentMethodLabel()`
- Logged via `ActivityTrait`. Log name: `Payment`.

### `expenses`
File: [app/Models/Expense.php](app/Models/Expense.php)
- `id`, `expense_date`, `category_id` (FK → `expense_categories`), `amount`, `payment_method`, `notes`
- Migration `2026_05_09_130000` dropped legacy `category` (string) and `description` columns — schema is now category-id-only
- `payment_method` keys: `config/payment.expense_methods` (`Cash` | `G-Pay` | `Online Transfer`) — note **MixedCase enum keys** (not lowercase)
- Logged via `ActivityTrait`. Log name: `Expense`.

### `expense_categories`
File: [app/Models/ExpenseCategory.php](app/Models/ExpenseCategory.php)
- `id`, `name` (unique), `description`
- `canDelete()` blocks delete if expenses exist

### `stock_closings`, `stock_closing_items`
Files: [app/Models/StockClosing.php](app/Models/StockClosing.php), [app/Models/StockClosingItem.php](app/Models/StockClosingItem.php)
- Closing snapshot table — period_label (e.g., `2026-05`), closing_date, notes
- Items hold per-product: opening_stock, purchased_qty, purchase_returns_qty, sold_qty, sale_returns_qty, expected_stock, actual_stock, difference
- **Gap**: `StockClosingController::store` writes ZEROS for opening/expected/sold/purchased fields and only saves `actual_stock`. Reconciliation values are computed for display in `index()` but NOT persisted. See [gaps-and-risks.md](gaps-and-risks.md).

### `stocks` (legacy, likely unused)
File: [app/Models/Stock.php](app/Models/Stock.php)
- Bare model: `product_name`, `quantity`, `unit_price`, `status`
- **NOT linked** to `Product`. Read by `DashboardController::index()` (low-stock count) and `DashboardController::inventory()` (low-stock list, total inventory value), but the rest of the app uses `Product::stock_quantity`. This is **inconsistent state** — the dashboard shows numbers from a different table than the rest of the app. See [gaps-and-risks.md](gaps-and-risks.md).

### `company_profiles`
File: [app/Models/CompanyProfile.php](app/Models/CompanyProfile.php)
- **Singleton** — code reads via `CompanyProfile::first()` everywhere
- `company_name`, `logo`, `favicon_16`, `favicon_32`, `description`, `email`, `phone`, `address`, `website_url`, `gst_number`
- Files stored on `public` disk under `uploads/img/`

### `users`
File: [app/Models/User.php](app/Models/User.php)
- `first_name`, `last_name`, `email`, `contact_no`, `address`, `dob`, `role_id`, `password`, `status`
- Uses `SoftDeletes`, `HasApiTokens` (Sanctum), `HasRoles` (Spatie), `Notifiable`, `ActivityTrait`
- `STATUS_ACTIVE = '1'` (string) — there is a constant; use `User::STATUS_ACTIVE` not raw `'1'`
- **DEAD CODE**: relationships `invoicecreate`, `invoiceby`, `challancreate`, `challanby`, `quotationcreate`, `quotationby` reference `App\Models\Invoice`, `App\Models\Challan`, `App\Models\Quotation` — **none of these classes exist**. `UserController::destroy()` calls these — calling delete-user will fatal. See [gaps-and-risks.md](gaps-and-risks.md).

### `roles` (custom) + Spatie permission tables
Files: [app/Models/Role.php](app/Models/Role.php) + spatie/permission migrations
- Custom `Role` model has only `name` (this is *not* the Spatie role used at runtime — `UserController` and `RoleController` use `Spatie\Permission\Models\Role`)
- Spatie tables: `permissions` (with **custom** `slug` and `module` columns added via migrations `2024_06_29_120229` and `2024_07_02_060502`), `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- Permission slugs are stored as plain strings (e.g., `"List Expense"`, `"Create User"`) — no enum/constant guarding them

### `documents`
File: [app/Models/Document.php](app/Models/Document.php)
- Polymorphic-ish file attachment table: `entity_id`, `entity_type`, `name`, `orignal_name` (typo preserved), `created_by`, `updated_by`
- Uses `SoftDeletes` + `ActivityTrait`. Log name: `Attachment`.

### `notifications`, `activity_log`, `failed_jobs`, `password_reset_tokens`, `personal_access_tokens`
Standard Laravel + Spatie + Sanctum tables. See migrations.

## Cross-cutting traits

### `App\Traits\ActivityTrait`
Used by: User, Document, Expense, Payment, SaleReturn, PurchaseReturn (and possibly others).
Provides activity-log integration with this project's conventions:
- `protected static $logName = '...'` — log channel
- `getLogDescription(string $event)` — HTML-formatted description with strong tags
- `protected static $logAttributes = [...]` — fields to log
Wraps `spatie/laravel-activitylog` v4.

## Money / quantity types

| Field type | DB column type | PHP cast |
|---|---|---|
| Currency | `decimal(10,2)` or `float` | `float` (most models) or `decimal:2` (`SalePayment.amount`, `Payment.amount`) |
| Quantity | `integer` (Sale/Purchase items) or `decimal` (StockClosing) | `integer` / `float` |
| Date | `date` | `date` cast |

**Inconsistency to clean up**: amount columns mix `float` and `decimal:2` casts across models.

## Status / enum value catalog (current as-built)

> Per project rule (see [project-context.md](../parampara/_bmad-output/project-context.md)): these MUST be referenced as model-defined constants in code, not as raw string literals. Several places in the codebase still use raw strings — flagged in [gaps-and-risks.md](gaps-and-risks.md).

| Field | Used on | Valid values | Constant defined? |
|---|---|---|---|
| `Sale.payment_mode` | sales | `cash`, `upi`, `gpay`, `mix`, `null` | ❌ (config keys only) |
| `Sale.payment_status` | sales | `paid`, `partial`, `pending` | ❌ |
| `Purchase.bill_type` | purchases | `gst`, `without_gst` | ❌ |
| `Payment.payment_status` | payments | `paid`, `pending`, `failed`, `cancelled` | ❌ |
| `Payment.payment_method` | payments | per `config/payment.purchase_payment_methods` | ❌ |
| `SalePayment.payment_method` | sale_payments | per `config/payment.sale_payment_methods` | ❌ |
| `Expense.payment_method` | expenses | `Cash`, `G-Pay`, `Online Transfer` (MixedCase!) | ❌ |
| `SaleInvoice.invoice_type` | sale_invoices | `cash`, `online`, `mix` | ✅ `TYPE_CASH/ONLINE/MIX` |
| `User.status` | users | `'1'` (active), other values for inactive | ⚠ Partial — `STATUS_ACTIVE` only |
| `Product.is_active` | products | `true` / `false` | ❌ (boolean) |
