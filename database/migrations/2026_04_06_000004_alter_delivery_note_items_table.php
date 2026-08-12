<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_note_items', function (Blueprint $table) {
            // 1. Új oszlop: inventory_item_id
            $table->integer('inventory_item_id')->nullable()->after('delivery_note_id');
            $table->integer('accepted_at')->nullable()->after('delivery_note_id');

            // 2. Külső kulcs az inventory táblára
            //$table->foreign('inventory_item_id')->references('inventory_id')->on('inventory')->onDelete('set null');

            // 3. Elavult oszlopok eltávolítása (a refaktor részeként)
            if (Schema::hasColumn('delivery_note_items', 'foreign_receipt_id')) {
                $table->dropColumn('foreign_receipt_id');
            }
            if (Schema::hasColumn('delivery_note_items', 'foreign_receipt_name')) {
                $table->dropColumn('foreign_receipt_name');
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_note_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');

            // Visszaállítás (ha szükséges)
            if (!Schema::hasColumn('delivery_note_items', 'foreign_receipt_id')) {
                $table->integer('foreign_receipt_id')->nullable();
            }
            if (!Schema::hasColumn('delivery_note_items', 'foreign_receipt_name')) {
                $table->string('foreign_receipt_name', 250)->nullable();
            }
        });
    }
};