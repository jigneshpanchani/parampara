<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\QueryTrait;


class UserController extends Controller
{

    public function __construct()
    {
        //$this->middleware('permission:List Users')->only(['index']);
        //$this->middleware('permission:Create User')->only(['create', 'store']);
        //$this->middleware('permission:Edit User')->only(['edit', 'update']);
        //$this->middleware('permission:Delete User')->only('destroy');
        //$this->middleware('permission:User Activation')->only('activation');
    }

    use QueryTrait;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::with('roles');
        if ($search !== null && $search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        $searchableFields = [ 'first_name', 'last_name', 'email', 'role_id','status'];
        $this->searchAndSort($query, $request, $searchableFields);

        $data = $query->orderBy('id', 'desc')->paginate(20);

        $permissions = [
            'canCreateUser'      => auth()->user()->can('Create User'),
            'canEditUser'        => auth()->user()->can('Edit User'),
            'canDeleteUser'      => auth()->user()->can('Delete User'),
        ];

        return Inertia::render('User/List', compact('data', 'permissions'));
    }

    public function create()
    {
        $roles = Role::all();
        return Inertia::render('User/Add', compact('roles'));
    }


    public function store(UserStoreRequest $request)
    {
        // dd( $request->DTO());
        DB::beginTransaction();
        try {
            $userData = $request->DTO();
            $userDataArray = (array) $userData;
            $userDataArray['password'] = Hash::make($request->password);
            $user = User::create($userDataArray);
            $role = Role::findById($user->role_id);
            $user->assignRole($role->name);
            DB::commit();
            return Redirect::to('/users')->with('success', 'User Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/users')->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = User::find($id);
        $roles= Role::all();
        return Inertia::render('User/Edit', compact('data', 'roles'));
    }

    public function update(UserUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->id);
            $user->update($request->all());
            $role = Role::findById($request->role_id);
            $user->syncRoles([$role->name]);
            DB::commit();
            return Redirect::to('/users')->with('success','User Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/users')->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user->invoicecreate()->count() > 0 || $user->challancreate()->count() > 0 || $user->quotationcreate()->count() > 0 ||
            $user->invoiceby()->count() > 0 || $user->challanby()->count() > 0 || $user->quotationby()->count() > 0) {
                return Redirect::to('/users')->with('error', 'User cannot be deleted');
            }
            $user->delete();
            DB::commit();
            return Redirect::to('/users')->with('success','User Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/users')->with('error', $e->getMessage());
        }
    }

    public function activation(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return Redirect::to('/users')->with('error', 'User not found');
        }

        DB::beginTransaction();
        try {
            $user->status = $request->status;
            $user->save();
            DB::commit();
            return Redirect::to('/users')->with('success','Status Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/users')->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request){
        return Excel::download(new UsersExport($request), 'users.xlsx');
    }

}
