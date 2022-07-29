<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $seeder = new ModuleSeeder();
        $seeder->addRandomImage();
        $seeder->addApiRandomImage();
        $seeder->addApiSpecimenImage();
        $seeder->addDownloadImage();
        $seeder->addTimeline();
        $seeder->addGallery();
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
