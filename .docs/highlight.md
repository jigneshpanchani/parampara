# Parampara – Inventory Management System

**Review Highlights**

---

## Overview

Parampara is a small inventory management system built with Laravel 10, Inertia.js (Vue), and Blade. It supports product management, purchases, sales, returns, payments, expenses, and reporting.

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 10, PHP 8.1+ |
| Frontend | Inertia.js, Vue 3, Blade |
| Auth | Laravel Breeze, Sanctum |
| RBAC | Spatie Laravel Permission |
| Auditing | Spatie Laravel Activity Log |
| Export | Maatwebsite Excel, DomPDF |

---

## Core Features

### 1. Product Management
- CRUD with product code, base price min/max, sell price
- Stock quantity stored on `Product` model
- Stock status: `in_stock`, `low_stock`, `out_of_stock` (threshold: 10)
- AJAX sell-price update endpoint
- Products with sales history cannot be deleted; orphan products can

### 2. Purchases
- Multi-item purchases linked to products
- Bill types: GST / non-GST
- Transportation cost, bill due date, extra expense fields
- Payment tracking with installments (`Payment` model)
- Payment status: pending, partial, paid
- Purchase returns with stock adjustment

### 3. Sales (Sells)
- Multi-item sales
- Payment modes: cash, UPI, GPay, mix (cash + online)
- Cash and online amounts tracked separately for mix
- Payment status, amount paid, pending amount
- Seller name and contact
- Sell returns with stock restoration

### 4. Stock Management
- `StockService` centralizes stock logic
- Purchase → increment stock
- Sale → decrement stock
- Purchase return → decrement
- Sell return → increment
- Stock summary: total products, in-stock, low-stock, out-of-stock counts and total value

### 5. Expenses
- Expense categories
- Date, amount, payment method, notes
- Activity logging for changes

### 6. Reports
- **Sales**: date range, payment mode, cash/online/mix breakdown, product-wise quantity
- **Purchases**: list with totals
- **Stock**: purchase/sell/return totals and available stock per product
- **Excel export** for sales (product columns, cash/online, returns, expenses)

### 7. Dashboard
- Main: sales, purchases, expenses, returns, profit, low stock
- Financial: profit margin and totals
- Inventory: product count, low/out-of-stock, inventory value

### 8. User & Access Control
- Roles and permissions (Spatie)
- User CRUD, activation, Excel export
- Activity logging for users and expenses
- Company profile (logo, favicon, name)

### 9. Settings
- Number/prefix settings
- Database backup
- Password update

---

## Architecture Highlights

| Aspect | Implementation |
|--------|----------------|
| Stock Logic | `StockService` with static methods for add/remove stock |
| Model Relationships | Product ↔ PurchaseItem, SellItem, PurchaseReturn, SellReturn |
| Payment Calculation | `Purchase::getTotalPayableAmount()`, `getRemainingAmount()`, `getPaymentStatus()` |
| Trait Usage | `CommonTrait`, `QueryTrait`, `ActivityTrait`, `FileUploadTrait` |
| DTOs | `UserDTO` for structured user data |
| Excel Export | Direct PhpSpreadsheet usage with dynamic product columns |

---

## Testing

- `ProductSellPurchaseTest` covers:
  - Product index, create, delete (with/without sales)
  - Sell index, create, stock deduction
  - Purchase index, create, stock increment
  - AJAX sell-price update

---

## Database Schema Overview

| Table | Purpose |
|-------|---------|
| products | Product catalog with stock_quantity |
| stocks | Legacy/standalone stock table (product_name, quantity, unit_price) |
| purchases, purchase_items | Purchase orders |
| sells, sell_items | Sales |
| purchase_returns, sell_returns | Returns |
| payments | Purchase payment installments |
| expenses, expense_categories | Expense tracking |
| company_profiles | Logo, favicon, company name |
| roles, permissions, model_has_roles | Spatie RBAC |
| activity_log | Spatie activity log |

---

## Observations & Suggestions

### Strengths
- Clear separation of purchase, sell, and return flows
- Centralized `StockService`
- Payment tracking with partial payments
- Reporting and Excel export
- RBAC and activity logging
- Tests for core product/sell/purchase flows

### Potential Improvements
1. **Stock vs Product**: `Stock` table uses `product_name` (no `product_id`), while `Product` has `stock_quantity`. Dashboard uses both. Consider unifying: either drop `stocks` or sync it with products.
2. **Dashboard low stock**: `Stock::where('quantity', '<', 10)` may not match product-level stock. Consider `Product::where('stock_quantity', '<', 10)` for consistency.
3. **User model**: References `Invoice`, `Challan`, `Quotation` that may not exist. Remove or implement.
4. **Transaction safety**: Use DB transactions for purchase/sell create/update/delete to keep stock and financial data consistent.
5. **API/Inertia**: Some routes use Blade views (e.g. `admin.products.index`); others may use Inertia. Consider standardizing on Inertia for a consistent SPA UX.
6. **Excel export**: Uses `streamDownload` with inline PhpSpreadsheet; consider using Maatwebsite Excel export classes for maintainability.

---

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed  # if seeders exist
php artisan serve
```

---

*Generated from codebase review – Parampara Inventory Management*
