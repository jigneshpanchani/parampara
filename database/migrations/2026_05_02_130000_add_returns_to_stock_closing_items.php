<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_closing_items', function (Blueprint $table) {
            $table->decimal('purchase_returns_qty', 12, 2)->default(0)->after('purchased_qty');
            $table->decimal('sell_returns_qty', 12, 2)->default(0)->after('sold_qty');
        });
    }

    public function down(): void
    {
        Schema::table('stock_closing_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_returns_qty', 'sell_returns_qty']);
        });
    }
};
