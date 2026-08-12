<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingdatasToTempSalesTable extends Migration
{
    public function up()
    {
        // migration fájlban
        Schema::table('temp_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('billingo_invoice_id')->nullable()->after('id');
            $table->string('billingo_invoice_number')->nullable();
            $table->decimal('billingo_net_total', 10, 2)->nullable();
            $table->decimal('billingo_gross_total', 10, 2)->nullable();
            $table->date('billingo_fulfillment_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('temp_sales', function (Blueprint $table) {
            $table->dropForeign(['billingo_invoice_id']);
            $table->dropColumn('billingo_invoice_number');
            $table->dropColumn('billingo_net_total');
            $table->dropColumn('billingo_gross_total');
            $table->dropColumn('billingo_fulfillment_date');
        });
    }
}