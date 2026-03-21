<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sells', function (Blueprint $table) {
            $table->foreignId('mix_sell_invoice_id')->nullable()->after('online_sell_invoice_id')->constrained('sell_invoices')->nullOnDelete();
        });

        // Move mix payment sells to mix_sell_invoice_id only (one FK per mix sale)
        if (Schema::hasColumn('sells', 'cash_sell_invoice_id')) {
            $mixSells = DB::table('sells')->where('payment_mode', 'mix')->get();
            foreach ($mixSells as $sell) {
                $mixInvoiceId = $sell->cash_sell_invoice_id ?: $sell->online_sell_invoice_id;
                DB::table('sells')->where('id', $sell->id)->update([
                    'mix_sell_invoice_id' => $mixInvoiceId,
                    'cash_sell_invoice_id' => null,
                    'online_sell_invoice_id' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->dropForeign(['mix_sell_invoice_id']);
            $table->dropColumn('mix_sell_invoice_id');
        });

        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
