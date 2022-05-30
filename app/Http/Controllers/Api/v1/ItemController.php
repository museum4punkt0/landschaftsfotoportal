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
                'zoomify' => $this->prepareZoomifyUrl($item, $image),
            ];
        }
        $data['media'] = $media;

        return response()->json(['data' => $data]);
    }

    /**
     * Get a single random image item.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRandomImage()
    {
        $item = Item::ofItemType('_image_')
                    ->with('details')
                    ->where('public', 1)
                    ->inRandomOrder()
                    ->first();

        $city = optional($item->details()->firstWhere('column_fk', 22))->value_string;
        $state = optional($item->details()->firstWhere('column_fk', 20))->value_string;
        $country = optional($item->details()->firstWhere('column_fk', 19))->value_string;
        $country = $country ? $country . ", " : "";
        $state = $state ? $state . ", " : "";

        $data = [
            'description' => optional($item->details()->firstWhere('column_fk', 23))->value_string,
            'author' => optional($item->details()->firstWhere('column_fk', 5))->value_string,
            'location' => $country . $state . $city,
            'license' => 'CC BY-SA 4.0',
            'extra' => $city ? '' : __(config('ui.frontend_layout') . '.api_missing_location'),
            'tags' => __(config('ui.frontend_layout') . '.api_hashtags'),
            'image' => asset('storage/'. config('media.medium_dir') . $item->getDetailWhereDataType('_image_')),
            'reference' => route('item.show.public', $item),
        ];

        return response()->json(['data' => $data]);
    }

    /**
     * Prepare URL to Zoomify image viewer.
     *
     * @param  Item  $item
     * @param  Item  $image
     * @return String
     */
    private function prepareZoomifyUrl(Item $item, Item $image)
    {
        if (strpos($image->getDetailWhereDataType('_image_title_'), 'Gesamtansicht') === false) {
            $url = config('media.zoomify_url') . "&image=" .
                config('media.zoomify_jpg_image_path') .
                pathinfo($image->getDetailWhereDataType('_image_'), PATHINFO_FILENAME) . ".jpg" .
                "&caption=" . rawurlencode($item->taxon->full_name . "; Barcode: " .
                    explode('_', pathinfo($image->getDetailWhereDataType('_image_'), PATHINFO_FILENAME))[0]) .
                "&description=" . rawurlencode($image->getDetailWhereDataType('_image_title_')) .
                "&copyright=" . rawurlencode($image->getDetailWhereDataType('_image_copyright_')) .
                "&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D" .
                $image->getDetailWhereDataType('_image_ppi_') / 25.4;
        }
        else {
            $url = config('media.zoomify_url') . "&image=" .
                config('media.zoomify_zif_image_path') .
                pathinfo($image->getDetailWhereDataType('_image_'), PATHINFO_FILENAME) . ".zif" .
                "&caption=" . rawurlencode($item->taxon->full_name . "; Barcode: " .
                    explode('_', pathinfo($image->getDetailWhereDataType('_image_'), PATHINFO_FILENAME))[0]) .
                "&description=" . rawurlencode($image->getDetailWhereDataType('_image_title_')) .
                "&copyright=" . rawurlencode($image->getDetailWhereDataType('_image_copyright_')) .
                "&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D" .
                $image->getDetailWhereDataType('_image_ppi_') / 25.4;
        }
        return $url;
    }
}
