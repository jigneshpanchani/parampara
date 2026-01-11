<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:List Roles')->only(['index']);
        $this->middleware('permission:Create Roles')->only(['create', 'store']);
        $this->middleware('permission:Edit Roles')->only(['edit', 'update']);
        $this->middleware('permission:Delete Roles')->only('destroy');
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        $permissions = [
            'canCreateRoles'      => auth()->user()->can('Create Roles'),
            'canEditRoles'        => auth()->user()->can('Edit Roles'),
            'canDeleteRoles'      => auth()->user()->can('Delete Roles')
        ];
        return Inertia::render('Role/List', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        $data = $permissions->toArray();
        return Inertia::render('Role/Add', compact('data'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            $role->givePermissionTo($request->permissions);
            DB::commit();
            return Redirect::to('/roles')->with('success','Role Add Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/roles')->with('error', $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $role = Role::find($id);
        $roleHasPermissions = array_column(json_decode($role->permissions, true), 'id');

        /*$permissions = Permission::all()->groupBy('module');
        $data = $permissions->toArray();*/

        $permissions = Permission::all();
        $data = [];
        foreach ($permissions as $permission) {
            $data[$permission['module']][] = $permission;
        }

        return Inertia::render('Role/Edit', compact('role','data', 'roleHasPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        DB::beginTransaction();
        try {
            $role = Role::find($request->id);
            $role->update(['name' => $request->name]);
            $permissions = $request->permissions ?? [];
            $role->syncPermissions($permissions);
            DB::commit();
            return Redirect::to('/roles')->with('success', 'Role & Permissions Update Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/roles')->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $usersWithRole = User::whereHas('roles', function ($query) use ($id) {
                $query->where('id', $id);
            })->count();
            if($usersWithRole > 0){
                return Redirect::to('/roles')->with('error', 'Can not delete this role. It is assigned to users');
            }
            $role = Role::find($id);
            $role->delete();
            DB::commit();
            return Redirect::to('/roles')->with('success','Role Delete Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/roles')->with('error', $e->getMessage());
        }
    }

}
