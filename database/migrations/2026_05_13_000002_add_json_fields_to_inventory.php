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
            $table->json('item_data')->nullable()->after('inv_comment');
            // Tárolt mezők:
            // - vzj (string)
            // - category_id (int)
            // - beszerzesi_ar (int)
            // - eladasi_ar (int)
            // - vat_type_id (int)
            // - hallmarks (array)
            // - statuses (array)
            // - karat_id (int)
            // - colors (array)
            // - size (string)
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('item_data');
        });
    }
};