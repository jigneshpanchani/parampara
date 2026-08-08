<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seller details needed on a GST tax invoice (state for CGST/SGST vs IGST,
     * bank details, food licence). All nullable — shown on the tax invoice only
     * when filled in Settings.
     */
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('state_code', 60)->nullable()->after('gst_number');
            $table->string('fssai_number', 40)->nullable()->after('state_code');
            $table->string('bank_name', 120)->nullable()->after('fssai_number');
            $table->string('bank_account_number', 40)->nullable()->after('bank_name');
            $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn(['state_code', 'fssai_number', 'bank_name', 'bank_account_number', 'bank_ifsc']);
        });
    }
};
