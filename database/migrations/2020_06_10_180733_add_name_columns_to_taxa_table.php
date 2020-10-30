<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameColumnsToTaxaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxa', function (Blueprint $table) {
            $table->string('taxon_suppl')->nullable(true);
            $table->string('full_name')->default('');
            $table->string('rank_abbr')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxa', function (Blueprint $table) {
            $table->dropColumn('taxon_suppl');
            $table->dropColumn('full_name');
            $table->dropColumn('rank_abbr');
        });
    }
}
