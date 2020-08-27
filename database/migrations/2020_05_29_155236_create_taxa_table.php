<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxa', function (Blueprint $table) {
            $table->increments('taxon_id');
            $table->integer('parent_fk')->nullable(true);
            $table->string('taxon_name');
            $table->string('taxon_author')->nullable(true);
            $table->string('native_name')->nullable(true);
            $table->integer('valid_name')->nullable(true);
            $table->integer('rank')->nullable(true);
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
        Schema::dropIfExists('taxa');
    }
}
