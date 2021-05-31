<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicToColumnMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('column_mapping', function (Blueprint $table) {
            $table->integer('public')->default(0);
        });
        
        DB::table('column_mapping')
            ->where('public', 0)
            ->update(['public' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('column_mapping', function (Blueprint $table) {
            $table->dropColumn('public');
        });
    }
}
