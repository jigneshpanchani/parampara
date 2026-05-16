---
stepsCompleted: ['step-01-init', 'step-02-discovery', 'step-02b-vision', 'step-02c-executive-summary', 'step-03-success', 'step-04-journeys', 'step-05-domain-skipped', 'step-06-innovation-skipped', 'step-07-project-type', 'step-08-scoping', 'step-09-functional', 'step-10-nonfunctional', 'step-11-polish', 'step-12-complete']
releaseMode: phased
status: complete
completedAt: 2026-05-09
inputDocuments:
  - _bmad-output/project-context.md
  - docs/index.md
  - docs/project-overview.md
  - docs/architecture.md
  - docs/source-tree-analysis.md
  - docs/data-models.md
  - docs/api-contracts.md
  - docs/component-inventory.md
  - docs/development-guide.md
  - docs/deployment-guide.md
  - docs/gaps-and-risks.md
documentCounts:
  briefs: 0
  research: 0
  brainstorming: 0
  projectDocs: 10
  projectContext: 1
workflowType: 'prd'
projectMode: 'brownfield'
classification:
  projectType: web_app
  domain: general
  domainFlavors: [internal-tool, single-tenant, owner-operator, indian-retail]
  complexity: low-to-medium
  projectContext: brownfield
  initiativeType: enhancement-quality-consistency
  stakeholderCount: 1
  scaleProfile: small-shop
  publicSurface: none
  outOfScopeUpfront:
    - multi-tenancy
    - public-api
    - mobile-or-native
    - i18n-or-multi-currency
    - seo
    - support-or-customer-success-ops
---

# Product Requirements Document — Parampara Improvement Initiative

**Author:** 250213
**Date:** 2026-05-09
**Project mode:** Brownfield improvement (no regressions)

## Executive Summary

Parampara is a Laravel 10 + Vue 3 (Inertia) stock & sales management system used by a single Indian retail/wholesale shop owner to record daily purchases, sales, returns, payments, expenses, and stock closings. The owner judges it as nearly feature-complete to the original brief and uses it daily; this PRD scopes a brownfield improvement initiative kicked off 2026-05-09 to elevate **daily-use ergonomics** (lists, filters, sorting, search, exports, responsive UI) and address the issues catalogued in the project audit ([docs/gaps-and-risks.md](../../docs/gaps-and-risks.md)) — without lossy migration of existing transaction data.

**Target user:** the owner-operator (one human; single sign-off authority). The improvement axis is *daily ergonomics for one user*, not multi-user features, not new domain capability.

**Problem being solved:** the system records the books, but the ergonomics around the books are inconsistent and full of friction. List views differ across modules, filters and sorting are absent, exports exist only in some places, the dashboard reads stock from a stale legacy table, and 25 audit findings document the gaps in detail. The system question is no longer "what does it do" — it's "how does it feel to use every day."

**Why now:** the right time to harden the ergonomics is before adding the next round of features.

### What Makes This Special

This isn't a new-product PRD — there's no market to differentiate against. The differentiator is **the post-initiative system vs its current self**: the same trusted ledger (same data, all existing history) with the friction points removed.

**Core insight:** the system is feature-complete; the gap is *daily-use ergonomics for one user*. We're treating that as the prioritization lens — list/filter/sort/export/responsive work jumps to the top tier; data correctness work earns its place as a *means* to ergonomics (a wrong dashboard hurts ergonomics), not as an independent goal; RBAC, multi-tenancy, and "enterprise readiness" stay out of scope because they don't serve this user.

**Constraint that shapes everything:** schema changes must be **lossless** — existing transaction data must remain intact and queryable. UI flows above the schema may be redesigned freely (the owner explicitly does not treat daily-entry, invoice-generation, or stock-auto-update flows as sacred — only the underlying data is).

## Project Context & Discovery

### Classification

| Field | Value |
|---|---|
| Project type | `web_app` (hybrid Inertia/Blade Laravel 10 + Vue 3) |
| Domain | `general` — small Indian retail/wholesale stock & sales management |
| Domain flavors | `internal-tool` · `single-tenant` · `owner-operator` · `indian-retail` |
| Complexity | low-to-medium |
| Project context | brownfield (~95% feature-complete) |
| Initiative type | enhancement / quality / consistency (not new product) |
| Stakeholder count | 1 (the owner; single sign-off authority) |
| Scale profile | small-shop — hundreds-to-low-thousands rows/year |
| Public surface | none (internal admin only, not internet-facing) |

### Out of scope (declared upfront — saves PRD design budget)

These are explicitly NOT in scope for this initiative. Future evolutions are possible but require a separate PRD:

- **Multi-tenancy** — system is single-shop; no tenant isolation work
- **Public API** — internal `web` middleware only; no external consumers
- **Mobile / native app** — browser-only via XAMPP
- **Internationalization / multi-currency** — Indian retail conventions are fixed: `₹`, UPI, G-Pay, Cash, Mix, GST/non-GST
- **SEO / public marketing pages** — admin only
- **Support runbooks / customer-success flows** — owner-operator deployment

### Implications for requirements (load-bearing prioritization)

The flavor tags rebalance how the audit findings in [docs/gaps-and-risks.md](../../docs/gaps-and-risks.md) should be prioritized in this PRD. Severity ratings in the audit assumed a generic web-app stance; the owner-operator + single-user reality shifts several findings:

- 🔴 **Stays HIGH** — direct daily-use impact:
  - **G-04** dashboard KPIs read from wrong table (owner sees wrong numbers daily)
  - **G-05** status string literals (owner's explicit rule + drift hazard)
  - **G-07** stock-closing reconciliation persists zeros (feature half-implemented; reconciliation IS the owner's tool)

- 🟡 **De-prioritizes to MEDIUM** for now:
  - **G-01** RBAC enforcement (matters when a 2nd user joins; not blocking today)
  - **G-06** GET-as-DELETE / CSRF (real bug, lower urgency on single-user internal tool)
  - **G-08** hardcoded path / `.env.example` (annoyance, not blocking)
  - **G-09** PurchaseController perf (small-shop scale gives years of runway)

- 🟢 **De-prioritizes to LOW**:
  - **G-02** User::destroy fatal (owner won't delete himself)
  - **G-03** dead DashboardController (no runtime impact)

- 🔼 **Upgrades to HIGH** under owner-operator + no-QA reality:
  - **G-16** automated regression tests **for the 6 invariants only** — they're how we enforce "no regressions" without a QA team

### Operational success-metric shape

Because the user is the owner-operator using this daily, success metrics skew operational ("the owner records the day's sales without rechecking", "stock closing actually reconciles", "list filters survive page nav across all modules") rather than commercial ("X% conversion lift").

## Success Criteria

### User Success (the owner is the user)

The initiative succeeds when the owner can — during normal daily use:

- Complete daily close-out without cross-checking against Excel or manual ledgers afterwards
- Find any past sale, purchase, expense, or payment by supplier name / seller name / date range / payment mode in under 30 seconds using a list filter (no SQL, no manual scrolling through pages) — *30s is a felt-time target, refine after Epic 2 ships*
- Export any list view to XLSX from the screen the data is on (not hunt for which screen has the export button)
- Trust that the dashboard KPIs (low stock, inventory value, monthly sales/purchases/expenses) match the underlying transaction tables — no silent divergence
- Use the system on a phone or tablet (320px+ width) without horizontal scrolling on any business module — *320px breakpoint assumed; revisit if owner uses tablet/desktop only*

- Save a stock closing and have the saved record show actual reconciliation values (purchased / sold / returns / expected vs actual / difference) — not zeros

### Operational Success (replaces "Business Success" — no commercial business here)

- The system books remain authoritative for tax/GST records throughout the initiative
- Zero historical data lost during the upgrade — every existing sale, purchase, payment, expense, return, invoice, and stock closing remains queryable in its current shape
- Zero days of "unable to record today's transactions" during rollout (the owner uses this every day; downtime is not acceptable)

### Technical Success

- **Zero regressions** on the six load-bearing invariants documented in [docs/architecture.md](../../docs/architecture.md):
  1. `Sale::recalculatePaymentStatus()` is the only writer of `payment_status` / `pending_amount`
  2. Mix payment split: `cash_amount + online_amount = amount_paid`
  3. SaleInvoice 3-FK (one Sale row, up to 3 invoice FKs by type), at most one invoice per (date, type)
  4. `Product::stock_quantity` is the source of truth for stock
  5. Schema rename in flight: `sell→sale` never reintroduced
  6. Inertia mount-node guard in `resources/js/app.js` stays load-bearing
- All schema changes lossless — existing rows queryable in the same shape (column adds OK; column renames with backfill OK; destructive drops without backfill not OK)
- All status / state values referenced via model-defined constants in any code touched by this initiative — no raw string literals like `'paid'`, `'pending'`, `'cash'` in newly-written or refactored code
- Automated regression tests added that exercise each of the six invariants (PHPUnit Feature, `RefreshDatabase` trait)

### Measurable Outcomes

- **100%** of admin module list views (Sales, Purchases, Products, Sale-Returns, Purchase-Returns, Sale-Invoices, Expenses, Expense-Categories, Payments, Stock, Stock-Closings) support: column sort, text search, server-side filter, paginate-with-querystring, XLSX export
- **0** raw status string literals (`'paid'` / `'pending'` / `'partial'` / `'cash'` / `'upi'` / `'gpay'` / `'mix'` / `'gst'` / `'without_gst'`) in code touched by this initiative — verified by grep over diff
- **Dashboard KPI parity:** every "Low stock" / "Inventory value" / stock-related number on the dashboard matches an equivalent query against `Product.stock_quantity` — verified by feature test
- **Mobile breakpoint:** all admin Blade views render usable at 320px viewport width — verified by manual checklist + screenshots
- **Invariant test coverage:** ≥ 1 feature test per invariant; runs green in `php artisan test`

## Product Scope

The audit's 6-epic sequencing in [docs/gaps-and-risks.md](../../docs/gaps-and-risks.md) maps to MVP / Growth / Vision after applying the first-principles rebalance from [Project Context & Discovery](#project-context--discovery). RBAC enforcement (audit's G-01) is **dropped from this PRD entirely** — given the owner's "just me indefinitely" stance, it becomes a future PRD if/when a 2nd user joins.

### MVP — Minimum Viable Product

Essential to declare the initiative successful. Without these, daily ergonomics doesn't measurably improve.

- **Epic 0 — Safety net (narrowed from audit's Epic 0):**
  - **G-04** Dashboard reads `Product.stock_quantity` (not legacy `Stock` table) for low-stock count and inventory value
  - **G-07** Stock closing reconciliation persists computed values (not zeros)
  - Add the smallest test scaffold to enforce no-regression for the six invariants
- **Epic 1 — Status constants sweep:**
  - **G-05** Add model-defined constants on Sale, Purchase, Payment, Product, etc.; replace raw status string literals across the codebase
  - **G-12** Use `SaleInvoice::TYPE_*` constants in `SaleInvoiceController` validation and queries
  - **G-13** Drive `ReportController::exportSales` payment-mode list from `config/payment.php` keys
- **Epic 2 — List view foundation (the bulk of stated improvement scope):**
  - Shared list-table component (Blade partial; Vue equivalent if needed) used by every admin module
  - Server-side sort, server-side filter (with persistent querystring), text search, XLSX export — uniformly available on every list
  - **G-09** Persist `Purchase.payment_status` so SQL-side filter + sort works
  - **G-10** Report stock from `Product.stock_quantity`; stop re-aggregating from items in `ReportController::stock`

### Growth Features (Post-MVP)

Improves the initiative outcome but the system is already meaningfully better without these.

- **Epic 3 — Component consolidation + responsive pass:**
  - **G-15** Pick canonical Modal / Pagination / SearchableDropdown variants; deprecate duplicates
  - Tailwind responsive audit on `admin.blade.php` + every `index.blade.php` (320px+)
- **Epic 4 — Stock-closing reconciliation depth:**
  - Beyond the **G-07** baseline fix in MVP, surface reconciliation history with diff highlighting and period-over-period comparison

### Vision (Future)

Enumerated so they don't sneak into this PRD's scope.

- **Audit Epic 5 — broader test coverage** beyond the six invariants
- **Audit Epic 6 — polish:** dead-code removal (**G-02, G-03, G-19, G-21, G-22**), `.env.example` (**G-08**), backup-path config (**G-08**), `orignal_name` typo migration (**G-24**)
- **G-01 (RBAC enforcement)** — explicitly out of scope per owner's "just me indefinitely" answer; pick up in a future PRD when a 2nd user joins
- **G-06 (GET-as-DELETE → CSRF-protected DELETE)** — code-quality issue, low daily-use urgency on a single-user internal tool; bundle into a future hardening PRD
- **G-14 (Activity log REGEXP_REPLACE perf)** — small-shop scale gives years of runway
- **G-17 (decimal:2 cast standardization)** — quiet correctness item; pick up when a real symptom appears
- **Productionization** — Docker / CI / automated backups / HTTPS — a separate PRD if/when the system leaves XAMPP

## User Journeys

> **Single persona** — *the owner.* No other user types interact with the system. The journeys below map different moments in the owner's day, contrasting the current friction (BEFORE) with the post-initiative behavior (AFTER) so requirements are derivable from the contrast.

### Persona — Aman, the shopkeeper *(name placeholder; substitute the owner's actual name when known)*

Owner-operator of a small Indian retail/wholesale shop with limited SKUs. Built Parampara himself to track daily business. Uses it multiple times a day: at opening to log late entries from yesterday, during the day to enter sales/purchases, at close-out to reconcile cash + generate daily invoices + verify stock. Comfortable with the system but increasingly frustrated as the data grows: filters don't exist, dashboards lie, exports are inconsistent. Wants the system to *get out of his way*.

### Journey 1 — Morning catch-up: logging yesterday's late sales

**When:** ~9 AM, opening the shop. A few sales came in just before closing yesterday and weren't entered.

**Before this initiative:**
Opens `/admin/sales`, sees the most recent 20 sales paginated. Wants to confirm what was already entered for yesterday before adding the new ones — but there's no date filter. Scrolls page-by-page. To be sure, opens MySQL directly and runs a date query. Adds the missed sales. Glances at the dashboard — the "Low stock" panel shows 5 products, but he just bought 3 of them yesterday. He doesn't trust the panel and ignores it.

**After:**
Opens `/admin/sales`, types yesterday's date into the date-range filter at the top of the list. Sees yesterday's entries instantly. Adds the missed sales via the existing entry form. Dashboard "Low stock" reflects the post-purchase state because it now reads `Product::stock_quantity`. He trusts what he sees.

**Capabilities revealed:**
- Server-side date-range filter on every list view
- Querystring-persistent filters
- Dashboard KPIs read canonical source-of-truth tables
- Filter UX consistent across modules so "filter by date" feels the same on Sales, Purchases, Expenses

### Journey 2 — Mid-day: entering a new purchase, then verifying it landed

**When:** ~2 PM, supplier just delivered. Owner enters the purchase.

**Before this initiative:**
Goes to `/admin/purchases/create`, picks supplier name, adds line items, sets bill type (GST), enters transportation cost. Saves. Returns to the purchases list to verify. Wants to filter by "Payment: Pending" to see his outstanding payables — the filter exists, but the code loads ALL purchases then filters in PHP. With ~600 historical purchases the page takes 4 seconds to render. Sorts by total descending — that doesn't work either (no sortable columns).

**After:**
Same entry flow (untouched — owner explicitly didn't mark it sacred, but no need to redesign what works). Returns to the list. The list loads in <500ms because `Purchase.payment_status` is now persisted and SQL-filterable. Sorts by total descending by clicking the column header. Exports the filtered "Pending" view to XLSX for his accountant.

**Capabilities revealed:**
- `Purchase.payment_status` persisted column (G-09)
- Server-side sort on every list view
- XLSX export on every list view (and same UX everywhere)
- List render time < 1s at small-shop scale

### Journey 3 — End-of-day: close-out and stock closing

**When:** ~9 PM, closing the shop. The most cognitively heavy moment of the day — owner needs to reconcile cash on hand, generate the day's invoices, and check stock.

**Before this initiative:**
Generates today's cash invoice → online invoice → mix invoice (`/admin/sale-invoices`) — three separate clicks across pages. Opens `/admin/stock-closings`, picks today's date range, sees the computed table (purchased / sold / returns / expected stock). Types in actual stock counts for each product. Hits Save. Goes to history, opens the saved closing — sees zeros for everything except the actual stock value he just entered. The reconciliation feature is half-implemented: it computes for display but persists nothing.

**After:**
Same invoice flow, except the close-out screen now consolidates the three "generate invoice" buttons + stock closing into one wizard (or, minimally, the existing screens persist their reconciliation context so navigating between them remembers the date). Stock closing Save persists the full computed snapshot — purchased, sold, returns, expected, actual, difference — exactly as displayed at the time of save. History view shows real reconciliation data.

**Capabilities revealed:**
- Stock closing reconciliation persistence (G-07)
- (Optional growth) Close-out wizard that gathers cash/online/mix invoice gen + stock closing on one screen with shared date
- All saved snapshots are immutable historical records (no future recompute risk if numbers in source tables change)

### Journey 4 — Discrepancy investigation: "the dashboard is wrong"

**When:** ~1 PM, owner sees the dashboard's "Low stock" panel showing something that doesn't match his memory.

**Before this initiative:**
Dashboard reads from the legacy `stocks` table, not `products`. Owner doesn't know that — he just knows the number is wrong. Opens phpMyAdmin, runs queries against `products` table to verify. Loses trust in the dashboard. Stops checking it.

**After:**
Dashboard reads from `Product::stock_quantity` — the same source that the rest of the app uses. The "Low stock" panel matches what the Stock list and Stock Closing index show. There's a feature test (G-04 verification) that fails CI if dashboard ever drifts from canonical source.

**Capabilities revealed:**
- Single source of truth for stock-related KPIs
- Automated test prevents future drift between dashboard and underlying tables
- Owner *trust* in the dashboard — the most subjective but most important success metric

### Journey 5 — Phone check: quick lookup while away from the desk

**When:** ~6 PM, owner is at a supplier's location and needs to check what he paid them last month.

**Before this initiative:**
Opens system on phone. Login works. Sidebar collapses but the main sales table requires horizontal scrolling — the GST number column pushes everything else off-screen. Numbers are cut off. Owner gives up and calls his accountant.

**After:**
Opens system on phone. Sales/Payments list shows owner-priority columns first (Date, Supplier, Total, Status). Secondary columns (GST number, transportation cost, notes) collapse into a tap-to-expand detail row. No horizontal scroll on 320px+ width.

**Capabilities revealed:**
- Responsive admin layout with breakpoint at 320px
- Mobile-priority column ordering (configurable per list)
- Detail-row pattern for secondary fields on mobile
- The owner's mental model: "I can use this from anywhere"

### Journey Requirements Summary

The five journeys above reveal three recurring capability families:

1. **Universal list-view UX** — date-range filter, text search, server-side filter, sortable columns, querystring persistence, XLSX export — must be present on every admin list, with consistent UX. *Maps to MVP Epic 2.*
2. **Trustworthy KPIs / reconciliation** — dashboard reads canonical sources; stock closing persists computed snapshots; all numbers the owner sees match what's actually in the database. *Maps to MVP Epic 0 (G-04, G-07).*
3. **Mobile-first responsive** — owner uses the system on phone regularly; tables must work at 320px without horizontal scroll. *Maps to Growth Epic 3.*

What the journeys deliberately do NOT reveal (because they're out-of-scope):
- Multi-user permission flows (single-user)
- Customer-facing screens (no public surface)
- API integration journeys (no external consumers)
- Billing / subscription / tenancy journeys (single-tenant)

## Web Application Specific Requirements

### Project-Type Overview

Parampara is a Laravel 10 + Vue 3 (Inertia) web application running locally on XAMPP. The frontend is a **hybrid**: Inertia/Vue 3 for cross-cutting screens (auth, users, roles, settings, activity log) and Blade for admin business modules (sales, purchases, products, expenses, returns, invoices, stock, reports). Both share the same Vite 4 build, Tailwind 3 stylesheet, and auth session.

Key architectural facts (from [docs/architecture.md](../../docs/architecture.md)):
- Mount-node guard in `resources/js/app.js` gates Inertia bootstrap on `[data-page]` so Blade pages share the JS bundle without booting a SPA. **Load-bearing — do not refactor.**
- Sessions + CSRF (no Sanctum tokens for the main app)
- Single-tenant, internal admin only — no public surface

### Technical Architecture Considerations

**Browser support matrix:**
- Modern desktop: Chrome ≥ 100, Edge ≥ 100, Firefox ≥ 100, Safari ≥ 15
- Modern mobile: Chrome on Android, Safari on iOS
- No IE 11 support; no legacy browser shims

**Responsive design:**
- Breakpoint floor: 320px viewport width (per [Journey 5](#journey-5--phone-check-quick-lookup-while-away-from-the-desk))
- Tailwind responsive utility classes (`sm:` `md:` `lg:` `xl:`) used throughout
- Mobile-priority column ordering on every list view (date, total, payment_mode visible at 320px; secondary fields collapse to a tap-to-expand detail row)
- Sidebar collapses on narrow viewports
- No horizontal scroll on any business module on phone

**Performance targets:**
- List render < 1s at small-shop scale (hundreds-to-low-thousands rows/year)
- Server-side pagination + filter + sort (no all-rows-then-filter-in-PHP patterns — replaces today's `PurchaseController::index` style)
- N+1 prevention via explicit eager loading on list-view controllers (continues today's pattern in `SaleController::index`)
- No specific p99 / latency SLA — owner-operator deployment, single XAMPP machine

**SEO strategy:**
- **N/A.** Application is internal admin behind login. No public-facing pages. No sitemap, no meta tags beyond basic title/favicon. Already declared out of scope in Step 2.

**Accessibility level:**
- Tailwind defaults (visible focus rings, semantic headings, form labels)
- No WCAG compliance commitment (not a regulated context, not customer-facing)
- Keyboard navigation: respect for tab order on forms (already mostly works via Tailwind defaults)
- Color contrast: basic (no audit / formal score)

**Real-time / live updates:**
- **N/A.** No websockets, no broadcasting, no live-updating dashboard. Owner refreshes manually if needed. Adding real-time would be a future PRD.

### Implementation Considerations

**Frontend bundling:**
- Vite 4 — `npm run dev` for HMR during development; `npm run build` for production assets to `public/build/`
- Single entry: `resources/js/app.js`
- Tailwind CSS compiled via PostCSS plugin in `vite.config.js`

**Inertia/Blade hybrid pattern (preserved as-is):**
- New screens match the surrounding module pattern:
  - Cross-cutting (users, roles, settings) → Inertia/Vue page in `resources/js/Pages/`
  - Business module → Blade view in `resources/views/admin/<module>/`
- Shared list-table component (Epic 2) has to work in both contexts: Blade partial for admin business modules + Vue component for the Inertia pages that need it. **This is a meaningful architectural decision flagged for the Architecture step (post-PRD)** — options include: (a) two implementations sharing a common props contract, (b) one canonical Blade implementation only (since admin business modules are the bulk of the surface), or (c) migrate one side to match the other. Decision deferred to Architecture per the BMad workflow.

**State preservation:**
- Inertia: form-state survives page transitions via Inertia's built-in mechanisms
- Blade: query-string-based state for filters (`->paginate(N)->withQueryString()` pattern)
- Standardize: every list view, both Inertia and Blade, must persist filter/sort/page in URL so back-button + bookmarks work

**Asset / upload handling:**
- Storage symlink (`php artisan storage:link`) required for product photos / company logo / favicon
- Public disk under `storage/app/public/`
- File upload validation in FormRequest classes (continues pattern in `StoreProductRequest`)

### Skipped categories (per CSV `skip_sections`)

- **`native_features`** — not a native app
- **`cli_commands`** — no CLI surface beyond Laravel artisan defaults

## Project Scoping & Phased Development

> Phasing was established in [Success Criteria → Product Scope](#product-scope) above. This section adds the strategic framing — MVP philosophy, resource picture, and risk mitigation strategy — without renegotiating the phase allocation.

### MVP Strategy & Philosophy

**MVP Approach: "Problem-solving MVP."** This is not a "minimum to validate concept" MVP (the concept is validated — the system runs daily). It's a "minimum to remove the daily friction" MVP. Each MVP epic must independently make a measurable difference to a journey from [User Journeys](#user-journeys). If an epic doesn't, it doesn't belong in MVP.

Concrete tests for "is it MVP-worthy?":

- Does this fix something the owner sees daily? (Journey 1, 2, 3, 4)
- Does this reduce a daily ergonomic friction OR fix a daily incorrectness? (Yes for G-04, G-05, G-07, G-09, G-10)
- Could the owner notice the change in Week 1 of using the post-MVP build? (Yes — list views feel different, stock closing now persists, dashboard numbers match)

**Resource requirements:**

- One developer (owner, with AI assistance)
- No QA team — automated tests for the six invariants substitute (G-16-narrowed, captured in Technical Success criteria)
- No designer — Tailwind defaults + the existing admin layout aesthetic
- No DevOps — XAMPP local; no CI in this PRD's scope
- Single sign-off authority (the owner)

### Phase 1 — MVP (already detailed in Product Scope)

Refer to [Product Scope → MVP](#mvp---minimum-viable-product). Epics 0, 1, 2.

**First module for Epic 2 list-view rollout: `Sales`** (most-used per Journey 1 & 2). Other modules adopt the pattern in subsequent stories within Epic 2.

### Phase 2 — Growth (Post-MVP)

Refer to [Product Scope → Growth Features (Post-MVP)](#growth-features-post-mvp). Epics 3, 4.

### Phase 3 — Vision (Future PRDs — explicitly out of this PRD)

Refer to [Product Scope → Vision (Future)](#vision-future). Items demoted out of scope (G-01 RBAC, G-06 CSRF, G-14 perf, etc.) live here.

### Risk Mitigation Strategy

**Technical risks:**

- *Lossy schema migration breaks historical data.* Mitigation: manual `parampara_pre_<change>_<timestamp>.sql` backup before every migration (already standard practice; verify before each PR). Per-migration rollback tested on a copy DB.
- *Status-constant refactor (Epic 1) misses an unreferenced code path.* Mitigation: grep over diff to verify zero raw status string literals remain in touched files. Smoke test the affected list views and write paths.
- *Persisting `Purchase.payment_status` (G-09) introduces drift between stored value and actual payments.* Mitigation: `recalculatePaymentStatus()`-equivalent on `Purchase`, called from every Payment create/update/delete event. Feature test for the invariant (parallel to existing `Sale` recalc).
- *Shared list-table component proves harder to share between Vue and Blade than expected.* Mitigation: deferred to Architecture step (post-PRD); the Architect picks the integration pattern with full context. **PRD does NOT pre-commit to a single approach.**

**User-acceptance risks (replaces "market risks" — no market):**

- *Owner doesn't like the new list-table UX after Epic 2.* Mitigation: `Sales` ships first (locked-in first module — see Phase 1 above), owner uses for 2-3 days, feedback informs the rest of the modules. Checkpoint review at end of each MVP epic.
- *Stop-the-world: an MVP epic breaks the daily-entry flow.* Mitigation: invariant feature tests added in Epic 0 fail CI before merge. Schema-lossless constraint means rollback is always possible. Owner can revert any deployed change to a previous git commit on his XAMPP machine.

**Resource risks:**

- *Owner has limited time alongside running the shop.* Mitigation: each MVP epic is independently shippable. No "all or nothing" — even if only Epic 0 ships, the system is meaningfully better.
- *AI-assisted code quality drifts across stories.* Mitigation: [_bmad-output/project-context.md](../../_bmad-output/project-context.md) is loaded as foundational context for every BMad story; constants rule and invariants rule are explicit there.
- *Bus factor 1 (single developer).* **Accepted constraint, not mitigated in this PRD.** The improved documentation set ([docs/](../../docs/)) and this PRD itself already reduce ramp-up time for any future developer; further mitigation is a future-PRD concern.

### Scope guardrails (lock-in)

To prevent drift between now and Architecture/Implementation:

- The 6 invariants in [docs/architecture.md → Key invariants](../../docs/architecture.md#key-invariants-must-not-break) are non-negotiable.
- The schema-lossless rule from Executive Summary is non-negotiable.
- The "no commercial business metrics" framing means: no analytics SDK, no telemetry, no A/B testing infra. The system is internal; success is observed by the owner directly.
- "Just me — indefinitely" answer locks RBAC enforcement work out of this PRD's scope. A second-user PRD picks it up if/when needed.
- **First-module-for-Epic-2 = `Sales`** (locked).

## Functional Requirements

> **Capability contract.** UX designs, architecture, epics, and stories all derive from this list. Capabilities not listed here will not be implemented unless added back to this section.
>
> **Single actor**: "the owner" (also referred to as "Aman" in journey narratives) — there are no other user types in scope.
>
> **System-facing FRs** (FR19-23) describe code/test properties rather than user-visible capabilities; they're included because they're testable, in-scope, and load-bearing for the no-regression goal.

### Universal List-View UX (MVP Epic 2)

- **FR1**: Owner can filter every admin module list (Sales, Purchases, Products, Sale-Returns, Purchase-Returns, Sale-Invoices, Expenses, Expense-Categories, Payments, Stock, Stock-Closings) by date range
- **FR2**: Owner can text-search every admin module list by relevant text fields (supplier name, seller name, product name, category name)
- **FR3**: Owner can sort every admin module list ascending or descending by any visible column
- **FR4**: Owner can apply multiple filters simultaneously on every list (e.g., date range + payment status + payment mode)
- **FR5**: Owner can navigate paginated list views and return via back button or bookmark to the same filtered/sorted state
- **FR6**: Owner can see total counts and aggregate sums reflecting the currently applied filters, not the unfiltered set
- **FR7**: System renders every list view in under 1 second at small-shop scale (defined as ≤ 5,000 rows in any single module)

### Data Export (MVP Epic 2)

- **FR8**: Owner can export the currently filtered list to XLSX from any admin module list view
- **FR9**: Owner can export the currently filtered list to PDF from any admin module list view (for human-readable presentation)
- **FR10**: Owner can export sales reports, purchase reports, and stock reports to XLSX (preserves today's report-export functionality)
- **FR11**: Owner can export individual sale invoices to XLSX (preserves today's per-invoice export)

### Dashboard KPI Trust (MVP Epic 0 — G-04)

- **FR12**: Dashboard "Low stock" count reflects products whose `stock_quantity` is below threshold, sourced from the canonical `Product` table (not legacy `Stock`)
- **FR13**: Dashboard "Total inventory value" reflects `SUM(stock_quantity × selling_price)` over active products from the `Product` table
- **FR14**: Dashboard monthly sales / purchases / expenses totals match the corresponding source tables exactly (parity verified by test)
- **FR15**: System fails CI if any dashboard KPI drifts from its canonical source

### Stock Closing Reconciliation (MVP Epic 0 — G-07)

- **FR16**: Owner can save a stock closing whose saved snapshot persists all reconciliation values (`opening_stock`, `purchased_qty`, `purchase_returns_qty`, `sold_qty`, `sale_returns_qty`, `expected_stock`, `actual_stock`, `difference`) — not zeros
- **FR17**: Owner can view stock-closing history and see the persisted reconciliation values for each saved closing
- **FR18**: Saved stock closings are immutable historical snapshots — they do not recompute from current data when viewed later

### Status & State Constants (MVP Epic 1 — G-05)

- **FR19**: System code references all status / state values (e.g., `'paid'`, `'pending'`, `'partial'`, `'cash'`, `'upi'`, `'gpay'`, `'mix'`, `'gst'`, `'without_gst'`) via model-defined class constants (e.g., `Sale::PAYMENT_STATUS_PAID`, `SaleInvoice::TYPE_CASH`), never via raw string literals
- **FR20**: System fails CI if any newly-touched code introduces a raw status string literal (verified by grep over the diff)

### Invariant Safety Net (MVP Epic 0 — G-16-narrowed)

- **FR21**: System has automated regression tests verifying each of the 6 invariants from [docs/architecture.md](../../docs/architecture.md): Sale payment recalc-only writer, Mix-payment cash+online sum, SaleInvoice 3-FK consistency, `Product.stock_quantity` authority, no `sell*` reintroductions, Inertia mount-node guard preserved
- **FR22**: System fails CI if any invariant test fails
- **FR23**: System persists `Purchase.payment_status` (computed from related payments) so list-view filtering and sorting can use SQL rather than load-everything-then-filter-in-PHP

### Responsive Mobile UI (Growth Epic 3)

- **FR24**: Owner can use every admin module on a 320px-wide viewport without horizontal scrolling
- **FR25**: Each list view shows owner-priority columns (date, total, payment_mode, status) at narrow viewports; secondary columns collapse to a tap-to-expand detail row
- **FR26**: Admin sidebar collapses to a hamburger menu on narrow viewports
- **FR27**: Admin forms on narrow viewports stack vertically without overflow

### Component Consolidation (Growth Epic 3 — G-15)

- **FR28**: System has a single canonical Modal component used across all admin modules; duplicates are removed
- **FR29**: System has a single canonical Pagination component used across all admin module lists; duplicates are removed
- **FR30**: System has a single canonical SearchableDropdown component used across all admin module forms; duplicates are removed

### Stock Closing Depth (Growth Epic 4)

- **FR31**: Owner can compare two saved stock closings to see period-over-period differences in reconciliation values
- **FR32**: Owner can view a saved closing with diff highlighting (products whose `difference` is non-zero are visually distinguished)

### No-Regression Contract on Existing Flows

- **FR33**: All existing transaction flows continue to work exactly as today: daily-sale entry, sale invoice generation (cash/online/mix), stock auto-update from purchase/sale/return, sale payment recalculation, mix-payment cash+online split
- **FR34**: All existing data (sales, purchases, payments, expenses, returns, invoices, stock closings) remains queryable in the same shape it has today — schema changes are additive or column-renames-with-backfill, never destructive drops without backfill
- **FR35**: All existing route names continue to resolve to the same controller actions; no route renames that break user bookmarks

## Non-Functional Requirements

> **Selective.** Categories not listed below were assessed for relevance and explicitly skipped because they don't apply to this product (scalability, accessibility-as-compliance, external integration, regulatory compliance). See Project-Type and Domain sections for skip rationale.

### Performance

- **NFR1**: Every admin list view renders within 1 second at small-shop scale (≤ 5,000 rows in any single module). *Already in FR7 — restated here as the binding performance budget.*
- **NFR2**: List-view filtering, sorting, and pagination execute as SQL queries — no `Collection::filter()` over `->get()`-loaded results, no aggregate sums recomputed in PHP after pagination
- **NFR3**: List-view controllers eager-load relations needed for display columns to avoid N+1 (continues today's `SaleController::index` pattern)
- **NFR4**: Dashboard KPI queries execute in under 500 ms (combined, for the entire dashboard render) at small-shop scale
- **NFR5**: XLSX / PDF export of any list view returns the file within 5 seconds at small-shop scale

### Security

- **NFR6**: All destructive operations (delete / soft-delete) use HTTP `DELETE` or `POST` with CSRF protection — never `GET`. *Note: current code uses `GET` for several `remove*` routes (G-06); fixing this is in Vision scope, not MVP. NFR documents the target state so new mutating routes added during MVP/Growth don't regress.*
- **NFR7**: User passwords are hashed using Laravel's `bcrypt` / `argon2` defaults (already enforced by Breeze; flagged here so future auth changes don't regress)
- **NFR8**: Session cookies use `Secure` and `HttpOnly` flags when served over HTTPS (configurable; HTTPS itself is post-PRD productionization concern)
- **NFR9**: File uploads (product photos, company logo, favicons) validate MIME type and size at the FormRequest layer — never trust client-provided filename or extension alone
- **NFR10**: The application is not exposed to the public internet in its current XAMPP deployment; if that changes, a separate hardening PRD applies first

### Maintainability

This is the core quality axis of the initiative.

- **NFR11**: All status / state values are referenced via model-defined class constants (binding rule established in [Executive Summary § What Makes This Special](#what-makes-this-special)). *Mirrors FR19; restated here as a maintainability NFR because the constants rule applies forever, not only to this initiative's diff.*
- **NFR12**: New write logic for Sale, Purchase, Stock, or Product flows through the corresponding `app/Services/<Domain>Service.php` service class — controllers stay thin (continues established pattern; codified as a binding NFR for new code)
- **NFR13**: New write actions use FormRequest validation classes (`app/Http/Requests/Admin/Store<Model>Request.php`); inline `$request->validate()` is forbidden for write actions in new code
- **NFR14**: New code passes `./vendor/bin/pint` (Laravel Pint default preset) before commit
- **NFR15**: Schema migrations are lossless for existing data — column adds, column-renames-with-backfill, and additive index creation are permitted; destructive column drops without backfill are not
- **NFR16**: Each MVP epic ships with at least one feature test exercising the epic's main code path (Epic 0: invariants; Epic 1: status-constant grep; Epic 2: list-view filter/sort/paginate per module)
- **NFR17**: The 6 architecture invariants are covered by automated feature tests after Epic 0 ships and remain covered indefinitely (regression test suite grows, never shrinks)

### Reliability

- **NFR18**: Before every destructive schema migration, a manual SQL backup is captured at repo-root with the convention `parampara_pre_<change>_<YYYYMMDD>_<HHMMSS>.sql` and excluded from git (continues established practice; documented here so it isn't silently dropped)
- **NFR19**: Any deployed change can be rolled back to a previous git commit on the XAMPP machine in under 15 minutes — recovery time objective for owner-initiated rollbacks
- **NFR20**: No availability SLA / uptime commitment — this is a single-user internal tool. The owner accepts that downtime during upgrades is bounded by his own deployment window

### Usability (cross-references only)

Usability quality attributes are entirely captured by:

- **FR24-27** — responsive UI requirements
- **FR1-7** — universal list-view UX
- **FR16-18** — stock closing reconciliation usability
- **[Project-Type § Responsive Design](#technical-architecture-considerations)** — 320px breakpoint, mobile-priority columns, sidebar collapse

No additional NFRs needed — the usability standards live in the Functional Requirements list because they are testable owner-facing capabilities, not background quality attributes.


