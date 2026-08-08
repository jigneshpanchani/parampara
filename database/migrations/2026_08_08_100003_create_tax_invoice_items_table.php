<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Line items for a tax invoice. Product details are snapshotted so edits to
     * the product catalogue never alter an issued invoice.
     */
    public function up(): void
    {
        Schema::create('tax_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_invoice_id')->constrained('tax_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('product_name');
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit_of_measure', 20)->nullable();

            $table->decimal('quantity', 12, 3)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(0);

            $table->decimal('taxable_amount', 12, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoice_items');
    }
};
