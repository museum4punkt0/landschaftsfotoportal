<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGroupOrderToColumnMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('column_mapping', function (Blueprint $table) {
            $table->integer('column_group_fk')->nullable(true);
            $table->integer('column_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('column_mapping', function (Blueprint $table) {
            $table->dropColumn('column_group_fk');
            $table->dropColumn('column_order');
        });
    }
}
