<?php
// database/migrations/2024_01_01_000003_create_cash_on_deliveries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOnDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::create('cash_on_deliveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('package_id')->unique();
            $table->decimal('amount', 10, 2); // Amount to collect
            $table->string('bank_account_number'); // Bank account for transfer
            $table->string('bank_name')->nullable();
            $table->string('recipient_name'); // Who should receive the money
            $table->string('currency')->default('HUF');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, collected, transferred, cancelled
            $table->datetime('collected_at')->nullable();
            $table->datetime('transferred_at')->nullable();
            $table->timestamps();

            //$table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_on_deliveries');
    }
}