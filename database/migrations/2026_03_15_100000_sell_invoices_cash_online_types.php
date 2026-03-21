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
            $table->string('invoice_type', 16)->default('cash')->after('invoice_date');
        });

        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_date']);
        });

        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->unique(['invoice_date', 'invoice_type'], 'sell_invoices_date_type_unique');
        });

        Schema::table('sells', function (Blueprint $table) {
            $table->foreignId('cash_sell_invoice_id')->nullable()->constrained('sell_invoices')->nullOnDelete();
            $table->foreignId('online_sell_invoice_id')->nullable()->after('cash_sell_invoice_id')->constrained('sell_invoices')->nullOnDelete();
        });

        if (Schema::hasColumn('sells', 'sell_invoice_id')) {
            $sells = DB::table('sells')->whereNotNull('sell_invoice_id')->get();
            foreach ($sells as $sell) {
                $id = $sell->sell_invoice_id;
                $mode = $sell->payment_mode ?? 'cash';
                if (in_array($mode, ['upi', 'gpay'], true)) {
                    DB::table('sells')->where('id', $sell->id)->update(['online_sell_invoice_id' => $id]);
                } elseif ($mode === 'cash') {
                    DB::table('sells')->where('id', $sell->id)->update(['cash_sell_invoice_id' => $id]);
                } else {
                    DB::table('sells')->where('id', $sell->id)->update([
                        'cash_sell_invoice_id' => $id,
                        'online_sell_invoice_id' => $id,
                    ]);
                }
            }

            Schema::table('sells', function (Blueprint $table) {
                $table->dropForeign(['sell_invoice_id']);
            });
            Schema::table('sells', function (Blueprint $table) {
                $table->dropColumn('sell_invoice_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->foreignId('sell_invoice_id')->nullable()->after('id')->constrained('sell_invoices')->nullOnDelete();
        });

        Schema::table('sells', function (Blueprint $table) {
            $table->dropForeign(['cash_sell_invoice_id']);
            $table->dropForeign(['online_sell_invoice_id']);
        });
        Schema::table('sells', function (Blueprint $table) {
            $table->dropColumn(['cash_sell_invoice_id', 'online_sell_invoice_id']);
        });

        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->dropUnique('sell_invoices_date_type_unique');
        });
        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->unique('invoice_date');
        });
        Schema::table('sell_invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
        });
    }
};
