<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->increments('detail_id');
            $table->integer('item_fk');
            $table->integer('column_fk');
            $table->integer('element_fk')->nullable(true);
            $table->integer('value_int')->nullable(true);
            $table->float('value_float')->nullable(true);
            $table->date('value_date')->nullable(true);
            $table->string('value_string', 65536)->nullable(true);
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
        Schema::dropIfExists('details');
    }
}
