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
        Schema::create('customer_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'price', 'product_item']);
            $table->decimal('value', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->text('rules')->nullable();
            $table->text('json')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_card_business', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_card_id');
            $table->unsignedBigInteger('business_id');
            $table->timestamps();
        });

        Schema::create('customer_card_customer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_card_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();
        });

        Schema::create('customer_card_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_card_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
        });

        Schema::create('customer_card_product_type_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_card_id');
            $table->unsignedBigInteger('product_type_id');
            $table->decimal('percent', 5, 2);
            $table->timestamps();
        });

        Schema::create('customer_card_product_group_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_card_id');
            $table->unsignedBigInteger('product_group_id');
            $table->decimal('percent', 5, 2);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_card_product_group_rules');
        Schema::dropIfExists('customer_card_product_type_rules');
        Schema::dropIfExists('customer_card_product');
        Schema::dropIfExists('customer_card_customer');
        Schema::dropIfExists('customer_card_business');
        Schema::dropIfExists('customer_cards');
    }
};
