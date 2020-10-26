<?php

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default user groups
        DB::table('groups')->insert([
            [
                'name' => 'registered',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{}',
            ], [
                'name' => 'author',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{"show-dashboard": true}',
            ], [
                'name' => 'editor',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{"show-dashboard": true}',
            ], [
                'name' => 'publisher',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{"show-dashboard": true}',
            ], [
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{"show-dashboard": true}',
            ], [
                'name' => 'super-admin',
                'created_at' => now(),
                'updated_at' => now(),
                'permissions' => '{"show-dashboard": true}',
            ]
        ]);
    }
}
