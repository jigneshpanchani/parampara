<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add category_id foreign key
            $table->foreignId('category_id')->nullable()->after('expense_date')->constrained('expense_categories')->onDelete('restrict');

            // Keep the old category column for backward compatibility
            // It will be deprecated in future versions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
