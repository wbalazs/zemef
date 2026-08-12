<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. A kapcsolat karakterkészletének beállítása
        DB::statement('SET NAMES utf8mb4');
        DB::statement('SET CHARACTER SET utf8mb4');
        DB::statement('SET collation_connection = utf8mb4_unicode_ci');

        // 2. A teljes adatbázis alapértelmezésének beállítása (opcionális, de ajánlott)
        DB::statement('ALTER DATABASE `'.env('DB_DATABASE').'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // 3. Táblák listája, amelyeket konvertálni kell
        $tables = [
            'purchase_tickets',
            'purchase_ticket_items',
            'stock_entries',
            'stock_entry_items',
            'webshop_orders',
            'customers',
            'products',
            'inventory',
            'delivery_note',
            'delivery_note_items',
            'warehouse_transfer',
            'warehouse_transfer_items',
            'businesses',
            'warehouses',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Tábla szintű konvertálás
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                // Alapértelmezett karakterkészlet beállítása
                DB::statement("ALTER TABLE `{$table}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }

        // 4. Oszlopok explicit újradefiniálása (biztonsági lépés)
        $textColumns = [
            'purchase_tickets' => ['ticket_number', 'storno_reason'],
            'purchase_ticket_items' => ['name', 'description', 'factory_number', 'comment', 'inv_comment'],
            'stock_entries' => ['ticket_number', 'invoice_number', 'contract_number', 'storno_reason'],
            'stock_entry_items' => ['name', 'description', 'factory_number', 'comment', 'inv_comment'],
            'webshop_orders' => ['order_id', 'status', 'payment_method', 'shipping_method'],
            'customers' => ['name', 'identity_card_number', 'tax_number'],
            'products' => ['name', 'code', 'short_description', 'long_description', 'brand', 'sku'],
            'inventory' => ['name', 'description', 'factory_number', 'comment', 'inv_comment', 'code'],
        ];

        foreach ($textColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        try {
                            // VARCHAR mezők
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        } catch (\Exception $e) {
                            // Ha TEXT típusú
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        // Visszaállítás nem szükséges (vagy opcionálisan megadható az eredeti collation)
    }
};