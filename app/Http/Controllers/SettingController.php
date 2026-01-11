<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:List Setting')->only(['index']);
        $this->middleware('permission:Activity Log')->only(['getAllLogs', 'loadMoreLogs']);
    }

    public function index(Request $request)
    {
        $permissions = [
            'canPermissionsAdd'   => auth()->user()->can('Roles & Permissions')
        ];
        return Inertia::render('Settings/Index', compact('permissions'));
    }

    public function getLogo()
    {
        $settings = CompanyProfile::first();
        $logoUrl = $settings && $settings->logo ? $settings->logo : null;

        return response()->json([
            'logoUrl' => $logoUrl
        ]);
    }

    protected function buildLogQuery($request)
    {
        $search   = $request->input('search', '');
        $userId   = $request->input('causer_id', null);
        $fromDate = $request->input('from_date', null);
        $toDate   = $request->input('to_date', null);
        $logName  = $request->input('log_name', null);

        $query = Activity::with(['causer', 'subject'])
            ->select('activity_log.*')
            ->latest()
            ->whereDate('activity_log.created_at', '>', '2025-02-16'); // Added filter for logs after 14-02-2025

            if (!empty($search)) {
                // Remove HTML tags before searching (using MySQL's REGEXP_REPLACE)
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("REGEXP_REPLACE(description, '<[^>]+>', '') LIKE ?", ['%' . $search . '%']);
                });
            }

        if (!empty($userId)) {
            $query->where('causer_id', $userId);
        }

        if ($fromDate != '' ) {
            $query->whereDate('activity_log.created_at', '>=', $fromDate);
        }
        if ($toDate != '') {
            $query->whereDate('activity_log.created_at', '<=', $toDate);
        }
        if ($logName != '') {
            $query->where('log_name', $logName);
        }

        return $query;
    }

    public function getAllLogs(Request $request)
    {
        $query = $this->buildLogQuery($request);

        // Retrieve extra data for filters
        $user = User::where(['status' => '1'])
            ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS name"), 'id')
            ->orderByRaw('first_name')
            ->get();
        $alluser = ['id' => null, 'name' => 'ALL USERS'];
        $user->prepend($alluser);

        $logNames = Activity::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->orderBy('log_name', 'asc')
            ->pluck('log_name');

        $limit = 15;
        $activityLogs = $query->limit($limit)->get();

        return Inertia::render('Activity/ActivityLog', compact('activityLogs', 'user', 'logNames'));
    }

    public function loadMoreLogs(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit  = 15;

        $query = $this->buildLogQuery($request);

        $activityLogs = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'activityLogs' => $activityLogs,
            'hasMore'      => ($activityLogs->count() === $limit)
        ]);
    }
}
