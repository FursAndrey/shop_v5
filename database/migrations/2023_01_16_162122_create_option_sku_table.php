<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_sku', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('sku_id');
            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('sku_id')->references('id')->on('skus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('option_sku', function (Blueprint $table) {
            $table->dropForeign(['option_id']);
            $table->dropIndex('option_sku_option_id_foreign');
            $table->dropForeign(['sku_id']);
            $table->dropIndex('option_sku_sku_id_foreign');
        });
        Schema::dropIfExists('option_sku');
    }
};
