<?php
// database/migrations/2024_01_01_000003_create_cash_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('cash_transactions');
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->integer('cash_register_id');
            $table->string('document_number'); // Prefix-EEHHNN/001
            $table->date('transaction_date');
            $table->enum('type', ['income', 'expense']); // bevétel / kiadás
            $table->decimal('amount', 15, 2);
            $table->string('payer_payee'); // Kinek/kitől
            $table->string('reason'); // Jogcím
            $table->string('reason_custom')->nullable(); // Egyéb jogcím
            $table->text('description')->nullable();
            $table->integer('created_by');
            $table->decimal('exchange_rate', 15, 6)->nullable(); // EUR árfolyam ha kell
            $table->json('denominations')->nullable(); // Címletek JSON formátumban
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
            
            //$table->foreign('cash_register_id')->references('cash_register_id')->on('cash_registers');
            //$table->foreign('created_by')->references('id')->on('users');
            $table->index('document_number');
            $table->index('transaction_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_transactions');
    }
};