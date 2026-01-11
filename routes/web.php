<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SellController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\SellReturnController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // If user is authenticated, redirect to admin dashboard
    if (auth()->check()) {
        return redirect()->route('admin.dashboard.index');
    }

    // Otherwise, show login page
    return Inertia::render('Auth/Login', [
        'canLogin'          => Route::has('login'),
        'canRegister'       => Route::has('register'),
        'laravelVersion'    => Application::VERSION,
        'phpVersion'        => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {

    Route::get('/profile',      [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',    [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',   [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Roles
    Route::resource('roles', RoleController::class)->middleware([HandlePrecognitiveRequests::class]);

    //Users
    Route::resource('users', UserController::class)->middleware([HandlePrecognitiveRequests::class]);
    Route::post('/users/activation',    [UserController::class, 'activation'])->name('users.activation');
    Route::get('export-users', [UserController::class, 'exportExcel']);

    //Reports
    Route::get('/setting',          [SettingController::class, 'index'])->name('setting');
    Route::get('logs',              [SettingController::class, 'getAllLogs'])->name('logs');
    Route::get('/logs/load-more',   [SettingController::class, 'loadMoreLogs'])->name('logs.loadMore');

    //Common
    Route::get('/removedocument/{id}/{name}',        [SettingController::class, 'removedocument'])->name('removedocument');
    Route::get('/removeproduct/{id}/{model}',        [SettingController::class, 'removeProduct'])->name('removeproduct');
    Route::get('/removetransferproduct/{id}',        [SettingController::class, 'removeTransferProduct'])->name('removetransferproduct');
    Route::get('/removechallantransferproduct/{id}', [SettingController::class, 'removeChallanTransferProduct'])->name('removechallantransferproduct');
    Route::get('/removeinvoicedetails/{id}',         [SettingController::class, 'removeInvoiceDetails'])->name('removeinvoicedetails');

    //Setting
    Route::get('/salesstock',       [SettingController::class, 'salesstock'])->name('salesstock');
    Route::get('/servicestock',     [SettingController::class, 'servicestock'])->name('servicestock');
    Route::get('/manage-prefix',    [SettingController::class, 'managePrefix'])->name('manage-prefix');
    Route::post('/saveprefix',      [SettingController::class, 'savePrefixInfo'])->name('savePrefixInfo');
    Route::get('/export-stock',     [SettingController::class, 'exportExcel'])->name('export-stock');
    Route::get('/number-setting',   [SettingController::class, 'numberSetting'])->name('number-setting');
    Route::post('/settings/update-number/{id}', [SettingController::class, 'updateNumber'])->name('setting.updateNumber');

    // Admin Routes - Stock Management, Products, Purchases, Sells, Reports
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard Routes
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('dashboard/financial', [AdminDashboardController::class, 'financial'])->name('dashboard.financial');
        Route::get('dashboard/inventory', [AdminDashboardController::class, 'inventory'])->name('dashboard.inventory');

        Route::resource('stocks', StockController::class);
        Route::resource('products', ProductController::class);
        Route::resource('purchases', PurchaseController::class);
        Route::resource('sells', SellController::class);
        Route::resource('expense-categories', \App\Http\Controllers\Admin\ExpenseCategoryController::class);
        Route::get('expense-categories-list', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'getCategories'])->name('expense-categories.list');
        Route::resource('expenses', ExpenseController::class);
        Route::resource('purchase-returns', PurchaseReturnController::class);
        Route::resource('sell-returns', SellReturnController::class);
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);
        Route::post('payments/{payment}/mark-as-paid', [\App\Http\Controllers\Admin\PaymentController::class, 'markAsPaid'])->name('payments.mark-as-paid');
        Route::get('payments/purchase/{purchaseId}', [\App\Http\Controllers\Admin\PaymentController::class, 'getPaymentsByPurchase'])->name('payments.by-purchase');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });

});


require __DIR__ . '/auth.php';



