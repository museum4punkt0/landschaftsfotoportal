<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageTitleToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            //$table->renameColumn('title', 'menu_title');
            $table->string('page_title', 1024)->nullable(true);
        });
        Schema::table('item_revisions', function (Blueprint $table) {
            //$table->renameColumn('title', 'menu_title');
            $table->string('page_title', 1024)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_revisions', function (Blueprint $table) {
            $table->dropColumn('page_title');
            //$table->renameColumn('menu_title', 'title');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('page_title');
            //$table->renameColumn('menu_title', 'title');
        });
    }
}
