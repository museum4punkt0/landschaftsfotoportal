<?php

use App\Item;
use App\ItemRevision;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_revisions', function (Blueprint $table) {
            $table->id('detail_revision_id');
            $table->integer('detail_fk')->nullable(true);
            $table->bigInteger('item_revision_fk');
            $table->integer('item_fk')->nullable(true);
            $table->integer('column_fk');
            $table->integer('element_fk')->nullable(true);
            $table->integer('value_int')->nullable(true);
            $table->float('value_float')->nullable(true);
            $table->date('value_date')->nullable(true);
            $table->string('value_string', 65536)->nullable(true);
            $table->timestamps();
        });

        /* To be used with belamov/postgres-range package, but doesnt work atm
         * See https://github.com/belamov/postgres-range/issues/20 for further deatails
        Schema::table('details', function (Blueprint $table) {
            $table->dateRange('value_daterange')->nullable(true);
        });
        */
        DB::statement("
            ALTER TABLE detail_revisions
            ADD COLUMN value_daterange daterange;
        ");

        Schema::create('element_mapping_revisions', function (Blueprint $table) {
            $table->id('elmap_id');
            $table->bigInteger('detail_revision_fk');
            $table->integer('element_fk');
            $table->timestamps();
        });

        // Create initial revision for all items
        foreach (Item::all() as $item) {
            $item->createRevisionWithDetails(false, true);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_revisions');
        Schema::dropIfExists('element_mapping_revisions');
    }
}
