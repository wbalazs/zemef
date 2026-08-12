<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory', 'warehouse_from')) {
                $table->integer('warehouse_from')->nullable()->after('source_header_id');
            }
            if (!Schema::hasColumn('inventory', 'warehouse_to')) {
                $table->integer('warehouse_to')->nullable()->after('warehouse_from');
            }

            // Külső kulcsok a warehouses táblára (opcionális)
            $table->foreign('warehouse_from')->references('warehouse_id')->on('warehouses')->onDelete('set null');
            $table->foreign('warehouse_to')->references('warehouse_id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['warehouse_from']);
            $table->dropForeign(['warehouse_to']);
            $table->dropColumn(['warehouse_from', 'warehouse_to']);
        });
    }
};