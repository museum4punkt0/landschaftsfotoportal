<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemFkToDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('details', function (Blueprint $table) {
            $table->integer('related_item_fk')->nullable(true);
            $table->foreign('related_item_fk')->references('item_id')->on('items');
        });
        Schema::table('detail_revisions', function (Blueprint $table) {
            $table->integer('related_item_fk')->nullable(true);
            $table->foreign('related_item_fk')->references('item_id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_revisions', function (Blueprint $table) {
            $table->dropForeign('detail_revisions_related_item_fk_foreign');
            $table->dropColumn('related_item_fk');
        });
        Schema::table('details', function (Blueprint $table) {
            $table->dropForeign('details_related_item_fk_foreign');
            $table->dropColumn('related_item_fk');
        });
    }
}
