<?php

use App\ColumnMapping;
use Database\Seeders\ListSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChangeDataTypeConfigItemType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $seeder = new ListSeeder();
        // Update default config
        $seeder->addDataTypeConfig();

        // Get all column mappings with data type '_relation_'
        $colmaps = ColumnMapping::whereHas('column', function (Builder $query) {
            $query->where('data_type_name', '_relation_');
        })->get();

        $this->changeConfig($colmaps, 'item_type', 'relation_item_type');

        // Get all column mappings with data type '_map_'
        $colmaps = ColumnMapping::whereHas('column', function (Builder $query) {
            $query->where('data_type_name', '_map_');
        })->get();

        $this->changeConfig($colmaps, 'item_type', 'map_item_type');
        $this->changeConfig($colmaps, 'title_col', 'map_title_col');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get all column mappings with data type '_relation_'
        $colmaps = ColumnMapping::whereHas('column', function (Builder $query) {
            $query->where('data_type_name', '_relation_');
        })->get();

        $this->changeConfig($colmaps, 'relation_item_type', 'item_type');

        // Get all column mappings with data type '_map_'
        $colmaps = ColumnMapping::whereHas('column', function (Builder $query) {
            $query->where('data_type_name', '_map_');
        })->get();

        $this->changeConfig($colmaps, 'map_item_type', 'item_type');
        $this->changeConfig($colmaps, 'map_title_col', 'title_col');
    }

    /**
     * Change JSON config by string replacing.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $colmaps
     * @param  String  $old
     * @param  String  $new
     * @return void
     */
    private function changeConfig($colmaps, $old, $new)
    {
        foreach ($colmaps as $colmap) {
            $colmap->config = str_replace($old, $new, $colmap->config);
            $colmap->save();

            Log::channel('migration')->info(
                'Replaced colmap config: '. $old .' -> '. $new .'; for '. $colmap->column->description, [
                    'colmap' => $colmap->colmap_id,
                ]
            );
        }
    }
}
