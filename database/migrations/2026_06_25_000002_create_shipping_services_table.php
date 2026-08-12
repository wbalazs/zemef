<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->boolean('supports_cod')->default(false);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('shipping_services')->insert([
            [
                'name' => 'GLS',
                'code' => 'GLS',
                'base_price' => 0,
                'supports_cod' => false,
                'status' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DPD',
                'code' => 'DPD',
                'base_price' => 0,
                'supports_cod' => false,
                'status' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UPS',
                'code' => 'UPS',
                'base_price' => 0,
                'supports_cod' => false,
                'status' => true,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MPL',
                'code' => 'MPL',
                'base_price' => 0,
                'supports_cod' => true,
                'status' => true,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Posta pont/csomagautomata',
                'code' => 'Posta_pont_csomagautomata',
                'base_price' => 0,
                'supports_cod' => false,
                'status' => true,
                'sort_order' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_services');
    }
};
