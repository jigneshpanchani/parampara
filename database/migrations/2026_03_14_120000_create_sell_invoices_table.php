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
        Schema::create('sell_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique();
            $table->date('invoice_date')->unique();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('cash_total', 12, 2)->default(0);
            $table->decimal('online_total', 12, 2)->default(0);
            $table->unsignedInteger('sells_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('sells', function (Blueprint $table) {
            $table->foreignId('sell_invoice_id')->nullable()->constrained('sell_invoices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->dropForeign(['sell_invoice_id']);
        });

        Schema::dropIfExists('sell_invoices');
    }
};
