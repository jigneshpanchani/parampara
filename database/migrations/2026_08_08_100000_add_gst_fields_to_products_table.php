<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * GST/tax invoice defaults per product. All nullable — existing products and
     * the Sales / Sale-Invoices flow are unaffected.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn_code', 20)->nullable()->after('product_code');
            $table->string('unit_of_measure', 20)->nullable()->after('hsn_code');
            $table->decimal('gst_rate', 5, 2)->default(0)->after('unit_of_measure');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'unit_of_measure', 'gst_rate']);
        });
    }
};
