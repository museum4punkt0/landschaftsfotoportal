<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\ColumnMapping;
use App\Detail;
use App\Item;
use Auth;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('verified');
    }

    /**
     * Get a single specimen item.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function showSpecimen($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json(['error' => 'specimen not found'], 404);
        }
        if ($item->getItemType() != '_specimen_') {
            return response()->json(['error' => 'invalid ID'], 400);
        }

        // Get all columns having an API attribute set
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)
                ->whereNotNull('api_attribute')
                ->get();

        // "meta" data of the item
        $data['id'] = $item->item_id;
        $data['barcode'] = $item->title;
        $data['modified'] = $item->updated_at;
        $data['scientific_name'] = $item->taxon->full_name;
        $data['coordinate_reference_system'] = 4326; // WGS84
        $data['reference'] = route('item.show.public', $item);

        // Data from the details belonging to the item
        foreach ($colmap as $cm) {
            $detail = Detail::with('column')
                ->where('item_fk', $item->item_id)
                ->where('column_fk', $cm->column_fk)
                ->first();

            if ($detail) {
                // Details are stored in different columns within database table
                switch ($detail->column->getDataType()) {
                    case '_integer_':
                        //$data[$cm->api_attribute] = var_dump($detail->value_int);
                        $data[$cm->api_attribute] = $detail->value_int;
                        break;
                    case '_float_':
                        //$data[$cm->api_attribute] = var_dump($detail->value_float);
                        $data[$cm->api_attribute] = $detail->value_float;
                        break;
                    default:
                        $data[$cm->api_attribute] = $detail->value_string;
                }
            }
        }

        // Data of images belonging to the item
        $images = Item::ofItemType('_image_')
                    ->where('parent_fk', $id)
                    ->where('public', 1)
                    ->get();

        foreach ($images as $image) {
            $media[] = [
                'title' => $image->getDetailWhereDataType('_image_title_'),
                'copyright' => $image->getDetailWhereDataType('_image_copyright_'),
                'thumbnail' => asset('storage/' . config('media.preview_dir') .
                            $image->getDetailWhereDataType('_image_')),
            ];
        }
        $data['media'] = $media;

        return response()->json(['data' => $data]);
    }
}
