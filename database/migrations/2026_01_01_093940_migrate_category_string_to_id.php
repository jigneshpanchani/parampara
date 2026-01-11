<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate old category string values to category_id
        // For each unique category name, create a category if it doesn't exist
        // Then update the expense to use the category_id

        $expenses = DB::table('expenses')
            ->whereNotNull('category')
            ->whereNull('category_id')
            ->distinct()
            ->pluck('category');

        foreach ($expenses as $categoryName) {
            // Find or create the category
            $category = DB::table('expense_categories')
                ->where('name', $categoryName)
                ->first();

            if (!$category) {
                $categoryId = DB::table('expense_categories')->insertGetId([
                    'name' => $categoryName,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $categoryId = $category->id;
            }

            // Update expenses with this category name to use the category_id
            DB::table('expenses')
                ->where('category', $categoryName)
                ->update(['category_id' => $categoryId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset category_id to null for expenses that were migrated
        DB::table('expenses')
            ->whereNotNull('category_id')
            ->update(['category_id' => null]);
    }
};
