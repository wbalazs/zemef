<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zip_city', function (Blueprint $table) {
            $table->integer('zip_id');
            $table->integer('city_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zip_city');
    }
};
