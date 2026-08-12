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
        Schema::table('purchase_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_tickets', 'options')) {
                $table->json('options')->nullable()->after('payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_tickets', 'options')) {
                $table->dropColumn('options');
            }
        });
    }
};
