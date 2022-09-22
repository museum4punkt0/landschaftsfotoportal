<?php

use App\Column;
use App\Element;
use App\Selectlist;
use Database\Seeders\ListSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ChangeImageDataTypes extends Migration
{
    private $list_id = null;

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

        // Get the list holding data types
        $data_type_list = Selectlist::where([
            'name' => '_data_type_',
            'internal' => true,
        ])->first();
        $this->list_id = $data_type_list->list_id;

        // Replace old image related data types by generic ones
        $this->changeDataTypeOfColumns('_image_title_', '_string_', true);
        $this->changeDataTypeOfColumns('_image_copyright_', '_string_', true);
        $this->changeDataTypeOfColumns('_image_ppi_', '_integer_', true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If you really need to go back, you should be able to travelling in time.
        // Otherwise, you have to implement this missing migration...
    }

    /**
     * Change the data type of all columns with a given data type.
     *
     * @param  String  $old
     * @param  String  $new
     * @return void
     */
    private function changeDataTypeOfColumns($old, $new, $destroy=false)
    {
        // Get list element holding given old data type
        $data_type = Element::whereHas('values', function (Builder $query) use ($new) {
            $query->where('value', $new);
        })->firstWhere('list_fk', $this->list_id);

        // Get all columns with given old data type to be replaced
        $columns = Column::where('data_type_name', $old)->get();
        foreach ($columns as $column) {
            $column->data_type_name = $new;
            $column->data_type_fk = $data_type->element_id;
            $column->save();

            Log::channel('migration')->info(
                'Replaced column data type: '. $old .' -> '. $new .'; for '. $column->description, [
                    'column' => $column->column_id,
                ]
            );
        }
        // Get rid of old data type
        if ($destroy === true) {
            $this->destroyDataType($old);
        }
    }

    /**
     * Destroy the data type with given name.
     *
     * @param  String  $name
     * @return void
     */
    private function destroyDataType($name)
    {
        // Get list element holding given data type
        $data_type = Element::whereHas('values', function (Builder $query) use ($name) {
            $query->where('value', $name);
        })->firstWhere('list_fk', $this->list_id);

        // The data type might not exist anymore
        if ($data_type) {
            // Only the element will be deleted. Its values are kept, just in case we need to recover
            $data_type->delete();

            Log::channel('migration')->info(
                'Deleted data type: '. $name, [
                    'element' => $data_type->element_id,
                ]
            );
        }
    }
}
