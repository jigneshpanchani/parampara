<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\Organization;
use App\Models\PurchaseOrderReceives;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{

    public function edit(Request $request): Response
    {
        $mustVerifyEmail = $request->user() instanceof MustVerifyEmail;
        $status   = session('status');
        return Inertia::render('Profile/Edit', compact('mustVerifyEmail', 'status'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();
        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }

    public function dashboard(Request $request)
    {
        $permissions = auth()->user()->getAllPermissions()->pluck('name');
        $permissions = json_encode($permissions);
        $user = auth()->user();
        $recentInvoices = Invoice::with('customers')->orderBy('id', 'desc',)->limit(10)->get();
        $recentQuotations = Quotation::with('customers')->orderBy('id', 'desc')->limit(10)->get();
        $recentPurchaseOrder = PurchaseOrderReceives::with('purchaseOrder.company')->orderBy('id', 'desc')->limit(10)->get();
        $productCount = Product::count();
        $invoiceCount = Invoice::count();
        $companyCount = Company::count();
        $quotationCount = Quotation::count();
        $organization  = Organization::select('id', 'name')->get();
        $allOrganization = ['id' => null, 'name' => 'ALL ORGANIZATION'];
        $organization->prepend($allOrganization);

        $salesDataByMonth = array_fill(0, 12, 0);
        $salesByMonth = Invoice::selectRaw('MONTH(date) as month, SUM(total_amount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');
        foreach ($salesByMonth as $month => $total) {
            $salesDataByMonth[$month - 4] = $total;
        }

        $purchaseDataByMonth = array_fill(0, 12, 0);
        $purchaseByMonth = PurchaseOrderReceives::selectRaw('MONTH(po_receive_date) as month, SUM(total_amount) as total')
            ->whereYear('po_receive_date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');
        foreach ($purchaseByMonth as $month => $total) {
            $purchaseDataByMonth[$month - 4] = $total;
        }

        $salesData = Invoice::with('users')->where('sales_user_id', '!=', NULL)->select('sales_user_id', DB::raw('SUM(sub_total) as total_sales'))
        ->groupBy('sales_user_id')
        ->get()->toArray();

        $pieChartData = [
            'labels' => [],
            'datasets' => []
        ];
        $pieChartData['datasets'][0]['data'] = [];

        foreach ($salesData as $sale) {
            $pieChartData['labels'][] = $sale['users']['first_name'].' '.$sale['users']['last_name'];
            $pieChartData['datasets'][0]['data'][] = $sale['total_sales'];
        }

        $productsWithLowStock = Product::join(DB::raw('(SELECT product_id, SUM(receive_qty) - SUM(sell_qty) AS stock
            FROM serial_numbers GROUP BY product_id) AS sn'), 'products.id', '=', 'sn.product_id')
            ->leftJoin('companies', 'companies.id', 'products.company_id')
            ->whereColumn('sn.stock', '<', 'products.min_qty')
            ->select('products.*', 'companies.name as company_name', 'sn.stock');
        $productsWithLowStock = $productsWithLowStock->orderBy('products.id', 'desc')->paginate(10);
        return Inertia::render('Dashboard', compact('permissions', 'productsWithLowStock', 'pieChartData', 'salesDataByMonth', 'purchaseDataByMonth', 'user', 'recentInvoices', 'recentQuotations', 'recentPurchaseOrder', 'productCount', 'invoiceCount', 'companyCount', 'quotationCount', 'organization'));
    }
}
