---
stepsCompleted: ['step-01-init', 'step-02-discovery', 'step-03-core-experience', 'step-04-emotional-response', 'step-05-inspiration', 'step-06-design-system', 'step-07-defining-experience', 'step-08-visual-foundation']
inputDocuments:
  - _bmad-output/planning-artifacts/prd.md
  - _bmad-output/project-context.md
  - docs/index.md
  - docs/project-overview.md
  - docs/architecture.md
  - docs/source-tree-analysis.md
  - docs/data-models.md
  - docs/component-inventory.md
  - docs/api-contracts.md
  - docs/development-guide.md
  - docs/deployment-guide.md
  - docs/gaps-and-risks.md
project_name: parampara
user_name: "250213"
date: 2026-05-09
projectMode: brownfield
workflowType: ux-design
status: in-progress
---

# UX Design Specification parampara

**Author:** 250213
**Date:** 2026-05-09

---

<!-- UX design content will be appended sequentially through collaborative workflow steps -->

## Executive Summary

### Project Vision

Brownfield ergonomics overhaul of a working stock & sales ledger that is already trusted with the books. The single user — the shop owner — needs the system to *get out of his way* during daily use: fast, consistent, exportable list views; trustworthy dashboard KPIs that match the underlying transaction tables; reconciliation features that actually persist what they compute; mobile-usable from the shop floor. Design success = the owner stops having to work around the system. Constraints inherited from the PRD: no regressions to existing flows, schema-lossless changes only, single-tenant, internal admin only.

### Target Users

Single persona: **Aman, the shopkeeper-owner** (placeholder name).

- **One user, indefinitely.** No multi-user, no role differentiation in this PRD scope. RBAC enforcement is a deferred future PRD.
- **High tech literacy.** Built the system himself with AI assistance; comfortable falling back to phpMyAdmin when the UI fails him — which means the UI has a higher bar to clear than "barely usable."
- **Uses the system 3–5 times per day** at distinct moments — morning catch-up on yesterday's late entries, mid-day data entry + verification, end-of-day close-out (cognitively heaviest), occasional phone lookup when away from the desk, discrepancy investigation when something looks wrong.
- **Concrete frustrations** with the current UI: missing date filter forces page-by-page scrolling; dashboard reads wrong table → already abandoned; stock closing saves zeros; lists differ visually and behaviorally across modules; phone view requires horizontal scroll.
- **Devices**: primarily desktop on XAMPP inside the shop; secondarily phone at 320px+ when off-site.
- **Trust threshold**: he stops using a feature the moment he doesn't trust it. The current dashboard "Low stock" tile is the canonical case — already ignored.

### Key Design Challenges

1. **Universal list-view consistency across 11 admin modules.** Every `index.blade.php` reimplements its own table today. The biggest UX leverage point is one shared interaction pattern; the architectural complication is that admin modules are Blade while cross-cutting screens are Vue/Inertia. Whether the shared component is one Blade partial, two parallel implementations, or a migration is deferred to Architecture — but the **interaction contract** (filter bar shape, sort affordance, export entry point) must be settled here so both sides can implement against it.
2. **Responsive at 320px without sacrificing data density.** Indian retail lists are wide — date, supplier/customer, items, total, GST, payment mode, status, action. Owner-priority columns must be visible at 320px; secondary columns need to collapse into a discoverable, non-intrusive detail-row pattern that doesn't break the desktop layout.
3. **Trust-by-design for KPIs and reconciliation.** Wrong numbers are a UX problem, not just a data problem. Once the dashboard reads `Product::stock_quantity` and stock closing persists computed snapshots, the UX must communicate "this matches the source of truth" — visual cues (last-updated timestamps, source labels, subtle confidence indicators) are part of the design surface, not just engineering correctness.
4. **No design system, Tailwind utility-first.** No shared button style, no shared input style, no design tokens. We need a thin pattern layer (filter bar, search input, sort header, export button, table density rules) that's reusable across modules without forcing a full design-system buildout — that would be out of scope.
5. **No-regression UX on the entry forms.** Owner does not treat the daily-entry / invoice-generation / stock-update flows as sacred for *redesign*, but they're sacred for *working today*. Visual and interaction changes around them must be additive — no flow rewrites in MVP.

### Design Opportunities

1. **One list pattern, eleven payoffs.** Defining a single list-view interaction model pays off across every admin module simultaneously. The single highest-leverage UX decision in the initiative.
2. **Mobile-priority column ordering as a per-list config.** Each module declares its priority order (Sales: date > customer > total > status; Expenses: date > category > amount > method) and the responsive breakpoint logic falls out automatically — no bespoke responsive code per module.
3. **Querystring-as-state for back-button + bookmark wins.** Already partially in place via `->withQueryString()`. Designing the URL-state model deliberately (filter param names, sort shape, page index) means filters survive reload, the back button works correctly, and bookmarks like "Pending purchases" become free.
4. **Trust signals on dashboard tiles.** Once KPIs read from canonical sources, dashboard tiles can include subtle source/freshness markers ("from products" / "as of now") that re-anchor trust. Small visual addition, large psychological payoff for an owner who has already abandoned the dashboard once.
5. **Close-out date-continuity (the MVP-tier wizard win).** Without committing to a full close-out wizard, simply designing the 3 invoice-generation screens + stock closing to share **date context across navigation** (URL-persistent date param) cuts the cognitive load of the heaviest moment of the day. The full wizard stays as Growth-tier; date-continuity is the cheap MVP win.

## Core User Experience

### Defining Experience

The product's value, post-initiative, lives in one core loop the owner runs dozens of times per day:

> **List → find → act.**
>
> The owner opens an admin module list, narrows it (date range / payment status / supplier / product / search), reads the result, and acts on a row — verify, edit, export, generate invoice, or move to a related screen.

Every journey except dashboard-discrepancy passes through this loop. The morning catch-up is "find yesterday's sales." The mid-day verify is "find the purchase I just entered." The end-of-day close-out is "find today's eligible sales for cash/online/mix invoices." The phone lookup is "find what I paid this supplier last month." Even discrepancy investigation ends in the loop ("show me the products at low stock").

The current friction is concentrated entirely on the **find** step — filters don't exist on most lists, search is inconsistent, sort isn't server-side, and the exported view often doesn't match what's on screen. The redesign's center of gravity is the find step.

A secondary core experience is **trust** — the owner must believe what he sees on the dashboard and on aggregate totals. Currently he doesn't, on the dashboard, and the system pays for it (he's stopped looking). Restoring trust is binary, not gradual: numbers either match the source of truth or they don't.

### Platform Strategy

- **Web app only.** No native, no PWA install, no offline mode. The PRD declares mobile-app and offline as out-of-scope.
- **Browser support**: modern desktop (Chrome / Edge / Firefox / Safari ≥ recent) and modern mobile (Chrome Android, Safari iOS). No legacy browser shims.
- **Primary input**: mouse + keyboard on desktop. **Secondary input**: touch on phone at 320px+. Tab order on forms must remain keyboard-friendly.
- **Hybrid Inertia/Blade rendering** — admin business modules are Blade; cross-cutting (auth/users/roles/settings/activity log) is Vue/Inertia. The interaction contract defined in this spec must be implementable on both sides — even if the actual shared component is one canonical implementation, the *design* assumes both renderers honor the same contract.
- **No real-time, no WebSockets, no broadcasting.** Owner refreshes manually if needed.
- **Session-based auth + CSRF** (no Sanctum tokens for the main app). State persistence is server-side session + URL querystring; no client-side persistence layer.
- **Tailwind 3 utility-first.** No design tokens, no theme config beyond defaults. The pattern layer this spec defines is utility composition + a thin set of named patterns, not a new design system.

### Effortless Interactions

These are the interactions that should require zero thought from the owner:

- **Open a list, type a date in the date filter, see the filtered set instantly.** No "Apply" button friction. Either inline-debounced or auto-submit on field blur.
- **Click a column header, list re-sorts.** Click again, direction flips. The sort is server-side and survives pagination.
- **Hit refresh or come back via back-button — filters and sort are still applied.** Querystring is the source of state truth.
- **Click "Export" and get the *currently visible* data**, not the unfiltered set. Export is right next to the filter bar so the visual relationship is obvious.
- **On phone, the priority columns are visible immediately**, secondary columns are one tap away in a detail row. No horizontal scroll, ever.
- **Sidebar navigation is always present** — switching modules carries no perceptible delay (Blade page navigation is fast already; preserve that).
- **Aggregate totals reflect the current filter** — if the owner filters "Pending" purchases, the total at the top of the page is the pending total, not the all-time total.

### Critical Success Moments

These are the binary "did the redesign work" moments — failing any one of these means the redesign didn't land:

1. **First filtered list query** — the moment the owner types yesterday's date into the Sales list filter and sees yesterday's entries instantly. If this feels slow, fiddly, or inconsistent across modules, the entire MVP misses.
2. **First trusted dashboard read** — the moment the owner checks the "Low stock" tile and the count matches what the Stock list shows. Trust-restoring or trust-killing.
3. **First saved stock closing review** — the moment the owner opens a saved closing from history and sees real reconciliation values (purchased / sold / returns / expected / actual / difference), not zeros. The half-implemented feature finally works.
4. **First clean XLSX export** — the moment the owner exports a filtered "Pending" view to XLSX and the file contains exactly the on-screen rows, no more, no less. If exports surprise him, exports won't be used.
5. **First successful phone session** — the moment the owner opens the system on his phone, navigates to Sales, and reads what he needs without horizontal scrolling. If this fails once, he'll go back to calling his accountant.
6. **First post-Epic-1 grep** — the developer-side success: a grep for raw status string literals in touched files returns zero results. CI fails when literals reappear. (Owner-invisible but load-bearing for long-term maintainability.)

### Experience Principles

The principles below guide every downstream decision in this spec — visual foundation, design directions, component strategy, UX patterns. When a design choice is in tension, these principles arbitrate.

1. **One pattern, applied everywhere.** Filter, search, sort, paginate, export — same affordances, same placement, same behavior across all 11 admin modules. Predictability beats per-module novelty. The owner learns the pattern once.
2. **Trust before delight.** Numbers must be right and feel right (source labels, freshness markers, totals matching filters) before any visual flourish. A wrong dashboard with beautiful animations is worse than a plain dashboard with right numbers.
3. **State lives in the URL.** Filter / sort / page / search are querystring params. Reload works, back button works, bookmarks work, deep-linking works — by virtue of the data being where the browser already keeps state.
4. **Mobile-priority columns, not mobile-redesigned screens.** The phone view is the desktop view with secondary columns collapsed into a detail row. One template, two layouts via responsive breakpoints — never a separate phone-only screen.
5. **Additive, never subtractive, on entry forms.** Daily-entry / invoice-generation / stock-update flows are not redesigned in MVP. UX changes around them (post-save confirmations, breadcrumbs, return-to-list) are additive; the working flow is sacrosanct.
6. **Tailwind utility composition over a new design system.** Define a thin set of named patterns (`.list-filter-bar`, `.list-table`, `.list-export-btn`) as Blade partials / Vue components — not a token system, not a Tailwind plugin, not a CSS rebuild.

## Desired Emotional Response

### Primary Emotional Goals

This is a daily-driver internal tool used by one person, not a consumer product chasing virality or delight. The emotional bar is calibrated accordingly:

> **Primary goal: Calm competence.**
>
> The system should feel quiet, reliable, and out-of-the-way. The owner should be able to use it without thinking about *it* — only about the work *it serves*. Trust and confidence are the load-bearing emotions, not excitement.

A delightful animation in this context is a distraction. A wrong number is a betrayal. A missing filter is friction. The redesign succeeds when the owner stops noticing the system at all — when it disappears into the background of his day.

### Emotional Journey Mapping

The owner has used this system for a year. There is no "first-time user" emotional arc. The arc that matters is the **before-vs-after-redesign** delta across his daily moments:

| Moment | Today's emotional state | After redesign |
|---|---|---|
| Opens Sales list | Mild dread — knows he'll scroll | Mild relief — filter works |
| Adds a purchase | Comfortable — entry forms work | Comfortable — unchanged |
| Glances at dashboard | Skeptical — numbers wrong, ignored | Quietly trusting — numbers match |
| End-of-day close-out | Cognitively heavy | Lighter — date context carries forward |
| Phone lookup off-site | Annoyance — horizontal scroll | Indifferent — just works |
| Discrepancy investigation | Distrust spiral — opens phpMyAdmin | Quick verify, move on |
| Reviews saved stock closing | Disappointment — sees zeros | Satisfaction — real reconciliation values |
| Sends export to accountant | Awkwardness — exports don't match what's on screen | Quiet pride — clean, exactly the filtered set |

Note the asymmetry: the redesign rarely *adds* delight; it *removes* friction. That's the right shape for this product.

### Micro-Emotions

The micro-emotions that matter most, ranked by leverage:

1. **Trust over Skepticism** — the highest-leverage micro-emotion. Already lost on the dashboard; recovering it is binary, not gradual. Numbers must match source-of-truth and *visibly* match.
2. **Confidence over Confusion** — every list filters/sorts/searches the same way; learning the pattern once means knowing every module.
3. **Calm over Excitement** — excitement signals novelty, novelty signals unpredictability, unpredictability signals risk. Calm is the goal.
4. **Accomplishment over Frustration** — at close-out, the heaviest moment of the day, the system should feel like it's helping, not extracting effort.
5. **Satisfaction over Delight** — clean exports, accurate snapshots, persisted reconciliation. Quiet wins, not flashy ones.

Micro-emotions to actively avoid:

- **Anxiety** during destructive actions — delete prompts must be clear but not alarming
- **Surprise** — exports that don't match the visible filter, dashboard numbers that drift, "wait, where did that come from"
- **Self-blame** when something fails — error messages must be specific and actionable, never make the owner feel like he did something wrong

### Design Implications

Connecting emotions to concrete UX choices:

| Emotional goal | UX design approach |
|---|---|
| **Calm** | Consistent layout across modules; no animations beyond subtle transitions; predictable filter/sort/export placement; no per-module novelty |
| **Trust** | Visible source labels on dashboard tiles ("from products"); freshness markers ("as of now"); aggregate totals that respect the active filter; saved-snapshot timestamps on stock closings |
| **Control** | Filter bar always visible (not collapsed behind a menu); URL-as-state so back-button and reload preserve everything; export button physically adjacent to the filter so the relationship is obvious |
| **Relief** | URL-persistent date context across close-out screens; eager-loaded relationships so list pages don't stutter; aggregate totals computed alongside the page so the owner never wonders "is it still loading?" |
| **Quiet pride** | Export contract: filtered view = exact rows in the file. No surprise rows, no missing rows. The accountant gets exactly what the owner saw. |
| **Confidence** | One pattern across 11 modules — Sales' filter bar looks and behaves identically to Expenses' filter bar |
| **Avoiding anxiety** | Destructive actions (delete sale, delete stock closing) confirm clearly with a specific name in the prompt ("Delete sale #123 of ₹4,500 dated 2026-05-08?"), not generic "Are you sure?" |
| **Avoiding surprise** | The dashboard, the lists, and the exports all read from the same source — what you see is what you exported is what's in the database |
| **Avoiding self-blame** | Validation errors point to the specific field with a fix-it message, never "Operation failed" |

### Emotional Design Principles

These translate the emotional goals into rules the design phases downstream must respect:

1. **Calm over delight.** No animations beyond ≤ 200ms transitions on hover/focus/disclosure. No celebration moments, no confetti, no "well done!" toasts. The product is a reliable colleague, not an enthusiastic intern.
2. **Trust signals are first-class UI.** Source labels, freshness markers, filter-respecting totals are not decoration — they're load-bearing. Treat them with the same rigor as primary actions.
3. **Confirm destructive actions; never interrogate routine ones.** Delete prompts must include the specific record name/amount/date. Filter changes never need confirmation. Save-closing needs no confirmation modal — the form submission is the confirmation.
4. **Anticipate, don't surprise.** Exports return on-screen rows. Dashboards show source data with provenance. Saved closings preserve the exact computed values at save-time. The owner should never need to ask "where did that number come from."
5. **Failure recovery is part of the emotional contract.** Specific, actionable, never-blaming error messages. If a CSV export fails, say "Export failed — try filtering to under 5,000 rows." Not "Operation failed."
6. **Mobile use is utilitarian, not aspirational.** Phone is for lookup, not for entry. The emotional goal at 320px is "I can read this without rotating" — not "this is a great phone experience." Honest framing prevents over-investing in mobile interaction patterns.

## UX Pattern Analysis & Inspiration

### Inspiring Products Analysis

The owner didn't volunteer specific apps, so this analysis draws on products that match the brief — Indian SMB retail / accounting tools the owner has likely encountered, plus general admin/data tools whose list-view patterns transfer cleanly to this context.

**Tally Prime** (Indian accounting mainstay)

- *What it does well*: Keyboard-driven flow, dense information per screen, zero ornamentation. The owner spends his day in lists; Tally's lists feel like spreadsheets that respond — they don't waste vertical space, totals are always visible, navigation is predictable.
- *What we learn*: Density is a feature, not a bug, in this context. The Indian retail mental model is "show me the data, get out of my way." Reject the modern SaaS tendency toward whitespace-heavy layouts.
- *Caveat*: Tally's keyboard-only flows are not the goal here. We borrow the density and predictability, not the input model.

**Linear** (project management)

- *What it does well*: List views that feel instant — server-side filter/sort/search with URL state, removable filter chips, sub-second response, sortable columns with subtle direction indicator. Calm aesthetic; no animations beyond purposeful ones.
- *What we learn*: This is the closest pattern reference for the Sales/Purchases/etc. list views. The interaction contract — filter bar → chips → sortable headers → URL-as-state — is the model.
- *Caveat*: Linear has a real design system. We're getting the *patterns* without the design-system buildout.

**Stripe Dashboard** (financial admin)

- *What it does well*: Trust signals on every numeric tile (timestamps, source labels, comparison deltas with subtle visual treatment); export buttons physically adjacent to the filter so the relationship is obvious; aggregate totals reflect the active filter (not the unfiltered set); destructive actions confirm with the specific record's name and amount.
- *What we learn*: This is the model for the dashboard tiles, the export contract, and the destructive-action confirmation language. Trust through provenance.
- *Caveat*: Stripe is enterprise-grade and overspecified for our scale. We borrow the patterns selectively.

**GitHub Issues** (list view as URL state)

- *What it does well*: Querystring is the source of truth. Every filter, sort, and search is in the URL. Reload, back-button, bookmark — all work because state is in the URL.
- *What we learn*: Validates the **State lives in the URL** principle. The pattern is mature, well-tested, and zero-cost to implement on a Laravel backend (already partially in place via `->withQueryString()`).
- *Caveat*: GitHub's query syntax (`is:open author:foo`) is a power-user pattern; we use plain query params.

**Vyapar** (Indian SMB invoicing app, phone-first)

- *What it does well*: Mobile-priority column ordering — phone view shows date, amount, status; secondary fields collapse. Targeted at the same persona band (small-shop owner-operator).
- *What we learn*: The mobile-priority model is a known pattern in this market; the owner won't find it foreign. Validates the per-list priority-order config.
- *Caveat*: Vyapar is mobile-first; we are desktop-first with mobile-supportive. Don't import Vyapar's mobile-only design language.

### Transferable UX Patterns

**Navigation patterns**

- **Persistent left sidebar with current-module highlight** (already exists in `admin.blade.php`) — keep, refine for responsive collapse to hamburger at narrow viewports.
- **URL-as-state querystring filters** (GitHub Issues / Linear) — every list filter / sort / page in the URL.
- **Breadcrumb-style "back to list" return path** after edit/show actions — Stripe pattern; cheap addition that preserves filter context.

**Interaction patterns**

- **Filter bar always visible** above the table (Linear / Stripe) — never collapsed behind a drawer. Inline-debounced or auto-submit on field blur; no "Apply Filters" button.
- **Removable filter chips** below the filter bar showing active filters (Linear) — quick at-a-glance "what am I looking at?" plus one-click filter removal.
- **Sortable column headers with subtle indicator** (Linear / Stripe) — click to sort asc, click again for desc, indicator (arrow + active styling) shows current sort. Server-side sort.
- **Aggregate totals reflecting active filter** (Stripe) — the "Total" row at the top of the list updates with the filter, never shows the unfiltered total.
- **Inline row-action menu** (kebab `⋮` menu) for edit/delete/export-row (GitHub / Linear) — instead of a wide "Actions" column.
- **Mobile-priority column ordering with detail-row tap-to-expand** (Vyapar / Notion databases) — per-list config of priority columns; secondary columns collapse.
- **Specific-record destructive confirmation** (Stripe) — "Delete sale #123 of ₹4,500 dated 2026-05-08?" not "Are you sure?".

**Visual patterns**

- **Right-aligned currency columns with consistent decimal formatting** (Tally / Stripe) — always `₹ X,XXX.XX`, right-aligned, monospace-friendly column treatment.
- **Density-friendly table rows** (Tally / Linear) — avoid over-padded rows; the owner wants more rows visible per screen, not fewer.
- **Subtle source/freshness labels on dashboard tiles** (Stripe) — small, secondary-text, but present.
- **Badge / pill component for status** (Linear) — color-coded but muted (not saturated); status = `paid` / `pending` / `partial` rendered as a pill.
- **No-results empty state with actionable guidance** ("No purchases match this filter — clear filters to see all" + a button) — better than silent empty list.

### Anti-Patterns to Avoid

These conflict with the principles established in Steps 3–4:

1. **Hidden filters behind a drawer or "Filters" button.** Forces an extra click on every list visit; conflicts with the **filter bar always visible** principle. Owner uses filters every time — make them frictionless.
2. **"Apply Filters" button friction.** Auto-submit or inline-debounced is the standard now (Linear / Stripe). Forcing the owner to click "Apply" after every field change is dated and slow.
3. **Mobile-only redesign / separate phone screens.** Conflicts with **mobile-priority columns, not mobile-redesigned screens**. The phone view must be the desktop view with secondary columns collapsed — never a different page.
4. **Generic "Are you sure?" confirmation modals.** No specifics → no confidence. Always name the record being deleted ("Delete sale #123…").
5. **Celebration toasts / confetti / "Well done!" success messages.** Clashes hard with **calm over delight**. Success is silent; only errors and destructive confirmations need explicit feedback.
6. **Inline column-header filters (Excel-style filter funnels).** Too cramped at 320px, hard to scan on desktop. The filter bar above the table is the right surface.
7. **Card-grid layouts for tabular data.** Wastes the screen density that this owner explicitly needs. Cards for dashboard tiles, tables for everything else.
8. **Animated transitions over 300ms.** The owner is repeating actions; animations slow him down. Cap transitions at 200ms; default to instant where possible.
9. **Auto-refresh / polling on lists or dashboard.** Owner refreshes manually; auto-refresh would surprise him and could create stale-data confusion mid-action.
10. **Heavy onboarding tooltips / coach marks.** This is a one-user system the owner has used for a year. No "did you know" tooltips, no first-run wizards. Help via documentation, not in-app coaching.
11. **Filter that mismatches the export.** If the export doesn't honor the filter, the export is worse than useless — it actively breaks trust. Anti-pattern strongly enforced.
12. **Status as raw text** ("paid", "pending"). Use a badge/pill with consistent color across modules — same status, same color, everywhere.

### Design Inspiration Strategy

**Adopt (use directly)**

- **GitHub-style URL-as-state** for all list view filter/sort/page params
- **Stripe-style trust signals** on dashboard tiles (source label + freshness marker)
- **Linear-style filter bar + chips** for active filter visualization
- **Tally-style data density** (more rows per screen, less padding)
- **Stripe-style specific-record destructive confirmations**
- **Right-aligned tabular currency** with consistent `₹ X,XXX.XX` formatting

**Adapt (modify for our context)**

- **Linear's column sort interaction** — adopt the click-to-toggle direction pattern, but skip Linear's drag-to-reorder columns (out of scope; per-list priority config covers this)
- **Vyapar's mobile-priority columns** — adopt the priority concept, but make it Tailwind-utility responsive (no separate mobile layer)
- **Stripe's "Export" button** — adopt the placement (next to filter bar) and the contract (export = visible rows), but use existing Maatwebsite Excel + DomPDF instead of building new export infra
- **Linear's badges for status** — adopt the pill pattern, but use Tailwind utility classes directly rather than building a dedicated component upfront (reserved for the canonical-component consolidation later in Epic 3)

**Avoid (explicitly off the menu)**

- **Tally's keyboard-only flow** — keyboard-friendly tab order is enough; full keyboard-driven flows are out of scope
- **Linear's design system maturity** — we're not building a design system; we're composing Tailwind utilities into named patterns
- **Stripe's enterprise data viz** — no charts beyond what's already in the dashboard via Chart.js
- **Vyapar's mobile-first defaults** — phone is utilitarian, desktop is primary
- **Modern SaaS animation language** — calm, ≤200ms, no celebration moments

## Design System Foundation

### Design System Choice

> **Choice: Themeable system — Tailwind CSS 3 (existing) + a thin custom pattern layer.**
>
> Not a custom design system. Not an established system (Material/Ant). Not a heavyweight themeable system (MUI/Chakra). Tailwind 3 + `@tailwindcss/forms` (already in the codebase) plus a small set of named patterns implemented as Blade partials and Vue components.

This is a deliberate non-decision: the Tailwind stack is already in place across both Inertia/Vue and Blade rendering layers. Choosing it again is the path of least cost and full alignment with Step 3's principle *Tailwind utility composition over a new design system*.

### Rationale for Selection

**Why themeable (path 3) and not custom (path 1) or established (path 2):**

- **Custom design system is overkill.** Building a token system, custom components, brand visual identity for a single-user internal tool would burn the entire MVP budget on infrastructure that one user doesn't need. Owner explicitly asked for "no regressions" — building a design system is the opposite of that.
- **Established system would force a rewrite.** Adopting Material UI / Ant Design / Bootstrap means rewriting every existing Blade view + Vue component to match the new framework. Massive scope creep. Conflicts with the no-regression constraint.
- **Tailwind already serves both renderers.** The hybrid Inertia/Blade architecture means whatever design system we pick must work in both contexts. Tailwind already does — no other choice has this property without significant adaptation.
- **The patterns we need are not visual novelty; they're consistency.** Filter bar, sortable column header, status badge, export button — these are layout patterns, not bespoke components. Tailwind utility composition handles them well; a heavier framework adds dependency cost without proportional payoff.
- **Existing component duplicates (Modal/NewModal, Pagination/PaginationNew, SearchableDropdown variants) are tracked for Epic 3 consolidation.** Not reinventing them; consolidating them.

**Why this choice satisfies the emotional and core-experience principles:**

| Principle | How this choice supports it |
|---|---|
| One pattern, applied everywhere | Named Blade/Vue patterns enforce consistency across modules |
| State lives in the URL | Independent of design system; standard Laravel + Vue practice |
| Mobile-priority columns | Tailwind responsive utilities (`hidden md:table-cell`, etc.) handle this natively |
| Calm over delight | Tailwind defaults are calm; no animation library, no celebration patterns |
| Tailwind utility composition over a new design system | Direct alignment |

### Implementation Approach

**The pattern layer to build (Epic 2):**

| Pattern | Form | Purpose | Renderer |
|---|---|---|---|
| `x-list-filter-bar` | Blade component | Encapsulates date range, payment status, search input, action buttons | Blade (admin) |
| `x-list-table` | Blade component | Sortable header wrapper, server-side sort indicator, no-results empty state | Blade (admin) |
| `x-list-pagination` | Blade component | Wraps the existing Tailwind paginator with consistent placement | Blade (admin) |
| `x-list-export-buttons` | Blade component | XLSX + PDF buttons positioned next to filter bar | Blade (admin) |
| `x-status-badge` | Blade component | Color-coded pill for status values (paid/pending/partial/etc.) | Blade (admin) |
| `x-currency` | Existing Blade component | Keep as-is | Blade (admin) |
| Vue equivalents | Vue components | Mirror the above for the Inertia screens that need lists (today: User/List, Role/List, Activity/ActivityLog) | Vue (Inertia) |

**Existing components to consolidate (Epic 3):**

- `Modal.vue` ↔ `NewModal.vue` → pick one canonical
- `Pagination.vue` ↔ `PaginationNew.vue` → pick one canonical
- `SearchableDropdown.vue` ↔ `SearchableDropdownNew.vue` ↔ `SearchableMultiselect.vue` → pick canonical(s)
- Toast variants (`ToastNotification*.vue` × 4) → audit and consolidate

**What we explicitly do NOT build in this initiative:**

- A design token system (CSS variables, theme tokens) — Tailwind defaults are sufficient
- A Tailwind plugin or preset — adding configuration complexity for one user is wasted effort
- Headless UI primitives library (Radix, Headless UI, etc.) — Tailwind + existing components cover the surface
- A Storybook / component playground — overhead doesn't pay off for a one-developer team
- A typography scale or font system — Tailwind's default (system fonts) is fine
- A formal accessibility audit / WCAG compliance — flagged out-of-scope in PRD; revisit if multi-user PRD lands

### Customization Strategy

**Color palette** — Tailwind 3 defaults. No brand color system. The `CompanyProfile` singleton provides logo/favicon as the only brand surface; UI colors are utility-first.

| Use | Tailwind classes |
|---|---|
| Primary action buttons | `bg-blue-600 hover:bg-blue-700 text-white` |
| Secondary action buttons | `bg-white hover:bg-gray-50 text-gray-700 border-gray-300` |
| Destructive actions | `bg-rose-600 hover:bg-rose-700 text-white` |
| Body text | `text-gray-900` (headings), `text-gray-700` (body), `text-gray-500` (secondary) |
| Backgrounds | `bg-white` (card), `bg-gray-50` (page), `bg-gray-100` (subtle row stripe if used) |
| Borders | `border-gray-200` (default), `border-gray-300` (input) |

**Status badge color mapping** — fixed across all modules for cross-module consistency:

| Status | Tailwind classes (background + text) |
|---|---|
| `paid` / `active` / `success` | `bg-emerald-100 text-emerald-800` |
| `pending` | `bg-amber-100 text-amber-800` |
| `partial` | `bg-sky-100 text-sky-800` |
| `failed` / `cancelled` | `bg-rose-100 text-rose-800` |
| `inactive` / `neutral` | `bg-gray-100 text-gray-700` |
| `gst` (bill type) | `bg-indigo-100 text-indigo-800` |
| `without_gst` (bill type) | `bg-gray-100 text-gray-700` |

These map onto the model-defined status constants from PRD Epic 1 (e.g., `Sale::PAYMENT_STATUS_PAID` → `paid` badge variant).

**Typography** — Tailwind defaults; system font stack. No custom font, no Google Fonts dependency.

**Spacing / sizing** — Tailwind defaults. Tables prefer dense rows (`py-2` not `py-4`) per Tally-density principle.

**Form inputs** — `@tailwindcss/forms` plugin (already in Tailwind config) handles input baseline. Keep as-is.

**Iconography** — Inline SVGs cover the few icons needed. No new icon library introduced unless absolutely required.

### Trade-offs (acknowledged)

- **Accessibility for modal/dropdown/dialog interactions is on us.** Choosing not to adopt headless UI primitives means we hand-roll focus management, escape-to-close, etc. Acceptable for a single-user internal tool; flagged for the future multi-user PRD.
- **No design tokens means the spec is brittle to brand changes.** Acceptable: there is no brand and no anticipated brand change.
- **Visual differentiation will be low.** That's fine — the goal is calm competence, not visual identity.
- **Tailwind class proliferation in templates.** Mitigated by the named pattern components (Blade partials, Vue components) that wrap repeated utility combinations.

## Defining Core Experience

### Defining Experience

> **The defining experience for parampara is: "Find any record on any list view in under 30 seconds, with one consistent interaction pattern across all 11 admin modules."**
>
> This is the redesign's product-defining moment. Everything else — dashboard trust signals, stock-closing persistence, the export contract, responsive collapse — supports or fails based on whether this one interaction works. If the owner can't find a record fast and consistently, the redesign hasn't shipped, regardless of what else is built.

The defining experience has three constituent moves the owner makes in sequence:

1. **Narrow** — apply date range / payment status / search term to scope the list.
2. **Sort** — click a column header to order results.
3. **Read or act** — read the row's values, or act on it (view, edit, delete, export).

The narrow + sort moves are where the entire UX leverage lives. The read/act move uses existing flows (already working). All redesign value flows through "narrow + sort."

### User Mental Model

The owner's mental model of his data is **the physical paper ledger his father kept**. He thinks in terms of:

- **When** it happened (date — always the first dimension)
- **Who** was involved (supplier name on purchases; customer/seller name on sales)
- **What state** it's in (paid / pending / partial; gst / non-gst)
- **How much** (total, by category, by payment mode)

Current workaround when the UI fails him: opens phpMyAdmin and runs SQL. He knows the schema well enough to do this; the friction is friction with the UI, not lack of technical capability. **The new UX must give him the same speed and certainty as his SQL queries — through the UI.** That sets the bar.

What he expects (carried over from Tally Prime, his accounting muscle memory, and modern web app conventions):

- Date filter is always there and always at the top
- Filtered view = filtered totals
- Sort by column should "just work"
- The list view URL is the canonical "where I am" — bookmarkable, refreshable
- No "Apply" button — typing means filtering
- Export gives him what's on screen, nothing more

Where he's likely to get confused or frustrated:

- Filters that don't auto-submit (he'll forget to click Apply)
- Filters that get reset on page navigation
- Search that searches "everything" without telling him which fields it's matching
- Lists where some columns are sortable and others aren't, with no indicator
- Mobile views that require horizontal scrolling — he's done with that

### Success Criteria

The defining experience succeeds when, on the **Sales list (the first module to ship)**, every one of these holds:

1. **Time-to-find ≤ 30 seconds** for any of these tasks (PRD-aligned, refined per Journey 1):
   - "Yesterday's sales" — date filter + search empty + status empty
   - "Pending purchases this month" — date range + status filter
   - "All sales over ₹10,000" — sort by total desc, scan top rows
   - "Sales for supplier 'Aman'" — search input
2. **Filter changes auto-apply** within ≤ 500ms of the input losing focus or debounce settling. No "Apply Filters" button.
3. **URL updates on every filter / sort / page change** so reload, back-button, and bookmark all preserve state.
4. **Aggregate header reflects the filter** — the "Total" / "Count" displayed at the top of the list is the filtered total, not the unfiltered set.
5. **Empty state is actionable** — when filters return no rows, the empty state shows the active filters and a "Clear all filters" button, not a silent blank table.
6. **Sortable column headers visibly indicate state** — current sort column has an arrow icon (asc / desc); other sortable columns have a subtle hover affordance to invite clicking.
7. **Export from the filtered view returns exactly the visible rows** — no more, no less.
8. **Mobile (320px) shows priority columns** — date, customer/supplier, total, status — without horizontal scrolling. Secondary columns are accessible via tap-to-expand detail row.

If any one of these fails on Sales, the pattern isn't ready to roll out to the other 10 modules.

### Novel UX Patterns (and lack thereof)

**The defining experience uses entirely established UX patterns.** None of them are novel:

- URL-state filters (GitHub Issues, ~10 years old)
- Filter chips with click-to-remove (Material Design, Linear, Gmail, ~10 years old)
- Auto-submit on filter change (Stripe Dashboard, Linear, modern norm)
- Sortable column headers with directional indicator (every spreadsheet, every admin panel ever)
- Filtered aggregates (Stripe Dashboard, Excel, financial tools)
- Mobile-priority column collapse (Vyapar, Notion databases)

**The novelty is operational, not interactional.** What's "new" for parampara is:

- **Applying these patterns consistently across 11 modules** that today each have their own ad-hoc list view
- **Doing it without introducing a design system** that would force the rest of the codebase to migrate
- **Making it work in both Blade and Vue/Inertia render contexts** without picking a side first

User education / onboarding cost: **zero**. The owner already understands every pattern from prior tools. There's no learning curve.

### Experience Mechanics

The step-by-step flow for the canonical "Find yesterday's sales" task on the Sales list:

**1. Initiation**

- Owner clicks "Sales" in the persistent left sidebar (already works today; preserved).
- Lands on `/admin/sales` with the filter bar visible above the table.
- Default view: recent sales paginated (e.g., 20 per page); aggregate at top shows current page's count + total.
- No empty state by default — the list always shows something on first arrival.

**2. Interaction (the core moment)**

- Owner clicks the **date-range** filter input. A small dropdown shows shortcut chips ("Today", "Yesterday", "This Week", "This Month") + a "Custom" option that reveals two date inputs.
- Owner selects "Yesterday".
- The filter **auto-submits** — no "Apply" button. The URL updates: `/admin/sales?date_from=2026-05-08&date_to=2026-05-08`.
- The page re-renders server-side with the filtered set. Filter chip below the filter bar reads `📅 May 8, 2026 ✕`.
- Aggregate at top updates: e.g., `8 sales · ₹12,450 total`.
- (Optional refinement) Owner types "Aman" in the **search** input; debounces 300ms after last keystroke; auto-submits; URL adds `&q=Aman`; results narrow further; aggregate updates.
- (Optional refinement) Owner clicks the **"Total"** column header; server re-renders sorted by total descending; URL adds `&sort=total&dir=desc`; arrow indicator (▼) appears next to "Total".

**3. Feedback during interaction**

- **URL is the visible source of truth** — the address bar shows exactly the current filter / sort / page state.
- **Filter chips** below the filter bar visualize active filters; clicking the ✕ on a chip removes that filter.
- **Aggregate counter** at the top of the list always reflects the filtered set.
- **Loading state** during a filter change: subtle spinner in the top-right of the table, or a shimmer on the table rows while the server responds. Page does not blank out; the previous state remains visible until the new state arrives.
- **Empty state**: when filters return no rows, the table area shows "No sales match these filters" + a button "Clear all filters" that resets the URL to `/admin/sales` and reloads the unfiltered view.
- **Server errors** (rare): an inline error banner appears above the filter bar with a specific message ("Filter failed — try a smaller date range") — never a blocking modal.
- **Validation errors** (e.g., date_to before date_from): inline message under the offending input, filter doesn't submit until valid.

**4. Completion**

- Owner has found the row he wants and chooses how to act:
  - **Click the row** → navigates to `/admin/sales/{id}` (existing show view, unchanged).
  - **Click the kebab `⋮` menu** on the row → reveals Edit / Delete / View actions (existing).
  - **Click "Export XLSX" or "Export PDF"** at the top of the list → downloads exactly the filtered rows.
  - **Just read** the data and close the tab.
- If the owner navigated to a row's show/edit view, the **breadcrumb / "Back to Sales" link** returns him to the same filtered list state (because the URL preserves it via `withQueryString()`).
- If the owner closed the tab and comes back, **bookmarking the filtered URL** brings him back to exactly the same view.

**Edge cases handled by design**

- **Combination produces no results** → empty state with "Clear all filters" CTA (above)
- **Slow query at scale** → loading indicator; previous state remains visible (no blank screen)
- **Invalid date range** → inline validation; filter doesn't apply until corrected
- **Tab / Shift+Tab navigation** through filter inputs respects natural reading order (date_from → date_to → status → search → table)
- **Browser back-button** after applying a filter → returns to the previous URL state (which, because state is in URL, is the previous filter state)
- **Mobile (320px)** → filter bar stacks vertically; date input becomes native HTML `<input type="date">` (which on mobile invokes the OS date picker); status filter becomes a `<select>`; search input remains text. Aggregate header collapses to one line above the table.

This is the defining experience. Once it works for Sales, the same pattern propagates to Purchases, Products, Sale-Returns, Purchase-Returns, Sale-Invoices, Expenses, Expense-Categories, Payments, Stock, Stock-Closings — with each module declaring its priority columns and filter set, and inheriting the rest from the shared `x-list-filter-bar` / `x-list-table` / `x-list-export-buttons` / `x-list-pagination` Blade components.

## Visual Design Foundation

### Brand Assessment

**No formal brand guidelines exist.** The `CompanyProfile` singleton stores the shop's logo, favicon, GST number, and contact info — but no color palette, typography preference, or design system documentation. The owner has not specified brand colors.

This is consistent with the project's nature: a single-tenant internal tool with no public surface, no marketing, no customer touchpoints. The "brand" surface is effectively the logo + favicon, both already loaded by the admin Blade layout from `CompanyProfile::first()`.

**Decision:** generate the visual foundation from the emotional goals (Step 4: calm competence, trust before delight) and the design system choice (Step 6: Tailwind utility composition), rather than from a brand. If brand guidelines emerge later, only the color palette would shift; the rest of the foundation is brand-agnostic.

### Color System

**Strategy:** Tailwind 3 default palette, used semantically rather than decoratively. Every color choice maps to a meaning, not to an aesthetic preference. Cross-module consistency is enforced — the same status (e.g., "paid") uses the same color in every list, every badge, every detail view.

#### Semantic palette

| Token / role | Tailwind class | Use |
|---|---|---|
| **Primary action** | `bg-blue-600` / hover `bg-blue-700` / text `text-white` | CTA buttons (Save, Create, Apply), primary links, active sidebar item |
| **Primary focus ring** | `ring-blue-500/40` | Focus states on inputs and buttons |
| **Secondary action** | `bg-white` / hover `bg-gray-50` / border `border-gray-300` / text `text-gray-700` | Cancel, Reset, less-critical buttons |
| **Destructive action** | `bg-rose-600` / hover `bg-rose-700` / text `text-white` | Delete buttons, destructive confirmations |
| **Page background** | `bg-gray-50` | Outermost page chrome |
| **Card / container** | `bg-white` | Tables, forms, dashboard tiles |
| **Subtle row stripe / hover** | `bg-gray-100` | Optional zebra stripes; row hover |
| **Border (default)** | `border-gray-200` | Card borders, table dividers |
| **Border (input)** | `border-gray-300` | Form input borders |

#### Text colors

| Use | Tailwind class |
|---|---|
| Heading (H1 / H2) | `text-gray-900` |
| Body text | `text-gray-700` |
| Secondary / metadata (timestamps, helpers) | `text-gray-500` |
| Disabled / placeholder | `text-gray-400` |
| Currency amounts (in tables) | `text-gray-900` (amounts deserve weight) |
| Inline link | `text-blue-600 hover:text-blue-700 underline` |
| Error text | `text-rose-600` |

#### Status badge palette (cross-module consistency)

This is the load-bearing semantic mapping — these colors must NEVER mean different things in different modules:

| Status | Background + text | Used for |
|---|---|---|
| `paid` / `active` / success outcome | `bg-emerald-100 text-emerald-800` | Sale paid, Product active, Payment paid |
| `pending` | `bg-amber-100 text-amber-800` | Sale pending, Payment pending |
| `partial` | `bg-sky-100 text-sky-800` | Sale partial payment status |
| `failed` / `cancelled` | `bg-rose-100 text-rose-800` | Payment failed/cancelled |
| `inactive` / `neutral` | `bg-gray-100 text-gray-700` | Product inactive, generic neutral |
| `gst` (bill type) | `bg-indigo-100 text-indigo-800` | Purchase GST bill |
| `without_gst` (bill type) | `bg-gray-100 text-gray-700` | Purchase non-GST bill |
| `cash` (payment mode) | `bg-emerald-50 text-emerald-700` | Sale payment mode = cash |
| `upi` / `gpay` (payment mode) | `bg-indigo-50 text-indigo-700` | Sale payment mode = UPI / GPay |
| `mix` (payment mode) | `bg-violet-50 text-violet-700` | Sale payment mode = mix |

These map onto the model-defined constants per PRD Epic 1 (e.g., `Sale::PAYMENT_STATUS_PAID` → `paid` badge variant; `SaleInvoice::TYPE_CASH` → `cash` badge variant).

#### Contrast notes

All foreground/background pairings above are checked for **WCAG 2.1 AA compliance for body text (4.5:1)**:

- `text-gray-900` on `bg-white` → ~17:1 (AAA)
- `text-gray-700` on `bg-white` → ~9.7:1 (AAA)
- `text-emerald-800` on `bg-emerald-100` → ~6.2:1 (AA)
- `text-amber-800` on `bg-amber-100` → ~5.8:1 (AA)
- `text-rose-600` on `bg-white` → ~5.1:1 (AA)
- `text-blue-600` on `bg-white` → ~5.9:1 (AA)

WCAG AAA (7:1) is **not** the target per the emotional/scope framing (PRD declares no compliance commitment); but AA-level contrast is observed by default because the chosen Tailwind shades are conservative.

### Typography System

**Strategy:** system font stack via Tailwind's `font-sans` default. No Google Fonts dependency, no custom font files, no font-loading FOUT/FOIT to manage. Tone is professional-functional.

#### Type scale

| Role | Tailwind classes | Use |
|---|---|---|
| Page heading (H1) | `text-3xl font-bold text-gray-900` | "Sales", "Dashboard", "Stock Closings" — top of every admin page |
| Section heading (H2) | `text-xl font-semibold text-gray-900` | Section titles within a page (e.g., "Filter", "Recent Activity") |
| Card / table heading (H3) | `text-base font-medium text-gray-900` | Card titles, table column headers |
| Body | `text-sm text-gray-700` | Default body text in tables, paragraphs, form labels |
| Body (emphasis) | `text-sm font-medium text-gray-900` | Currency amounts, status badges |
| Metadata / caption | `text-xs text-gray-500` | Timestamps, footer info, secondary fields on dashboard tiles |
| Inline label | `text-xs font-medium uppercase tracking-wide text-gray-500` | Definition-list-style labels in show views |

#### Currency typography (special case)

Currency values in tables and totals use `tabular-nums` (Tailwind utility) so digits align vertically across rows. Right-aligned in table columns. Format: `₹ X,XXX.XX` via the existing `<x-currency>` Blade component.

```html
<td class="px-3 py-2 text-right text-sm font-medium text-gray-900 tabular-nums">
    <x-currency :value="$sale->total_amount" />
</td>
```

#### Line heights and density

Tailwind defaults are kept (`leading-tight`, `leading-snug`, `leading-normal`). Tables use `leading-snug` for density; body paragraphs use `leading-normal` for readability. No custom `line-height` overrides.

#### Font pairing rationale

Single font (system stack). No pairing. The owner's reading is data-driven (numbers, names, dates) — no long-form content needs a secondary font. Single-font system also keeps the Tailwind class footprint small and renders identically across desktop and mobile.

### Spacing & Layout Foundation

**Strategy:** Tailwind 4px-based scale, density-first per the Tally inspiration. Owner wants more rows per screen, not more whitespace.

#### Spacing tokens (Tailwind defaults)

| Token | Value | Use |
|---|---|---|
| `space-1` (4px) | 4px | Tight inline gaps (icon-to-text) |
| `space-2` (8px) | 8px | Default inter-element gap (filter inputs, button groups) |
| `space-3` (12px) | 12px | Table cell padding (`px-3 py-2`) |
| `space-4` (16px) | 16px | Card internal padding default; form field vertical spacing |
| `space-6` (24px) | 24px | Section vertical spacing; emphasized card padding |
| `space-8` (32px) | 32px | Page-level vertical sections |

#### Page-level layout

```
┌───────────────────────────────────────────────────────────────────┐
│ Top bar (logo + user menu + notifications)         h-16 bg-white  │
├──────────────┬────────────────────────────────────────────────────┤
│              │ Page chrome: max-w-7xl mx-auto px-4 sm:px-6 lg:px-8│
│  Sidebar     │ ┌──────────────────────────────────────────────┐   │
│  w-64        │ │ H1: Page title                              │   │
│  bg-white    │ ├──────────────────────────────────────────────┤   │
│  border-r    │ │ Filter bar (filter inputs + chips + export) │   │
│              │ ├──────────────────────────────────────────────┤   │
│              │ │ Table or content                            │   │
│              │ ├──────────────────────────────────────────────┤   │
│              │ │ Pagination                                  │   │
│              │ └──────────────────────────────────────────────┘   │
└──────────────┴────────────────────────────────────────────────────┘
```

- **Top bar**: `h-16 bg-white border-b border-gray-200`
- **Sidebar**: `w-64 bg-white border-r border-gray-200` on `md:` and up; collapses to `w-0` (hamburger overlay) on mobile
- **Page container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6`
- **Vertical rhythm within page**: `space-y-6` between major sections (filter bar → table → pagination)

#### Table density rules

| Element | Spacing |
|---|---|
| Cell padding | `px-3 py-2` (denser than Tailwind default `py-4`) |
| Header padding | `px-3 py-2` matching cells |
| Row hover | `hover:bg-gray-50` |
| Row borders | `divide-y divide-gray-200` between rows |
| Header bottom border | `border-b border-gray-200` |

#### Form layout

| Element | Spacing |
|---|---|
| Vertical gap between fields | `space-y-4` |
| Horizontal gap between paired fields | `gap-4` |
| Form section gap | `space-y-6` |
| Mobile (<sm:) | All fields stack with `space-y-4`; no horizontal pairing |
| Desktop (`sm:` and up) | Related fields pair via `grid grid-cols-2 gap-4` |

#### Filter bar layout

| Element | Spacing |
|---|---|
| Filter row | `flex flex-wrap gap-2` (wraps when narrow) |
| Date range pair | `flex gap-2` (always paired) |
| Chip row (active filters) | `flex flex-wrap gap-2 mt-2` |
| Mobile (<sm:) | Filter bar stacks vertically with `space-y-2` |

#### Dashboard grid

| Breakpoint | Tile grid |
|---|---|
| Mobile (default) | `grid-cols-1` |
| Tablet (`md:`) | `md:grid-cols-2` |
| Desktop (`lg:`) | `lg:grid-cols-3` |
| Wide desktop (`xl:`) | `xl:grid-cols-4` |

Tile gap: `gap-4`. Tile padding: `p-6`.

#### Layout principles

1. **Density over whitespace.** Tables, lists, and forms favor more content per screen. Modern SaaS tendency toward generous whitespace is rejected.
2. **Persistent sidebar is the navigation backbone.** Always visible on desktop; collapsible (hamburger) on mobile. Never replaced with bottom nav or top tabs.
3. **Filter bar is non-collapsible** above tables. Direct, frictionless access to the most-used interaction.
4. **Mobile collapses, never redesigns.** Sidebar → hamburger; tables → priority columns + detail row; filter bar → vertical stack. Same templates, responsive utilities only.
5. **Currency right, status center, names left.** Tabular convention enforced consistently.

### Accessibility Considerations

**Calibrated honestly per the PRD's no-compliance-commitment stance.** Not chasing WCAG AAA; observing AA-level contrast and basic-tier keyboard accessibility because they cost nothing additional with Tailwind defaults.

#### What's observed

- **Color contrast** — all body text / background pairs meet WCAG 2.1 AA (4.5:1 for normal text). Status badges' dark-on-light combinations all clear AA.
- **Status conveyed by text + color, never color alone** — paid badge always says "Paid" *and* renders emerald; sort indicator is an arrow icon *and* color change. Color-blind users get the same information.
- **Keyboard navigation** — tab order respects natural reading order on forms; sidebar links are real `<a>` tags with `href`; sortable column headers are real `<button>` elements with `aria-sort` attribute reflecting current state.
- **Visible focus rings** — Tailwind's default focus ring (`focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-2`) is preserved. No `outline:none` overrides without a replacement.
- **Form labels** — every form input has a visible `<label>` with `for=` matching `id=`. Continues the existing convention.
- **Modals (hand-rolled, since we're not adopting Headless UI)** — escape-to-close, focus-trap on open, restore focus to trigger on close. To be implemented as part of Epic 3 modal consolidation.
- **Touch targets at 320px** — interactive elements (buttons, kebab menus, filter chips' ✕) sized `min-h-[44px]` on mobile per platform conventions.
- **Semantic HTML** — `<table>` for tabular data (not `<div>`-based grids); `<button>` for buttons; `<a>` for navigation. Continues existing convention.

#### What's NOT committed to

- **WCAG AAA** compliance (not the target)
- **Screen reader audit** — no formal NVDA/JAWS/VoiceOver testing planned
- **Prefers-reduced-motion** support — there are no significant motion designs to reduce; Tailwind transitions (≤200ms hover/focus) are subtle enough
- **High-contrast mode** — no Windows high-contrast media query handling
- **Multi-language / RTL** — single-language app per PRD scope

If multi-user RBAC PRD lands later, accessibility expectations will likely tighten; this foundation is compatible with a future hardening pass.
