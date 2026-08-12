<?php
// database/migrations/2026_05_13_000002_add_json_item_data_to_inventory.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->integer('sorszam')->nullable()->after('product_id');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('sorszam');
        });
    }
};