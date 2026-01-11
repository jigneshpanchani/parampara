<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (DB::table('roles')->count() == 0) {
            DB::table('roles')->insert([
                [
                    'name'  => 'Admin',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }


        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert([
                [
                    'first_name'  => 'Admin',
                    'last_name' => 'User',
                    'email' => 'admin@parampara.com',
                    'password' => bcrypt('Parampara@3536'),
                    'role_id' => 1,
                    'email_verified_at' => now(),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'first_name'  => 'Test',
                    'last_name' => 'User',
                    'email' => 'test@parampara.com',
                    'password' => bcrypt('test123456'),
                    'role_id' => 1,
                    'email_verified_at' => now(),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        if (DB::table('roles')->count() > 0 && DB::table('permissions')->count() > 0) {

            foreach (User::all() as $user){
                $role = Role::findById($user->role_id);
                $user->assignRole($role->name);
            }

            $permissions = Permission::all();
             foreach (Role::all() as $role){
                 if($role->name == 'Admin'){
                     $role->givePermissionTo($permissions);
                 }else{
                     //$role->givePermissionTo('Dashboard');
                 }
             }
        }
    }
}
