<?php

use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default attributes for lists
        DB::table('attributes')->insert([
            ['name' => 'code', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'config', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'name_de', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'name_en', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'description_de', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'description_en', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'placeholder_de', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'placeholder_en', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
