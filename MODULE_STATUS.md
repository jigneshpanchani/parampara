# Module Review & Bug Fix Status

Last updated: 2026-02-28

---

## ✅ Reviewed & Fixed

### Product
- Fixed: Old photo not deleted on update or destroy (storage leak)
- Fixed: No `#` serial number column on index
- Added: `show.blade.php` (was missing — would crash the show route)
- Added: View button on index

---

### Purchase
- Fixed: Editing `bill_type` from GST → Without GST was silently failing (pending migration + inconsistent `old()` fallback in edit form)
- Fixed: `destroy()` was not deleting related payments and returns (orphan records)
- Fixed: N+1 query in index (`$purchase->payments()` → `$purchase->payments` collection)
- Added: `DB::transaction()` in `store()`, `update()`, `destroy()`
- Added: `show.blade.php` (was missing)
- Added: `#` serial number column and View button on index
- Added: GST / Without GST split stats on index

---

### Sell
- Fixed: `destroy()` was not deleting related sell returns before deleting (orphan records)
- Added: `DB::transaction()` in `store()`, `update()`, `destroy()`
- Added: `#` serial number column on index
- Added: Seller Name + contact column on index
- Added: View button on index
- Improved: `show.blade.php` — seller info, payment breakdown (cash/online split), items total, notes

---

### Sell Return
- Added: `DB::transaction()` in `store()`, `update()`, `destroy()`
- Fixed: `mx-autoo` typo in create and edit views
- Fixed: Missing `@section('title')` in create and edit views
- Fixed: Missing `old()` fallbacks on all fields in create and edit views
- Fixed: Missing empty `<option>` in sell_id and product_id selects in edit view
- Fixed: `Session::get('success')` → `session('success')` in index
- Added: Error flash block, `#` column, Sale column, View button on index
- Added: `show.blade.php` (was missing — would crash the show route)

---

### Purchase Return
- Added: `DB::transaction()` in `store()`, `update()`, `destroy()`
- Fixed: `mx-autoo` typo in create and edit views
- Fixed: Missing `@section('title')` in create and edit views
- Fixed: Missing `old()` fallbacks in create and edit views
- Fixed: Missing empty `<option>` in purchase_id and product_id selects in edit view
- Fixed: `Session::get('success')` → `session('success')` in index
- Added: Error flash block, `#` column, Purchase (supplier) column, View button on index
- Added: `show.blade.php` (was missing — would crash the show route)
- Added: `return_date` default to today on create form

---

### Payment
- Fixed: Purchase ID column now shows supplier name with link instead of raw ID
- Added: `@section('title')` to index, create, edit views
- Added: `#` serial number column on index

---

### Expense
- Fixed: `Session::get('success')` → `session('success')` in index
- Added: Error flash block in index
- Added: `@section('title')` to index view
- Added: `#` serial number column, Description column, View button on index
- Added: `show.blade.php` (was missing — would crash the show route)

---

### Expense Category
- Fixed: `Session::get('success')` / `Session::get('error')` → `session()` in index
- Added: `@section('title')` to index view
- Added: `#` serial number column on index
- Added: `show.blade.php` (was missing — would crash the show route)
- Fixed: `show()` now eager loads expenses before rendering view

---

## ⚠️ Not Yet Reviewed

| Module    | Notes                                                      |
|-----------|------------------------------------------------------------|
| Stock     | Looks clean — has `@section('title')`, modal for add stock |
| Reports   | Complex — date/filter logic, Excel export, not reviewed    |
| Dashboard | Not reviewed                                               |
| Settings  | Partially touched (DB backup download fix)                 |

---

## Infrastructure / Cross-Cutting

| Item                     | Status |
|--------------------------|--------|
| DB auto-backup on logout | ✅ Done — saves ZIP to `D:\etc\Batsal\Parampara\DB Backup\`, runs in background CMD, 30-day retention |
| DB backup download       | ✅ Done — downloads latest ZIP from backup folder |
