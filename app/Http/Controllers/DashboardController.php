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
use App\Models\Customer;
use App\Models\Jobcard;
use App\Models\Orders;
use App\Models\Quotation;
use App\Models\Organization;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderReceives;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    public function dashboard(Request $request)
    {
        $organizationId = $request->input('organization_id');
        $permissions = auth()->user()->getAllPermissions()->pluck('name');
        $permissions = json_encode($permissions);

        return Inertia::render('Dashboard', compact(
            'permissions'
        ));
    }
}
