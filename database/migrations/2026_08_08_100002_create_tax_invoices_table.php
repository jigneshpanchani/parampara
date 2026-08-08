<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Formal GST tax invoices — manually created for a single buyer.
     * Independent of the daily Sales / Sale-Invoices flow.
     */
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique();
            $table->date('invoice_date');

            // Buyer snapshot (denormalised so past invoices never change)
            $table->string('buyer_name');
            $table->text('buyer_address')->nullable();
            $table->string('buyer_gstin', 20)->nullable();
            $table->string('buyer_state', 60)->nullable();
            $table->string('buyer_contact_number', 20)->nullable();

            // Optional transport / reference meta (as on the sample invoice)
            $table->string('vehicle_no', 40)->nullable();
            $table->string('transport', 120)->nullable();
            $table->string('broker', 120)->nullable();
            $table->string('eway_bill_no', 40)->nullable();

            // Tax type: intra-state (cgst+sgst) or inter-state (igst)
            $table->boolean('is_interstate')->default(false);

            // Money snapshot
            $table->decimal('taxable_amount', 12, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('amount_in_words', 255)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
