<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->integer('country_id')->nullable();
            $table->integer('zip_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('street_type_id')->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();
            $table->string('floor')->nullable();
            $table->string('door')->nullable();
            $table->string('parcel_number')->nullable();

            $table->string('type');

            $table->string('tax_number')->nullable();
            $table->string('eu_tax_number')->nullable();
            $table->string('name')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
