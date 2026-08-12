<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Táblák listája, amelyeket egységesíteni kell.
     * (Itt sorold fel az összes érintett táblát)
     */
    protected $tables = [
        'delivery_note_items',
        'warehouse_transfer_items',
        'inventory',
        'products',
        'purchase_tickets',
        'purchase_ticket_items',      // ha még létezik (deprecated)
        'consignment_receipts',
        'consignment_receipt_items',   // ha még létezik
        'stock_entries',
        'stock_entry_items',           // ha még létezik
        'item_list',
        'item_list_items',             // ha még létezik
        'warehouse_transfer',
        'delivery_note',
        'businesses',
        'warehouses',
        'customers',
        'customer_addresses',
    ];

    public function up()
    {
        // Beállítjuk az adatbázis kapcsolat karakterkészletét (csak biztonság kedvéért)
        DB::statement('SET NAMES utf8mb4');

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                } catch (\Exception $e) {
                    // Naplózhatod a hibát, de ne álljon le a migráció
                    \Log::warning("Nem sikerült konvertálni a(z) {$table} táblát: " . $e->getMessage());
                }
            }
        }
    }

    public function down()
    {
        // Visszaállítás: nincs egyszerű módszer az eredeti collation visszaállítására,
        // ezért a down metódus üresen hagyható, vagy opcionálisan visszaállíthatod az alapértelmezettre.
        // Ha szükséges, itt megadhatod az eredeti collation-t (pl. utf8_general_ci).
        // Jelen esetben nem csinálunk semmit.
    }
};