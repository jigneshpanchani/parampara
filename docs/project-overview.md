# Parampara — Project Overview

Generated 2026-05-09 via `bmad-document-project` (deep scan).

## What it is

**Parampara** is a small-shop **stock & sales management system** built by the owner for a single Indian retail/wholesale business. It tracks the day-to-day operations:

- **Products** (with photo, base/selling price, GST-aware, active/inactive toggle)
- **Purchases** from suppliers (with bill type GST/non-GST, transportation cost, multi-payment partial settlement)
- **Sales** to customers (cash / UPI / G-Pay / mix payment modes; partial payments via `sale_payments`)
- **Returns** on both sides (sale returns + purchase returns)
- **Daily aggregated invoices** — one invoice per (date, payment-mode-bucket): cash invoice, online invoice, mix invoice
- **Expenses** with categories
- **Stock closings** — periodic reconciliation of expected vs actual
- **Reports** — sales report, purchases report, stock report (with XLSX export)
- **Activity log** — audit trail of business changes via Spatie Activitylog
- **Settings** — company profile (logo, GST number, contact, favicon), user management with Spatie Permission RBAC

## Project type

| Property | Value |
|---|---|
| Repository type | **Monolith** |
| Project type (BMad classification) | **`web`** (fullstack web app) |
| Architecture pattern | MVC + Service Layer + FormRequest validation |
| Rendering | Hybrid Inertia/Vue + server-rendered Blade |
| Auth | Session-based (Breeze) + Sanctum tokens for API |
| Single tenant | Yes — built for one business |

## Tech stack summary

| Layer | Stack |
|---|---|
| Backend | PHP 8.1, Laravel 10, Inertia 0.6.8 |
| Frontend | Vue 3.2 + Vite 4 + Tailwind 3.4 |
| Auth | Laravel Breeze, Sanctum, Spatie Permission v6 |
| Audit | Spatie Activitylog v4 |
| Exports | Maatwebsite Excel 3.1, PhpSpreadsheet (direct), Barryvdh DomPDF 3 |
| Charts | Chart.js 4 + vue-chartjs 5 |
| Editors | Vue-Quill, Tagify, html2canvas, jspdf |
| DB | MySQL/MariaDB (XAMPP local) |

## Repository structure

```
parampara/
├── app/                  # Laravel application code (controllers, models, services, requests)
├── routes/               # web.php (main), auth.php (Breeze), api.php (minimal)
├── resources/
│   ├── js/               # Vue 3 SPA source (Inertia pages + components)
│   └── views/            # Blade templates (admin business modules)
├── database/migrations/  # 35 migrations (active sell→sale rename in flight)
├── config/payment.php    # Single source of truth for payment-mode enums
├── tests/                # PHPUnit (sparse — auth + 1 real business test)
├── public/               # Web root + compiled assets
├── _bmad-output/
│   └── project-context.md  # AI agent rules
└── docs/                 # ← This documentation set
```

See [source-tree-analysis.md](source-tree-analysis.md) for the detailed breakdown.

## Status (as of 2026-05-09)

- **~95% complete** to client requirement (per owner)
- Active branch: `code-improve`
- Schema rename in flight: `sell → sale` everywhere (migration `2026_05_02_140000`)
- Active improvement initiative kicked off **2026-05-09**: better lists / exports / sorting / search / UX / performance / responsive / RBAC enforcement, with a hard "no regressions" constraint.

## Documentation set

| Document | Purpose |
|---|---|
| [architecture.md](architecture.md) | How the system is built, key invariants, layered structure |
| [data-models.md](data-models.md) | All 19 models, schema, relationships, status enums |
| [api-contracts.md](api-contracts.md) | Every route, JSON endpoint, and export endpoint |
| [component-inventory.md](component-inventory.md) | Vue components + Blade views catalog |
| [source-tree-analysis.md](source-tree-analysis.md) | Annotated directory tree |
| [development-guide.md](development-guide.md) | Setup, run, test, conventions |
| [deployment-guide.md](deployment-guide.md) | Current XAMPP-based deploy + productionization roadmap |
| [gaps-and-risks.md](gaps-and-risks.md) | **★ Improvement initiative input** — every issue surfaced during the audit |
| [_bmad-output/project-context.md](../_bmad-output/project-context.md) | Lean rule sheet for AI agents working in this repo |

## Getting started

For **new developers / contributors** — start with [development-guide.md](development-guide.md).

For **AI agents working on this project** — start with [_bmad-output/project-context.md](../_bmad-output/project-context.md), then read [architecture.md](architecture.md) for the invariants.

For the **improvement initiative** — start with [gaps-and-risks.md](gaps-and-risks.md). It's the prioritized punch-list that maps directly to the owner's stated improvement goals.

## Architecture pattern

**MVC + Service Layer**, with one important wrinkle:

> The app is a **hybrid Inertia/Blade application**. Inertia/Vue handles cross-cutting screens (auth, users, roles, settings, activity log). Blade handles business modules (sales, purchases, products, expenses, returns, invoices, stock, reports). Both share the same Vite build, same Tailwind compile, same auth session. The `resources/js/app.js` bootstrap gates Inertia mounting on the presence of `[data-page]` so Blade pages can use the same JS bundle.

This is non-obvious and easy to break — see the architecture doc for details.

## Key business rules

1. **Sale payment mode** is one of `cash` / `upi` / `gpay` / `mix` (Indian retail context). Keys must match `config/payment.sale_modes`.
2. **Sale payment status** is `paid` / `partial` / `pending` — auto-computed by `Sale::recalculatePaymentStatus()`.
3. **Mix payments** require `cash_amount + online_amount = amount_paid`.
4. **Daily sale invoices** come in 3 flavors per day: cash, online, mix. One Sale row can be on up to 3 invoices via 3 separate FK columns. At most ONE invoice per (date, type).
5. **Stock truth** lives in `Product.stock_quantity`. The legacy `stocks` table exists but is not the canonical source.
6. **Currency** is always rendered as `₹` + `number_format($x, 2)`.
7. **Status/state values** in code MUST be model-defined constants, never raw string literals.

## Quick links

- [README.md](../README.md) — default Laravel boilerplate (no project-specific content)
- [composer.json](../composer.json) — PHP dependencies
- [package.json](../package.json) — JS dependencies
- [.env.example](../.env.example) — **NOT FOUND** — adding one is a small but high-value win
