<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Porter Charge',
            'AMC Bill',
            'Tea/Cold drinks/Ice cream',
            'Auto Rixa Charge',
            'Sound Box Charge',
            'Mobile Bill',
            'News Paper Bill',
            'Water Bill',
            'Stationary Bill',
            'Rent Agreement',
            'Fuel Charge (Petrol)',
            'Light Bill',
            'Shop Rent',
            'Salary',
            'Other',
        ];

        foreach ($categories as $name) {
            DB::table('expense_categories')->updateOrInsert(
                ['name' => $name],
                [
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
