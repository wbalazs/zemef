<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('ws_product_id', 100)->nullable()->after('sku');
            $table->index('ws_product_id');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropIndex(['ws_product_id']);
            $table->dropColumn('ws_product_id');
        });
    }
};