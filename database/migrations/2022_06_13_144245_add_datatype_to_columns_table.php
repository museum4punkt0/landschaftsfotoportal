<?php

use App\Column;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatatypeToColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->string('data_type_name')->default('')
                ->comment('Redundant name of data_type_fk. Improves performance.');
        });

        // Fill new column with name of data type
        foreach (Column::all() as $column) {
            $column->data_type_name = $column->getDataTypeName();
            $column->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->dropColumn('data_type_name');
        });
    }
}
