<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('item_lists')) {
            return;
        }

        Schema::table('item_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('item_lists', 'business_id')) {
                $table->integer('business_id')->nullable()->after('printed_at');
            }

            if (!Schema::hasColumn('item_lists', 'storage_id')) {
                $table->integer('storage_id')->nullable()->after('business_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('item_lists')) {
            return;
        }

        Schema::table('item_lists', function (Blueprint $table) {
            if (Schema::hasColumn('item_lists', 'storage_id')) {
                $table->dropColumn('storage_id');
            }

            if (Schema::hasColumn('item_lists', 'business_id')) {
                $table->dropColumn('business_id');
            }
        });
    }
};
