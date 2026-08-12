<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Kapcsolódási karakterkészlet beállítása
        DB::statement('SET NAMES utf8mb4');
        DB::statement('SET CHARACTER SET utf8mb4');
        DB::statement('SET collation_connection = utf8mb4_unicode_ci');

        $tables = [
            'delivery_note_items',
            'warehouse_transfer_items',
            'inventory',
            'products',
            'warehouse_transfer',
            'delivery_note',
            'purchase_tickets',
            'consignment_receipts',
            'stock_entries',
            'item_list',
            'businesses',
            'warehouses',
            'customers',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Tábla szintű konvertálás
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                // Biztos, ami biztos: újraállítjuk a tábla alapértelmezett kollációját
                DB::statement("ALTER TABLE `{$table}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }

        // Kifejezetten a szöveges oszlopok újraállítása (ha az előző nem vitte végig)
        $textColumns = [
            'delivery_note_items' => ['product_name', 'product_description', 'factory_number'],
            'warehouse_transfer_items' => ['note', 'transfer_items_product_name', 'transfer_items_product_description', 'transfer_items_product_factory_number'],
            'inventory' => ['name', 'description', 'factory_number', 'comment', 'inv_comment'],
            'products' => ['name', 'short_description', 'long_description', 'brand', 'sku', 'code', 'hallmark_type'],
            'warehouse_transfer' => ['warehouse_transfer_number', 'shipping_label', 'employee_name', 'business_name', 'business_address', 'storno_reason', 'receive_employee_name', 'warehouse_from_name', 'warehouse_to_name'],
            'delivery_note' => ['delivery_note_number', 'shipping_label', 'employee_name', 'business_name', 'business_address', 'customer_name', 'storno_reason', 'receive_employee_name', 'warehouse_from_name', 'warehouse_to_name'],
        ];

        foreach ($textColumns as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        try {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        } catch (\Exception $e) {
                            // Lehet, hogy nem VARCHAR, hanem TEXT – próbálkozzunk azzal
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        // Nem szükséges visszaállítás
    }
};