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
            // Make category field nullable (for backward compatibility)
            $table->string('category')->nullable()->change();

            // Make description field nullable (optional)
            $table->string('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revert changes
            $table->string('category')->nullable(false)->change();
            $table->string('description')->nullable(false)->change();
        });
    }
};
