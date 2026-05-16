# API & Route Contracts

Generated 2026-05-09 via `bmad-document-project` (deep scan). Source: [routes/web.php](routes/web.php), [routes/auth.php](routes/auth.php), [routes/api.php](routes/api.php).

> **Note**: this is a session-based Laravel app. "API contracts" here means *all routes the app exposes* — both Inertia/Blade page routes and JSON endpoints. There is no separate REST API surface beyond two trivial Sanctum-protected endpoints.

## Conventions

- All admin business routes live under `Route::prefix('admin')->name('admin.')`. Names follow `admin.<resource>.<action>`.
- Cross-cutting routes (users, roles, profile, setting) are NOT under the admin prefix. Names are bare (`users.index`, `roles.store`, etc.).
- Auth routes (Breeze defaults) live in [routes/auth.php](routes/auth.php) under `guest`/`auth` middleware groups.
- Inertia pages return `Inertia::render(...)`. Blade pages return `view(...)`.
- Resource routes use Laravel's `Route::resource()` shorthand (7 verbs: index/create/store/show/edit/update/destroy).
- Custom non-resource verbs use kebab-case for URL segments and dot-or-camelCase for route names — match the surrounding pattern when adding routes.

---

## API endpoints (`routes/api.php`)

| Method | Path | Name | Auth | Returns |
|---|---|---|---|---|
| GET | `/api/logo` | `getLogo` | none | JSON `{logoUrl}` |
| GET | `/api/user` | — | `auth:sanctum` | Authenticated user |

That's it. The "API surface" of this app is essentially nil — everything else flows through `web` middleware (sessions + CSRF).

---

## Public routes (`routes/web.php`)

| Method | Path | Returns |
|---|---|---|
| GET | `/` | Redirects to `admin.dashboard.index` if authenticated; otherwise renders `Auth/Login` Inertia page |

---

## Auth routes (`routes/auth.php`)

`guest` middleware group:

| Method | Path | Controller@method | Name |
|---|---|---|---|
| GET | `/register` | `RegisteredUserController@create` | `register` |
| POST | `/register` | `RegisteredUserController@store` | — |
| GET | `/login` | `AuthenticatedSessionController@create` | `login` |
| POST | `/login` | `AuthenticatedSessionController@store` | — |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | `password.request` |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | `password.email` |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | `password.reset` |
| POST | `/reset-password` | `NewPasswordController@store` | `password.store` |

`auth` middleware group:

| Method | Path | Name | Notes |
|---|---|---|---|
| GET | `/verify-email` | `verification.notice` | |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | `signed`, `throttle:6,1` |
| POST | `/email/verification-notification` | `verification.send` | `throttle:6,1` |
| GET | `/confirm-password` | `password.confirm` | |
| POST | `/confirm-password` | — | |
| PUT | `/password` | `password.update` | |
| POST | `/logout` | `logout` | |

---

## Authenticated user/profile (Inertia)

| Method | Path | Controller@method | Name |
|---|---|---|---|
| GET | `/profile` | `ProfileController@edit` | `profile.edit` |
| PATCH | `/profile` | `ProfileController@update` | `profile.update` |
| DELETE | `/profile` | `ProfileController@destroy` | `profile.destroy` |

---

## Roles & Users (Inertia + Spatie Permission)

`roles` and `users` use `HandlePrecognitiveRequests` middleware so client-side validation runs before submit.

```
Route::resource('roles', RoleController::class);
Route::resource('users', UserController::class);
Route::post('/users/activation', [UserController::class, 'activation'])->name('users.activation');
Route::get('export-users', [UserController::class, 'exportExcel']);
```

Resource verbs produce 7 endpoints each (index/create/store/show/edit/update/destroy). Inertia pages: `Role/{List,Add,Edit}`, `User/{List,Add,Edit}`.

---

## Settings, Logs & cross-cutting (Inertia for Settings/Logs, Blade for prefixes)

| Method | Path | Controller@method | Name |
|---|---|---|---|
| GET | `/setting` | `SettingController@index` | `setting` |
| GET | `/logs` | `SettingController@getAllLogs` | `logs` |
| GET | `/logs/load-more` | `SettingController@loadMoreLogs` | `logs.loadMore` |
| GET | `/removedocument/{id}/{name}` | `SettingController@removedocument` | `removedocument` |
| GET | `/removeproduct/{id}/{model}` | `SettingController@removeProduct` | `removeproduct` |
| GET | `/removetransferproduct/{id}` | … | `removetransferproduct` |
| GET | `/removechallantransferproduct/{id}` | … | `removechallantransferproduct` |
| GET | `/removeinvoicedetails/{id}` | … | `removeinvoicedetails` |
| GET | `/salesstock` | `SettingController@salesstock` | `salesstock` |
| GET | `/servicestock` | `SettingController@servicestock` | `servicestock` |
| GET | `/manage-prefix` | `SettingController@managePrefix` | `manage-prefix` |
| POST | `/saveprefix` | `SettingController@savePrefixInfo` | `savePrefixInfo` |
| GET | `/export-stock` | `SettingController@exportExcel` | `export-stock` |
| GET | `/number-setting` | `SettingController@numberSetting` | `number-setting` |
| POST | `/settings/update-number/{id}` | `SettingController@updateNumber` | `setting.updateNumber` |

> **Smell**: GET routes that perform deletes (`removedocument`, `removeproduct`, `removetransferproduct`, etc.). These are not idempotent and bypass CSRF. The named methods reference legacy concepts (`transferproduct`, `challantransferproduct`, `invoicedetails`) that may not even exist in the active controllers — needs verification. See [gaps-and-risks.md](gaps-and-risks.md).

---

## Admin business routes (`/admin/...`)

All grouped under `Route::prefix('admin')->name('admin.')`.

### Dashboard
| Method | Path | Name | View type |
|---|---|---|---|
| GET | `/admin/dashboard` | `admin.dashboard.index` | Blade |
| GET | `/admin/dashboard/financial` | `admin.dashboard.financial` | Blade |
| GET | `/admin/dashboard/inventory` | `admin.dashboard.inventory` | Blade |

### Stock (legacy + active)
| Method | Path | Name | Notes |
|---|---|---|---|
| GET | `/admin/stock` | `admin.stock.index` | View-only Blade `admin.stock.index` |
| Resource | `/admin/stocks` | `admin.stocks.*` | `StockController` — only `index` and `store` are actually implemented |

### Products
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/products` | `admin.products.*` |
| PATCH | `/admin/products/{product}/selling-price` | `admin.products.update-selling-price` |
| PATCH | `/admin/products/{product}/toggle-status` | `admin.products.toggle-status` |

### Purchases + payments
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/purchases` | `admin.purchases.*` |
| GET | `/admin/purchases/{purchase}/payment-details` | `admin.purchases.paymentDetails` (JSON) |
| POST | `/admin/purchases/{purchase}/add-payment` | `admin.purchases.addPayment` (JSON) |
| PATCH | `/admin/purchases/{purchase}/payments/{payment}` | `admin.purchases.updatePayment` (JSON) |

### Sales + payments + invoices
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/sales` | `admin.sales.*` |
| GET | `/admin/sales/{sale}/payment-details` | `admin.sales.paymentDetails` (JSON) |
| POST | `/admin/sales/{sale}/add-payment` | `admin.sales.addPayment` (JSON) |
| PATCH | `/admin/sales/{sale}/payments/{salePayment}` | `admin.sales.updatePayment` (JSON) |
| GET | `/admin/sale-invoices/{saleInvoice}/export` | `admin.sale-invoices.export` (XLSX stream) |
| Resource (partial) | `/admin/sale-invoices` | only `index, create, store, show, destroy` |

### Expenses & expense categories
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/expense-categories` | `admin.expense-categories.*` |
| GET | `/admin/expense-categories-list` | `admin.expense-categories.list` (JSON) |
| Resource | `/admin/expenses` | `admin.expenses.*` |

### Returns
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/purchase-returns` | `admin.purchase-returns.*` |
| Resource | `/admin/sale-returns` | `admin.sale-returns.*` |

### Purchase payments (top-level)
| Method | Path | Name |
|---|---|---|
| Resource | `/admin/payments` | `admin.payments.*` |
| POST | `/admin/payments/{payment}/mark-as-paid` | `admin.payments.mark-as-paid` |
| GET | `/admin/payments/purchase/{purchaseId}` | `admin.payments.by-purchase` (JSON) |

### Reports
| Method | Path | Name |
|---|---|---|
| GET | `/admin/reports` | `admin.reports.index` |
| GET | `/admin/reports/sales` | `admin.reports.sales` |
| POST | `/admin/reports/sales/export` | `admin.reports.sales.export` (XLSX stream) |
| GET | `/admin/reports/purchases` | `admin.reports.purchases` |
| GET | `/admin/reports/purchases/export` | `admin.reports.purchases.export` (XLSX stream) |
| GET | `/admin/reports/stock` | `admin.reports.stock` |

### Stock closings
| Method | Path | Name |
|---|---|---|
| GET | `/admin/stock-closings` | `admin.stock-closings.index` |
| GET | `/admin/stock-closings/history` | `admin.stock-closings.history` |
| POST | `/admin/stock-closings` | `admin.stock-closings.store` |
| GET | `/admin/stock-closings/{stockClosing}` | `admin.stock-closings.show` |
| DELETE | `/admin/stock-closings/{stockClosing}` | `admin.stock-closings.destroy` |

### Settings (admin)
| Method | Path | Name |
|---|---|---|
| GET | `/admin/settings` | `admin.settings.index` |
| PUT | `/admin/settings` | `admin.settings.update` |
| PUT | `/admin/settings/password` | `admin.settings.update-password` |
| GET | `/admin/db-backup/download` | `admin.db-backup.download` |

---

## Authorization model

- All app routes inside the top `Route::middleware('auth')` group require an authenticated session.
- **Spatie Permission middleware is COMMENTED OUT in every controller constructor** — see [gaps-and-risks.md](gaps-and-risks.md). This means any authenticated user can hit any route. The `auth()->user()->can('...')` checks in some Inertia controllers (e.g., `RoleController::index`, `UserController::index`) are passed to the front-end as flags but do not block server-side execution.
- Permission slugs referenced in code (commented or active): `List Users`, `Create User`, `Edit User`, `Delete User`, `User Activation`, `List Roles`, `Create Roles`, `Edit Roles`, `Delete Roles`, `Roles & Permissions`, `List Setting`, `Activity Log`, `List Expense`, `Create Expense`, `Edit Expense`, `Delete Expense`, `List Expense Category`, `Create Expense Category`, `Edit Expense Category`, `Delete Expense Category`, `List Payment`, `Create Payment`, `Edit Payment`, `Delete Payment`, `List Sale Return`, `Create Sale Return`, `Edit Sale Return`, `Delete Sale Return`, `List Purchase Return`, `Create Purchase Return`, `Edit Purchase Return`, `Delete Purchase Return`. (Other modules have no `permission:` references at all.)

## JSON-returning endpoints (catalog)

For SPA / AJAX consumers, here are the endpoints that return JSON (others return Inertia or Blade):

- `GET /api/logo`
- `GET /api/user`
- `GET /admin/expense-categories-list`
- `POST /admin/expense-categories` (when `Accept: application/json`)
- `GET /admin/sales/{sale}/payment-details`
- `POST /admin/sales/{sale}/add-payment`
- `PATCH /admin/sales/{sale}/payments/{salePayment}`
- `GET /admin/purchases/{purchase}/payment-details`
- `POST /admin/purchases/{purchase}/add-payment`
- `PATCH /admin/purchases/{purchase}/payments/{payment}`
- `GET /admin/payments/purchase/{purchaseId}`
- `PATCH /admin/products/{product}/selling-price`
- `PATCH /admin/products/{product}/toggle-status`

## Streaming (XLSX/PDF) endpoints

- `GET /admin/sale-invoices/{saleInvoice}/export` — streams XLSX via PhpSpreadsheet
- `POST /admin/reports/sales/export` — streams XLSX
- `GET /admin/reports/purchases/export` — streams XLSX
- `GET /export-users` — Maatwebsite Excel download
- `GET /export-stock` — XLSX (via SettingController)
- `GET /admin/db-backup/download` — streams latest .zip from a hardcoded Windows path
