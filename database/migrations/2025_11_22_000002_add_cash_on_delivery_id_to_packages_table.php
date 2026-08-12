<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashOnDeliveryIdToPackagesTable extends Migration
{
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
           $table->foreignId('cash_on_delivery_id')->nullable()->constrained()->onDelete('set null');
           $table->foreignId('package_size_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['cash_on_delivery_id']);
            $table->dropColumn('cash_on_delivery_id');
            $table->dropForeign(['package_size_id']);
            $table->dropColumn('package_size_id');
        });
    }
}