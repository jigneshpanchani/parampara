<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Allow NULL so a sell can be saved without selecting a payment mode yet
        DB::statement("ALTER TABLE sells MODIFY COLUMN payment_mode ENUM('cash','upi','gpay','mix') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        // Restore non-nullable with default 'cash' (existing NULLs become 'cash')
        DB::statement("UPDATE sells SET payment_mode = 'cash' WHERE payment_mode IS NULL");
        DB::statement("ALTER TABLE sells MODIFY COLUMN payment_mode ENUM('cash','upi','gpay','mix') NOT NULL DEFAULT 'cash'");
    }
};
