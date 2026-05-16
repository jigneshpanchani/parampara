# Component & View Inventory

Generated 2026-05-09 via `bmad-document-project` (deep scan). Catalogs both **Vue 3 components/pages** (Inertia side) and **Blade views** (admin business modules).

> **Architecture reminder**: parampara is a hybrid Inertia/Blade app. Inertia/Vue is used for cross-cutting screens (auth, users, roles, settings, activity log, top-level dashboard). Blade is used for the daily-use business modules (sales, purchases, products, expenses, etc.). Both compile through the same Vite pipeline.

---

## Vue components (`resources/js/Components/`) — 47 components

### Layout & navigation
- `ApplicationLogo.vue` — branded logo block
- `SideMenu.vue` — left sidebar shell (Inertia layouts)
- `NavLink.vue`, `ResponsiveNavLink.vue`, `DropdownLink.vue`, `ActionLink.vue` — link variants
- `Dropdown.vue`, `SimpleDropdown.vue` — menu primitives
- `NotificationDropdown.vue` — bell icon w/ list

### Form inputs
- `TextInput.vue`, `TextArea.vue` — text fields
- `Checkbox.vue`, `CheckboxWithLabel.vue`, `MultipleCheckbox.vue`
- `RadioButton.vue`
- `SwitchButton.vue` — toggle
- `DateInput.vue` — date picker
- `InputLabel.vue`, `InputError.vue` — labels & inline errors
- `ComboboxInput.vue`
- `SearchableDropdown.vue`, `SearchableDropdownNew.vue`, `SearchableMultiselect.vue` — search/select widgets (3 variants — likely consolidation candidate)
- `FileUpload.vue`, `MultipleFileUpload.vue`, `FileViewer.vue`

### Buttons
- `PrimaryButton.vue`, `SecondaryButton.vue`, `DangerButton.vue`
- `CustomButton.vue`, `CreateButton.vue`
- `ArrowIcon.vue`

### Modals
- `Modal.vue`, `NewModal.vue` — modal shells (two variants — consolidation candidate)
- `DeleteModal.vue` — delete confirmation

### Charts (Chart.js + vue-chartjs)
- `BarChart.vue`, `LineChart.vue`, `MultiLineChart.vue`, `PieChart.vue`

### Pagination
- `Pagination.vue`, `PaginationNew.vue` — two variants (consolidation candidate)

### Notifications / toasts
- `Notification.vue`
- `ToastNotification.vue`, `ToastNotificationSuccess.vue`, `ToastNotificationError.vue`, `ToastNotificationWarning.vue` — four toast variants

### Misc
- `LoadingSpinner.vue`, `NoRecordsFound.vue`

### Composables
- [resources/js/Composables/sortAndSearch.js](resources/js/Composables/sortAndSearch.js) — list sorting/search helper

### Utilities
- [resources/js/Utils/ResponsiveUtils.js](resources/js/Utils/ResponsiveUtils.js)
- [resources/js/helpers/can.js](resources/js/helpers/can.js) — Vue plugin for permission checks; registered via `app.use(can)` in `app.js`

---

## Vue Pages (`resources/js/Pages/`) — 17 pages

| Page | File | Purpose |
|---|---|---|
| Dashboard (legacy?) | `Pages/Dashboard.vue` | Old `DashboardController::dashboard` returns this — but no route registers that controller method. Likely dead. |
| Welcome | `Pages/Welcome.vue` | Default Laravel welcome — likely dead |
| Auth/Login | `Pages/Auth/Login.vue` | Login screen (rendered from `/`) |
| Auth/Register | `Pages/Auth/Register.vue` | |
| Auth/Registerv2 | `Pages/Auth/Registerv2.vue` | Alternate register screen — verify which is wired |
| Auth/ForgotPassword | `Pages/Auth/ForgotPassword.vue` | |
| Auth/ResetPassword | `Pages/Auth/ResetPassword.vue` | |
| Auth/ConfirmPassword | `Pages/Auth/ConfirmPassword.vue` | |
| Auth/VerifyEmail | `Pages/Auth/VerifyEmail.vue` | |
| Role/List | `Pages/Role/List.vue` | Roles list |
| Role/Add | `Pages/Role/Add.vue` | |
| Role/Edit | `Pages/Role/Edit.vue` | |
| User/List | `Pages/User/List.vue` | Users list |
| User/Add | `Pages/User/Add.vue` | |
| User/Edit | `Pages/User/Edit.vue` | |
| Settings/Index | `Pages/Settings/Index.vue` | Settings landing — minimal Inertia entry |
| Activity/ActivityLog | `Pages/Activity/ActivityLog.vue` | Activity log viewer with filters |

---

## Vue Layouts (`resources/js/Layouts/`)

- `AuthenticatedLayout.vue` — Inertia layout for logged-in users
- `AdminLayout.vue` — Vue admin layout (used by Inertia pages only — admin Blade modules use the Blade layout)
- `GuestLayout.vue` — Inertia layout for auth pages (login/register)

---

## Blade layouts (`resources/views/layouts/`)

- `admin.blade.php` — **the** admin layout used by every business module. Includes inline sidebar with route-driven active states, logo + favicon from CompanyProfile singleton, `@vite([css, js/app.js])` to compile Tailwind. ~600+ lines of template + Tailwind classes.
- `app.blade.php` (root) — minimal root template

---

## Blade views (`resources/views/admin/`) — 47 view files across 14 folders

### Dashboard
```
admin/dashboard/
├── index.blade.php       # main KPI dashboard
├── financial.blade.php   # financial overview
└── inventory.blade.php   # inventory overview
```

### Products
```
admin/products/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php
└── _form.blade.php       # shared form partial
```

### Purchases & purchase returns
```
admin/purchases/{index,create,edit,show}.blade.php
admin/purchase-returns/{index,create,edit,show}.blade.php
```

### Sales / sale invoices / sale returns
```
admin/sales/{index,create,edit,show}.blade.php
admin/sale-invoices/{index,create,show}.blade.php   # no edit (immutable invoices)
admin/sale-returns/{index,create,edit,show}.blade.php
```

### Expenses & categories
```
admin/expenses/{index,create,edit,show}.blade.php
admin/expense-categories/{index,create,edit,show}.blade.php
```

### Payments (purchase-side)
```
admin/payments/{index,create,edit,show}.blade.php
```

### Stock & stock closings
```
admin/stock/index.blade.php             # static view (Route::view)
admin/stocks/index.blade.php            # StockController@index
admin/stock-closings/{index,history,show}.blade.php
admin/stock-closings/_save_modal.blade.php   # shared modal partial
```

### Reports
```
admin/reports/{index,sales,purchases,stock}.blade.php
```

### Settings
```
admin/settings/index.blade.php
```

### Errors
```
errors/403.blade.php
```

### Shared Blade components (`resources/views/components/`)
- `currency.blade.php` — `<x-currency>` component for `₹ 1,234.56` formatting
- `delete-confirmation-modal.blade.php` — reusable delete modal

### Vendor pagination overrides (`resources/views/vendor/pagination/`)
Standard Laravel pagination templates published; `tailwind.blade.php` and `simple-tailwind.blade.php` are the relevant ones for this project.

---

## Component categorization summary

| Category | Vue components | Blade views/partials |
|---|---|---|
| Layout/nav | 8 | 1 (admin.blade.php) + sidebar inline |
| Form inputs | 16 | inline in `_form.blade.php` per module |
| Buttons | 6 | inline (Tailwind utility classes) |
| Modals | 3 | `_save_modal`, `delete-confirmation-modal` |
| Charts | 4 | rendered via Chart.js inside Blade `<canvas>` blocks (dashboard) |
| Pagination | 2 | tailwind paginator views |
| Notifications | 5 | flash-message divs in `admin.blade.php` |
| Other | 5 | `currency` component |

## Reusability notes

- **Multiple variants** of similar components (`Modal` + `NewModal`, `Pagination` + `PaginationNew`, `SearchableDropdown` + `SearchableDropdownNew` + `SearchableMultiselect`) suggest in-flight migrations or accumulated drift. Consolidation is a viable improvement-initiative target.
- **No design system** is formally documented — Tailwind utility classes are applied ad-hoc per template.
- **No shared list-table component** — every Blade module reimplements its own table, filters, and pagination. This is a high-leverage improvement target (matches the user's "Better list views / column sorting / search and filter improvements" goal).
- **Mixing Inertia and Blade for similar UI tasks** means a list/table component would need *two* implementations (Vue + Blade) unless one side migrates.
