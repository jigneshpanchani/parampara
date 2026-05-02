<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            $table->dropForeign(['cash_sell_invoice_id']);
            $table->dropForeign(['online_sell_invoice_id']);
            $table->dropForeign(['mix_sell_invoice_id']);
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->dropForeign(['sell_id']));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->dropForeign(['sell_id']));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->dropForeign(['sell_id']));

        Schema::table('sells', function (Blueprint $table) {
            $table->renameColumn('sell_date', 'sale_date');
            $table->renameColumn('cash_sell_invoice_id', 'cash_sale_invoice_id');
            $table->renameColumn('online_sell_invoice_id', 'online_sale_invoice_id');
            $table->renameColumn('mix_sell_invoice_id', 'mix_sale_invoice_id');
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->renameColumn('sell_id', 'sale_id'));
        Schema::table('sell_invoices', fn(Blueprint $t) => $t->renameColumn('sells_count', 'sales_count'));
        Schema::table('stock_closing_items', fn(Blueprint $t) => $t->renameColumn('sell_returns_qty', 'sale_returns_qty'));
        Schema::table('products',      fn(Blueprint $t) => $t->renameColumn('sell_price', 'selling_price'));

        Schema::rename('sells',         'sales');
        Schema::rename('sell_items',    'sale_items');
        Schema::rename('sell_returns',  'sale_returns');
        Schema::rename('sell_invoices', 'sale_invoices');
        Schema::rename('sell_payments', 'sale_payments');

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('cash_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
            $table->foreign('online_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
            $table->foreign('mix_sale_invoice_id')->references('id')->on('sale_invoices')->nullOnDelete();
        });
        Schema::table('sale_items',    fn(Blueprint $t) => $t->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete());
        Schema::table('sale_returns',  fn(Blueprint $t) => $t->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete());
        Schema::table('sale_payments', fn(Blueprint $t) => $t->foreign('sale_id')->references('id')->on('sales')->cascadeOnDelete());
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cash_sale_invoice_id']);
            $table->dropForeign(['online_sale_invoice_id']);
            $table->dropForeign(['mix_sale_invoice_id']);
        });
        Schema::table('sale_items',    fn(Blueprint $t) => $t->dropForeign(['sale_id']));
        Schema::table('sale_returns',  fn(Blueprint $t) => $t->dropForeign(['sale_id']));
        Schema::table('sale_payments', fn(Blueprint $t) => $t->dropForeign(['sale_id']));

        Schema::rename('sales',         'sells');
        Schema::rename('sale_items',    'sell_items');
        Schema::rename('sale_returns',  'sell_returns');
        Schema::rename('sale_invoices', 'sell_invoices');
        Schema::rename('sale_payments', 'sell_payments');

        Schema::table('sells', function (Blueprint $table) {
            $table->renameColumn('sale_date', 'sell_date');
            $table->renameColumn('cash_sale_invoice_id', 'cash_sell_invoice_id');
            $table->renameColumn('online_sale_invoice_id', 'online_sell_invoice_id');
            $table->renameColumn('mix_sale_invoice_id', 'mix_sell_invoice_id');
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_payments', fn(Blueprint $t) => $t->renameColumn('sale_id', 'sell_id'));
        Schema::table('sell_invoices', fn(Blueprint $t) => $t->renameColumn('sales_count', 'sells_count'));
        Schema::table('stock_closing_items', fn(Blueprint $t) => $t->renameColumn('sale_returns_qty', 'sell_returns_qty'));
        Schema::table('products',      fn(Blueprint $t) => $t->renameColumn('selling_price', 'sell_price'));

        Schema::table('sells', function (Blueprint $table) {
            $table->foreign('cash_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
            $table->foreign('online_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
            $table->foreign('mix_sell_invoice_id')->references('id')->on('sell_invoices')->nullOnDelete();
        });
        Schema::table('sell_items',    fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
        Schema::table('sell_returns',  fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
        Schema::table('sell_payments', fn(Blueprint $t) => $t->foreign('sell_id')->references('id')->on('sells')->cascadeOnDelete());
    }
};
