<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_number')->unique();
            $table->bigInteger('order_id');
            $table->string('shipping_service')->default('GLS');
            $table->string('tracking_number')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('created'); // created, shipped, delivered, etc.
            $table->datetime('shipped_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->timestamps();

            //$table->foreign('order_id')->references('id')->on('webshop_orders');
            $table->index('package_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
}