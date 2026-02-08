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
        Schema::table('sells', function (Blueprint $table) {
            $table->decimal('cash_amount', 10, 2)->default(0)->after('amount_paid');
            $table->decimal('online_amount', 10, 2)->default(0)->after('cash_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->dropColumn(['cash_amount', 'online_amount']);
        });
    }
};

