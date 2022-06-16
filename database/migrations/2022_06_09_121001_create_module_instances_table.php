<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_instances', function (Blueprint $table) {
            // Columns
            $table->increments('module_instance_id');
            $table->string('name', 255)->unique();
            $table->string('description', 255);
            $table->integer('module_fk');
            $table->foreign('module_fk')->references('module_id')->on('modules');
            $table->jsonb('config')->default('{}'); // jsonb deletes duplicates
            $table->timestamps();
            // Indexes
            $table->index('module_fk');
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
            $table->dropIndex(['module_fk']);
            $table->dropForeign('module_instances_module_fk_foreign');
        });

        Schema::dropIfExists('module_instances');
    }
}
