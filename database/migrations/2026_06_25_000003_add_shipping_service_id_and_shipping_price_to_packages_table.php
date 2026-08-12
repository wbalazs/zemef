<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'shipping_service_id')) {
                $table->unsignedBigInteger('shipping_service_id')->nullable()->after('order_id');
            }

            if (!Schema::hasColumn('packages', 'shipping_price')) {
                $table->decimal('shipping_price', 12, 2)->default(0)->after('shipping_service_id');
            }
        });

        if (Schema::hasColumn('packages', 'shipping_service') && Schema::hasTable('shipping_services')) {
            $packages = DB::table('packages')->select('id', 'shipping_service')->get();
            foreach ($packages as $package) {
                if (empty($package->shipping_service)) {
                    continue;
                }

                $service = DB::table('shipping_services')
                    ->where('code', $package->shipping_service)
                    ->first();

                if (!$service) {
                    $serviceId = DB::table('shipping_services')->insertGetId([
                        'name' => $package->shipping_service,
                        'code' => $package->shipping_service,
                        'base_price' => 0,
                        'supports_cod' => $package->shipping_service === 'MPL',
                        'status' => true,
                        'sort_order' => 999,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $serviceId = $service->id;
                }

                DB::table('packages')->where('id', $package->id)->update([
                    'shipping_service_id' => $serviceId,
                ]);
            }
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->index('shipping_service_id', 'packages_shipping_service_id_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'shipping_service_id')) {
                $table->dropIndex('packages_shipping_service_id_index');
                $table->dropColumn('shipping_service_id');
            }

            if (Schema::hasColumn('packages', 'shipping_price')) {
                $table->dropColumn('shipping_price');
            }
        });
    }
};
