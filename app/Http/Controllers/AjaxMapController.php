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
            // Exclude items without latitude/longitude
            ->whereHas('details', function ($query) {
                $query->where('column_fk', 24)
                      ->whereNotNull('value_float');
            })
            ->whereHas('details', function ($query) {
                $query->where('column_fk', 25)
                      ->whereNotNull('value_float');
            })
            ->with('details')
            ->get();
        
        // Create feature array for GeoJSON
        $features = $this->createPointFeaturesFromItems($items);
        
        // ...and put all those in a GeoJSON feature collection
        $geojson = $this->createFeatureCollection($features);
        
        return response()->json($geojson);
    }

    /**
     * Get search result items with coordinates from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchResults()
    {
        // Make sure there are any search results 
        if (session('search_results')) {
            // Get the items (which were saved by search controller to session)
            $items = Item::where('public', 1)->where('item_type_fk', 39)
                ->whereIn('item_id', session('search_results'))
                ->with('details')
                ->get();
            
            // Create feature array for GeoJSON
            $features = $this->createPointFeaturesFromItems($items);
        }
        else {
            $features = false;
        }
        
        // ...and put all those in a GeoJSON feature collection
        $geojson = $this->createFeatureCollection($features);
        
        return response()->json($geojson);
    }

    /**
     * Create an array of OpenLayers point features from given items
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $items
     * @return array
     */
    private function createPointFeaturesFromItems($items) {
        $features = false;
        
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
        }
        return $features;
    }

    /**
     * Create an OpenLayers feature collection from given features
     *
     * @param  array  $features
     * @return array
     */
    private function createFeatureCollection($features) {
        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}