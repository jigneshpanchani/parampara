<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('is_pay_later')->default(false)->after('pending_amount');
        });

        // Backfill: any historical sale where the customer didn't fully pay at sale time
        // was effectively a pay-later sale. `amount_paid` is the at-sale value and is never
        // touched by follow-up payments, so this stays accurate even for sales that have
        // since been cleared via the Pay modal.
        DB::statement('UPDATE sales SET is_pay_later = 1 WHERE amount_paid < total_amount');
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('is_pay_later');
        });
    }
};
