<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_set_items', function (Blueprint $table) {
            $table->increments('inventory_set_item_id');
            $table->integer('inventory_set_id'); // hivatkozás a szett fejrekordra (később definiálandó tábla)
            $table->timestamp('accepted_at')->nullable();

            ///$table->foreign('inventory_item_id')->references('inventory_id')->on('inventory')->onDelete('cascade');
            $table->index('inventory_set_id');
            $table->index('inventory_item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_set_items');
    }
};