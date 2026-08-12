<?php
// database/migrations/2024_01_01_000001_create_item_list_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_list_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_list_id');
            $table->integer('product_id');
            $table->integer('main_product_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('oneprice')->nullable();
            $table->integer('price')->nullable();
            $table->string('factory_number', 255)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->boolean('investigation')->nullable();
            $table->datetime('investigated_at')->nullable();
            $table->integer('investigation_user_id')->nullable();
            $table->boolean('hallmark')->nullable();
            $table->boolean('repair')->nullable();
            $table->boolean('is_fake')->nullable();
            $table->integer('warehouse_from')->nullable();
            $table->integer('warehouse_to')->nullable();
            $table->text('comment')->nullable();
            $table->text('inv_comment')->nullable();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->boolean('status')->nullable();
            
            // Add indexes for better performance
            $table->index('item_list_id');
            $table->index('product_id');
            $table->index('main_product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_list_items');
    }
};