<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_closings', function (Blueprint $table) {
            $table->id();
            $table->date('closing_date');
            $table->string('period_label', 20); // e.g. "2026-04"
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_closing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_closing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('opening_stock', 12, 2)->default(0);
            $table->decimal('purchased_qty', 12, 2)->default(0);
            $table->decimal('sold_qty', 12, 2)->default(0);
            $table->decimal('expected_stock', 12, 2)->default(0);
            $table->decimal('actual_stock', 12, 2)->default(0);
            $table->decimal('difference', 12, 2)->default(0); // expected - actual (positive = missing, negative = surplus)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_closing_items');
        Schema::dropIfExists('stock_closings');
    }
};
