<?php

use Database\Seeders\AttributeSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\ListSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedDataTypeRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Initial seeding
        $attrSeeder = new AttributeSeeder();
        $attrSeeder->run();
        $grpSeeder = new GroupSeeder();
        $grpSeeder->run();
        $seeder = new ListSeeder();
        $seeder->addInitialLists();

        // Add new data type for relations
        $seeder->addDataTypeRelation();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
