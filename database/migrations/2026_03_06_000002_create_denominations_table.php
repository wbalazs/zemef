<?php
// database/migrations/2024_01_01_000002_create_denominations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('denominations');
        Schema::create('denominations', function (Blueprint $table) {
            $table->id('denomination_id');
            $table->integer('currency_id');
            $table->string('name'); // pl. "200 Ft", "10 EUR", "Bankjegy"
            $table->decimal('value', 15, 2); // címlet értéke
            $table->string('type')->default('coin'); // coin, banknote
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            //$table->foreign('currency_id')->references('id')->on('master_currencies');
            $table->unique(['currency_id', 'value']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('denominations');
    }
};