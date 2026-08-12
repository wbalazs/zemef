<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('consignment_receipts', 'header_data')) {
            Schema::table('consignment_receipts', function (Blueprint $table) {
                $table->json('header_data')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('consignment_receipts', 'header_data')) {
            Schema::table('consignment_receipts', function (Blueprint $table) {
                $table->dropColumn('header_data');
            });
        }
    }
};
