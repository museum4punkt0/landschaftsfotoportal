<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_revisions', function (Blueprint $table) {
            $table->id('item_revision_id');
            $table->integer('revision');
            $table->integer('item_fk')->nullable(true);
            $table->integer('parent_fk')->nullable(true);
            $table->integer('item_type_fk');
            $table->integer('taxon_fk')->nullable(true);
            $table->string('title')->nullable(true);
            $table->integer('public')->default(0);
            $table->integer('created_by')->nullable(true)->comment("The original creator of the item");
            $table->integer('updated_by')->nullable(true)->comment("The creator of this revision (means: editor of the item)");
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
        Schema::dropIfExists('item_revisions');
    }
}
