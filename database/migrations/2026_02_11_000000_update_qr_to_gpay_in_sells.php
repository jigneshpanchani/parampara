<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'qr' payment mode to 'gpay'
        DB::table('sells')
            ->where('payment_mode', 'qr')
            ->update(['payment_mode' => 'gpay']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'gpay' back to 'qr'
        DB::table('sells')
            ->where('payment_mode', 'gpay')
            ->update(['payment_mode' => 'qr']);
    }
};

