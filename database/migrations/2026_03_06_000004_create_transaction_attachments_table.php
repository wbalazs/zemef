<?php
// database/migrations/2024_01_01_000004_create_transaction_attachments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('transaction_attachments');
        Schema::create('transaction_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->integer('transaction_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
            
            //$table->foreign('transaction_id')->references('transaction_id')->on('cash_transactions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_attachments');
    }
};