<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

            
return new class extends Migration
{
    public function up()
    {
        Schema::table('temp_sales', function (Blueprint $table) {
            $table->integer('company_id')->nullable();
            $table->integer('business_id')->nullable();
        });
    }

    public function down()
    {
         Schema::table('temp_sales', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('business_id');
        });
    }
};
