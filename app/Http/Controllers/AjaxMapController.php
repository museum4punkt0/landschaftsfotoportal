<?php

namespace App\Http\Controllers;

use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AjaxMapController extends Controller
{
    /**
     * Get all items with coordinates from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $items = Item::where('public', 1)->where('item_type_fk', 39)
            ->with('details')
            ->get();
        
        // Create feature array for GeoJSON
        foreach ($items as $item) {
            // Check for missing lat/lon details
            if ($item->details->firstWhere('column_fk', 25) && $item->details->firstWhere('column_fk', 24)) {
                $features[] = [
                    'type' => 'Feature',
                    'id' => $item->item_id,
                    'properties' => [
                        'name' => $item->details->firstWhere('column_fk', 23)->value_string,
                        'preview' => asset('storage/'. config('media.preview_dir')) . '/'
                            . $item->details->firstWhere('column_fk', 13)->value_string,
                        'details' => route('item.show.public', $item),
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            floatval($item->details->firstWhere('column_fk', 25)->value_float),
                            floatval($item->details->firstWhere('column_fk', 24)->value_float),
                        ],
                    ],
                ];
            }
            else {
                /* for debugging
                $features[] = [
                    'type' => 'MISSING',
                    'id' => $item->item_id,
                ];
                */
            }
        }
        // ...and put all those in a GeoJSON feature collection
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
        
        return response()->json($geojson);
    }
}
