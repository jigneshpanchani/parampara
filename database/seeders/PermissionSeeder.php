<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{        
    public function run()
    {
        // Path to your CSV file
        $csvFile = base_path('database/seeders/csv/permissions.csv');

        // Open the file
        $file = fopen($csvFile, 'r');
        // dd($file);

        // Read the file line by line & collect array for insert
        $firstLine = true;
        $insertData = [];
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {

            if (!$firstLine) {
                $whereArr = [
                    'name'          => $data[1],
                    'guard_name'    => $data[2],
                    // 'slug'          => $data[3],
                    'module'        => $data[4]
                ];
                $exists = DB::table('permissions')->where($whereArr)->exists();
                if(!$exists){
                    //$insertData[] = $whereArr;
                    $insertData[] = [
                        'name'          => $data[1],
                        'guard_name'    => $data[2],
                        'slug'          => $data[3],
                        'module'        => $data[4]
                    ];
                }else{
                    DB::table('permissions')->where($whereArr)->update(['slug' => $data[3]]);
                }
            }
            $firstLine = false;
        }

        // Insert permissions
        if ($insertData) {
            DB::table('permissions')->insert($insertData);
            echo 'Successfully added '. count($insertData) .' Permissions.';
        }else{
            echo "Already Upto date all permissions.";
        }

        // Close the file
        fclose($file);
    }
}
