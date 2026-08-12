<?php

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
        if (!Schema::hasTable('customer_card_inventory')) {
            Schema::create('customer_card_inventory', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_card_id');
                $table->unsignedBigInteger('inventory_id');
                $table->timestamps();

                $table->index('customer_card_id');
                $table->index('inventory_id');
                $table->unique(['customer_card_id', 'inventory_id'], 'customer_card_inventory_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_card_inventory');
    }
};
