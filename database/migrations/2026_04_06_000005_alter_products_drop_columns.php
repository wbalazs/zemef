<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'main_product_id')) {
                $table->dropColumn('main_product_id');
            }
            if (Schema::hasColumn('products', 'storage_id')) {
                $table->dropColumn('storage_id');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'main_product_id')) {
                $table->integer('main_product_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('products', 'storage_id')) {
                $table->integer('storage_id')->nullable()->after('category_id');
            }
        });
    }
};