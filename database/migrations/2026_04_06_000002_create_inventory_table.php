<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('inventory_id'); // int(11) auto_increment
            $table->integer('product_id'); // NOT NULL, hivatkozás a products táblára
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('factory_number', 255)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->boolean('hallmark')->default(0);
            $table->boolean('repair')->default(0);
            $table->boolean('is_fake')->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('oneprice')->nullable();
            $table->integer('price')->nullable();
            $table->boolean('investigation')->default(0);
            $table->dateTime('investigated_at')->nullable();
            $table->integer('investigation_user_id')->nullable();
            $table->text('comment')->nullable();
            $table->text('inv_comment')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('storage_id')->nullable(); // aktuális raktárhely
            $table->enum('source_type', ['purchase', 'consignment', 'stock_entry', 'item_list'])->nullable(); // null nem megengedett, de a migráció biztonsága miatt nullable
            $table->integer('source_header_id')->nullable();
            $table->timestamps(); // created_at, updated_at datetime

            // Indexek
            $table->index(['source_type', 'source_header_id']);
            $table->index('storage_id');
            $table->index('factory_number');
            $table->index('status');

            // Külső kulcsok
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('storage_id')->references('warehouse_id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};