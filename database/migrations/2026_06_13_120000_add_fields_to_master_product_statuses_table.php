<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('master_product_statuses', function (Blueprint $table) {
            if (!Schema::hasColumn('master_product_statuses', 'price')) {
                $table->string('price')->nullable()->after('name');
            }

            if (!Schema::hasColumn('master_product_statuses', 'vat_type_id')) {
                $table->integer('vat_type_id')->nullable()->after('price');
                $table->index('vat_type_id');
            }

            if (!Schema::hasColumn('master_product_statuses', 'from')) {
                $table->date('from')->nullable()->after('vat_type_id');
            }

            if (!Schema::hasColumn('master_product_statuses', 'label')) {
                $table->boolean('label')->default(false)->after('from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_product_statuses', function (Blueprint $table) {
            if (Schema::hasColumn('master_product_statuses', 'vat_type_id')) {
                $table->dropIndex(['vat_type_id']);
                $table->dropColumn('vat_type_id');
            }

            if (Schema::hasColumn('master_product_statuses', 'price')) {
                $table->dropColumn('price');
            }

            if (Schema::hasColumn('master_product_statuses', 'from')) {
                $table->dropColumn('from');
            }

            if (Schema::hasColumn('master_product_statuses', 'label')) {
                $table->dropColumn('label');
            }
        });
    }
};
