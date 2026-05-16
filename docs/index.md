# Parampara Documentation Index

Generated 2026-05-09 via `bmad-document-project` (deep scan, initial documentation pass).

## Project overview

- **Type**: monolith — single-tenant fullstack web app (`web` per BMad classification)
- **Primary stack**: PHP 8.1 + Laravel 10 + Vue 3 (Inertia) + Tailwind 3 + MySQL/MariaDB
- **Architecture**: MVC + Service Layer + FormRequest validation, with **hybrid Inertia/Blade rendering**
- **Status**: ~95% feature-complete; brownfield improvement initiative kicked off 2026-05-09 (no regressions allowed)
- **Active branch**: `code-improve`

## Quick reference

- **Tech stack**: see [project-overview.md](project-overview.md)
- **Architecture & invariants**: see [architecture.md](architecture.md)
- **Entry point**: HTTP — [public/index.php](../public/index.php) → routes/web.php → controllers; SPA — [resources/js/app.js](../resources/js/app.js) (Inertia bootstrap with mount-node guard)
- **Architecture pattern**: hybrid server-rendered Blade (admin business modules) + Inertia/Vue SPA (auth, users, roles, settings, activity log)

## Generated documentation

- [Project Overview](project-overview.md) — what this is, status, navigation
- [Architecture](architecture.md) — layered structure, key invariants, strengths & weaknesses
- [Source Tree Analysis](source-tree-analysis.md) — annotated directory tree with critical folders called out
- [Data Models](data-models.md) — all 19 Eloquent models, schema, relationships, status enum catalog
- [API Contracts](api-contracts.md) — every route, JSON endpoint, export endpoint
- [Component Inventory](component-inventory.md) — Vue 3 components & pages + Blade views catalog
- [Development Guide](development-guide.md) — setup, run, test, conventions
- [Deployment Guide](deployment-guide.md) — current XAMPP-based deploy + productionization roadmap
- [Gaps & Risks](gaps-and-risks.md) — **★ audit findings prioritized for the improvement initiative**

## Existing documentation (project)

- [README.md](../README.md) — default Laravel boilerplate (no project-specific content) — see [G-23](gaps-and-risks.md) to update
- [_bmad-output/project-context.md](../_bmad-output/project-context.md) — lean rule sheet for AI agents working in this repo
- [_bmad-output/](../_bmad-output/) — BMad artifacts directory
- No CONTRIBUTING.md, no ARCHITECTURE.md (this folder is now your architecture doc), no .env.example

## Existing documentation (BMad)

- [_bmad/bmm/config.yaml](../_bmad/bmm/config.yaml) — module config (paths, language, user)
- [_bmad/core/config.yaml](../_bmad/core/config.yaml) — core BMad config

## Getting started

### For new developers
1. Read [project-overview.md](project-overview.md) for the 60-second overview
2. Follow [development-guide.md](development-guide.md) for setup
3. Skim [architecture.md](architecture.md) — pay attention to "Key invariants" so you don't break things silently

### For AI agents working on this codebase
1. Always read [_bmad-output/project-context.md](../_bmad-output/project-context.md) first — it's the lean rule sheet
2. Then [architecture.md](architecture.md) for invariants
3. Then [data-models.md](data-models.md) for the schema
4. For specific tasks, jump to the relevant subsystem doc

### For the improvement initiative (PRD / epics planning)
1. Start with [gaps-and-risks.md](gaps-and-risks.md) — every finding mapped to the owner's goals + recommended epic sequencing
2. The PRD scope is captured in `~/.claude/projects/d--xammp8-1-htdocs-parampara/memory/project_parampara_overview.md` (memory file)
3. Next BMad workflow: `bmad-create-prd` → consume this docs/ folder as input

## Key invariants (must-not-break)

These rules are documented in detail in [architecture.md](architecture.md#key-invariants-must-not-break) — TL;DR:

1. `Sale::recalculatePaymentStatus()` is the only writer of `payment_status` / `pending_amount` on a Sale
2. Mix payments: `cash_amount + online_amount = amount_paid`
3. SaleInvoice 3-FK split (cash/online/mix) — at most one invoice per (date, type)
4. `Product::stock_quantity` is the source of truth for stock; legacy `stocks` table is NOT
5. Schema rename in flight: `sell → sale` — never use `sell*` in new code
6. Inertia mount-node check in [resources/js/app.js](../resources/js/app.js) is load-bearing — Blade pages depend on it
7. Status string literals are forbidden — use model-defined constants

## Workflow state

- **bmad-document-project state**: `docs/project-scan-report.json` (resume / re-scan inputs)
- **Scan level**: deep
- **Mode**: initial_scan (no prior docs existed)
- **Files generated**: 9 (this index + 8 supporting docs) + state file = 10
