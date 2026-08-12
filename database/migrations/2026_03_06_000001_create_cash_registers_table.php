<?php
// database/migrations/2024_01_01_000001_create_cash_registers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('cash_registers');
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id('cash_register_id');
            $table->string('name');
            $table->string('prefix')->unique();
            $table->integer('currency_id');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->date('opening_balance_date');
            $table->integer('business_id');
            $table->integer('employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            //$table->foreign('currency_id')->references('id')->on('master_currencies');
            //$table->foreign('business_id')->references('business_id')->on('business');
            //$table->foreign('employee_id')->references('employee_id')->on('employees');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_registers');
    }
};