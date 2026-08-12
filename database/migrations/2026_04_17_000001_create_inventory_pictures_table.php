<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_pictures', function (Blueprint $table) {
            $table->id('inventory_picture_id');
            $table->unsignedBigInteger('inventory_id');
            $table->string('image_path');
            $table->string('image_date')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
/*
            $table->foreign('inventory_id')
                  ->references('inventory_id')
                  ->on('inventory')
                  ->onDelete('cascade');
                  */
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_pictures');
    }
};
