<?php
// database/migrations/2026_05_13_000001_add_json_header_data_to_stock_entries.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->json('header_data')->nullable()->after('status');
            // Tárolt mezők: 
            // - vzj (string)
            // - category_id (int)
            // - beszerzesi_ar (int)
            // - eladasi_ar (int)
            // - vat_type_id (int)
            // - hallmarks (array)
        });
    }

    public function down()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropColumn('header_data');
        });
    }
};