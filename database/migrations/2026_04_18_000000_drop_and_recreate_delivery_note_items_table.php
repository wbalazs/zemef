<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Eldobjuk a régi táblát
        Schema::dropIfExists('delivery_note_items');

        // 2. Létrehozzuk az új táblát
        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id('note_items_id'); // bigint, auto_increment
            $table->integer('delivery_note_id');
            $table->integer('inventory_item_id')->nullable(); // kapcsolat az inventory táblához

            // Denormalizált mezők (a szállítás pillanatképéhez)
            $table->string('product_name', 300)->nullable();
            $table->text('product_description')->nullable();
            $table->string('factory_number', 300)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('price')->nullable(); // eladási ár a szállításkor

            // Raktárak (innen, hová)
            $table->integer('warehouse_from')->nullable();
            $table->integer('warehouse_to')->nullable();

            // Elfogadás adatai
            $table->integer('accepted_employee_id')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps(); // created_at, updated_at

            // Külső kulcsok
            $table->foreign('delivery_note_id')->references('delivery_note_id')->on('delivery_note')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('inventory_id')->on('inventory')->onDelete('set null');
            $table->foreign('warehouse_from')->references('warehouse_id')->on('warehouses');
            $table->foreign('warehouse_to')->references('warehouse_id')->on('warehouses');
            $table->foreign('accepted_employee_id')->references('employee_id')->on('employees');
        });
    }

    public function down()
    {
        // Visszaállítás (csak a tábla eldobása, az eredeti állapotot nem tudjuk visszaállítani)
        Schema::dropIfExists('delivery_note_items');
        // Itt lehetne egy régi sémát visszaállítani, de a downgrade nem kötelező
    }
};