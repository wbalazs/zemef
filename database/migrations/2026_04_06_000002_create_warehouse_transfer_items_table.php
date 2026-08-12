<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('warehouse_transfer_items');

        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
            $table->increments('transfer_items_id'); // bigint(20) auto_increment
            $table->integer('warehouse_transfer_id');
            $table->integer('inventory_item_id');
            $table->integer('quantity')->default(1);
            $table->integer('warehouse_from')->nullable();
            $table->integer('warehouse_to')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('back_to_storage_at')->nullable();
            $table->integer('accepted_employee_id')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->boolean('is_repair')->default(0);
            $table->integer('repair_price')->nullable();
            $table->dateTime('repaired_at')->nullable();
            $table->timestamps(); // created_at, updated_at

            // Külső kulcsok
            //$table->foreign('warehouse_transfer_id')->references('warehouse_transfer_id')->on('warehouse_transfer')->onDelete('cascade');
            //$table->foreign('inventory_item_id')->references('inventory_id')->on('inventory')->onDelete('cascade');
            //$table->foreign('warehouse_from')->references('warehouse_id')->on('warehouses');
            //$table->foreign('warehouse_to')->references('warehouse_id')->on('warehouses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_transfer_items');
    }
};