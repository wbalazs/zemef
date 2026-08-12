<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageItemsTable extends Migration
{
    public function up()
    {
        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id');
            $table->bigInteger('order_item_id');
            $table->integer('quantity');
            $table->timestamps();

            //$table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            //$table->foreign('order_item_id')->references('id')->on('webshop_order_items');
            //$table->index(['package_id', 'order_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_items');
    }
}