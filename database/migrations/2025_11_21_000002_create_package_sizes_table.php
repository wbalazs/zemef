<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageSizesTable extends Migration
{
    public function up()
    {
        Schema::create('package_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // S, M, L, XL, etc.
            $table->string('code')->unique(); // S, M, L
            $table->decimal('length', 8, 2)->nullable(); // cm
            $table->decimal('width', 8, 2)->nullable(); // cm
            $table->decimal('height', 8, 2)->nullable(); // cm
            $table->decimal('weight_limit', 8, 2)->nullable(); // kg
            $table->decimal('price', 8, 2)->default(0); // base price for this size
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('package_sizes');
    }
}