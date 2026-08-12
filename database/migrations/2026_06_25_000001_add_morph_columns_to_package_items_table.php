<?php

use App\Models\WebshopOrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('package_items')) {
            return;
        }

        Schema::table('package_items', function (Blueprint $table) {
            if (!Schema::hasColumn('package_items', 'itemable_type')) {
                $table->string('itemable_type')->nullable()->after('package_id');
            }

            if (!Schema::hasColumn('package_items', 'itemable_id')) {
                $table->unsignedBigInteger('itemable_id')->nullable()->after('itemable_type');
            }
        });

        if (Schema::hasColumn('package_items', 'order_item_id')) {
            DB::table('package_items')
                ->whereNotNull('order_item_id')
                ->whereNull('itemable_type')
                ->whereNull('itemable_id')
                ->update([
                    'itemable_type' => WebshopOrderItem::class,
                    'itemable_id' => DB::raw('order_item_id'),
                ]);
        }

        Schema::table('package_items', function (Blueprint $table) {
            $table->index(['itemable_type', 'itemable_id'], 'package_items_itemable_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('package_items')) {
            return;
        }

        if (Schema::hasColumn('package_items', 'order_item_id')) {
            DB::table('package_items')
                ->where('itemable_type', WebshopOrderItem::class)
                ->whereNotNull('itemable_id')
                ->update([
                    'order_item_id' => DB::raw('itemable_id'),
                ]);
        }

        Schema::table('package_items', function (Blueprint $table) {
            if (Schema::hasColumn('package_items', 'itemable_type') && Schema::hasColumn('package_items', 'itemable_id')) {
                $table->dropIndex('package_items_itemable_index');
                $table->dropColumn(['itemable_type', 'itemable_id']);
            }
        });
    }
};
