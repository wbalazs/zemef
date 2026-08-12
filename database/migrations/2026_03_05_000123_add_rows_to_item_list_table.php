<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRowsToItemListTable extends Migration
{
    public function up()
    {
        Schema::table('item_list', function (Blueprint $table) {
            $table->datetime('printed_at')->nullable();
            $table->integer('business_id')->nullable();
            $table->integer('storage_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_list');
    }
}
