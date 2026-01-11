<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ForcePermissionSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:force-sync {role=Admin}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Forcefully seed all permissions from CSV and assign to a role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->argument('role');
        
        $this->info("🔄 Starting permission sync...");
        
        // Step 1: Seed permissions from CSV
        $this->info("📋 Step 1: Seeding permissions from CSV...");
        $this->seedPermissionsFromCSV();
        
        // Step 2: Get the role
        $this->info("🔍 Step 2: Finding role: {$roleName}...");
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("❌ Role '{$roleName}' not found!");
            return 1;
        }
        
        $this->info("✅ Found role: {$roleName}");
        
        // Step 3: Get all permissions
        $this->info("📊 Step 3: Fetching all permissions...");
        $permissions = Permission::all();
        
        if ($permissions->isEmpty()) {
            $this->error("❌ No permissions found in database!");
            return 1;
        }
        
        $this->info("✅ Found " . $permissions->count() . " permissions");
        
        // Step 4: Assign all permissions to role
        $this->info("🔐 Step 4: Assigning all permissions to role '{$roleName}'...");
        
        // Clear existing permissions first
        $role->syncPermissions($permissions);
        
        $this->info("✅ Successfully assigned " . $permissions->count() . " permissions to role '{$roleName}'");
        
        // Step 5: Clear cache
        $this->info("🧹 Step 5: Clearing cache...");
        \Illuminate\Support\Facades\Cache::clear();
        $this->info("✅ Cache cleared");
        
        // Step 6: Display summary
        $this->info("\n" . str_repeat("=", 60));
        $this->info("✅ PERMISSION SYNC COMPLETE!");
        $this->info(str_repeat("=", 60));
        $this->info("Role: {$roleName}");
        $this->info("Permissions Assigned: " . $permissions->count());
        $this->info("\n📝 Permissions assigned:");
        
        foreach ($permissions as $permission) {
            $this->line("   ✓ {$permission->name}");
        }
        
        $this->info("\n💡 Next steps:");
        $this->info("   1. Logout from admin panel");
        $this->info("   2. Clear browser cache (Ctrl+Shift+Delete)");
        $this->info("   3. Login again");
        $this->info("   4. All permissions should now be available!");
        
        return 0;
    }
    
    /**
     * Seed permissions from CSV file
     */
    private function seedPermissionsFromCSV()
    {
        $csvFile = base_path('database/seeders/csv/permissions.csv');
        
        if (!File::exists($csvFile)) {
            $this->error("❌ CSV file not found: {$csvFile}");
            return;
        }
        
        $file = fopen($csvFile, 'r');
        $firstLine = true;
        $addedCount = 0;
        $updatedCount = 0;
        
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (!$firstLine) {
                $whereArr = [
                    'name'          => $data[1],
                    'guard_name'    => $data[2],
                    'module'        => $data[4]
                ];
                
                $exists = DB::table('permissions')->where($whereArr)->exists();
                
                if (!$exists) {
                    DB::table('permissions')->insert([
                        'name'          => $data[1],
                        'guard_name'    => $data[2],
                        'slug'          => $data[3],
                        'module'        => $data[4],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                    $addedCount++;
                } else {
                    DB::table('permissions')->where($whereArr)->update([
                        'slug'          => $data[3],
                        'updated_at'    => now(),
                    ]);
                    $updatedCount++;
                }
            }
            $firstLine = false;
        }
        
        fclose($file);
        
        $this->info("✅ Added: {$addedCount} new permissions");
        $this->info("✅ Updated: {$updatedCount} existing permissions");
    }
}

