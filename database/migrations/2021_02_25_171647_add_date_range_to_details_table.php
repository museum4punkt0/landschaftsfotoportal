<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeToDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* To be used with belamov/postgres-range package, but doesnt work atm
         * See https://github.com/belamov/postgres-range/issues/20 for further deatails
        Schema::table('details', function (Blueprint $table) {
            $table->dateRange('value_daterange')->nullable(true);
        });
        */
        DB::statement("
            ALTER TABLE details
            ADD COLUMN value_daterange daterange;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('details', function (Blueprint $table) {
            $table->dropColumn('value_daterange');
        });
    }
}
