<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE documents
            MODIFY document_module ENUM(
                'employee_doc',
                'tool_doc',
                'vehicle_doc',
                'property_doc',
                'company_doc',
                'warehouse_transfer_doc',
                'delivery_note_doc',
                'stock_entry_doc',
                'item_list_doc'
            ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE documents
            MODIFY document_module ENUM(
                'employee_doc',
                'tool_doc',
                'vehicle_doc',
                'property_doc',
                'company_doc',
                'warehouse_transfer_doc',
                'delivery_note_doc',
                'stock_entry_doc'
            ) NOT NULL");
    }
};
