<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToModuleInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_instances', function (Blueprint $table) {
            $table->string('position', 255)->nullable();
            $table->integer('item_fk')->nullable();
            // Indexes
            $table->index('position');
            $table->index('item_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_instances', function (Blueprint $table) {
            $table->dropIndex(['position']);
            $table->dropIndex(['item_fk']);
            $table->dropColumn('position');
            $table->dropColumn('item_fk');
        });
    }
}
