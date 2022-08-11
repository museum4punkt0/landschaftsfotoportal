<?php

namespace App\Http\Controllers;

use App\ColumnMapping;
use App\Detail;
use App\DetailRevision;
use App\Value;
use App\Item;
use App\ItemRevision;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AjaxMapController extends Controller
{
    /**
     * Get all items with coordinates from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $cm = ColumnMapping::find(intval($request->query('colmap')));
        if (!$cm) {
            $data = ['error' => 'colmap not found'];
            return response()->json($data, 400);
        }
        $config = $cm->config_array;

        $items = Item::where('public', 1)->where('item_type_fk', intval($cm->getConfigValue('item_type')))
            // Exclude items without latitude/longitude
            ->whereHas('details', function ($query) use ($cm) {
                $query->where('column_fk', intval($cm->getConfigValue('map_lat_col')))
                      ->whereNotNull('value_float');
            })
            ->whereHas('details', function ($query) use ($cm) {
                $query->where('column_fk', intval($cm->getConfigValue('map_lon_col')))
                      ->whereNotNull('value_float');
            })
            ->with('details')
            ->get();
        
        // Create feature array for GeoJSON
        $features = $this->createPointFeaturesFromItems($items, $config);
        
        // ...and put all those in a GeoJSON feature collection
        $geojson = $this->createFeatureCollection($features);
        
        return response()->json($geojson);
    }

    /**
     * Get search result items with coordinates from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchResults(Request $request)
    {
        $cm = ColumnMapping::find(intval($request->query('colmap')));
        if (!$cm) {
            $data = ['error' => 'colmap not found'];
            return response()->json($data, 400);
        }
        $config = $cm->config_array;

        // Make sure there are any search results 
        if (session('search_results')) {
            // Get the items (which were saved by search controller to session)
            $items = Item::where('public', 1)
                ->where('item_type_fk', intval($cm->getConfigValue('item_type')))
                ->whereIn('item_id', session('search_results'))
                ->with('details')
                ->get();
            
            // Create feature array for GeoJSON
            $features = $this->createPointFeaturesFromItems($items, $config);
        }
        else {
            $features = false;
        }
        
        // ...and put all those in a GeoJSON feature collection
        $geojson = $this->createFeatureCollection($features);
        
        return response()->json($geojson);
    }

    /**
     * Get JSON configuration for map.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConfig(Request $request)
    {
        $cm = ColumnMapping::find(intval($request->query('colmap')));
        if ($cm) {
            $config = $cm->config_array;
            if (isset($config['points'])) {
                $config['api_points'] = route('map.points', ['colmap' => $request->query('colmap')]);
                if (!isset($config['marker_icon'])) {
                    $config['marker_icon'] = asset(config('geo.marker_icon', 'storage/images/dot.svg'));
                }
                if (!isset($config['marker_color'])) {
                    $config['marker_color'] = config('geo.marker_color', '#ffffff');
                }
                if (!isset($config['marker_scale'])) {
                    $config['marker_scale'] = config('geo.marker_scale', 1.0);
                }
            }
            if (isset($config['polygons'])) {
                $config['api_polygons'] = route('map.polygons', ['colmap' => $request->query('colmap')]);
            }
            return response()->json($config);
        }
        else {
            $data = ['error' => 'colmap not found'];
            return response()->json($data, 404);
        }
    }

    /**
     * Get polygon features for a given item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPolygonFeaturesForItem(Request $request)
    {
        if (!Gate::allows('view', Item::find($request->query('item')))) {
            $data = ['error' => __('Unauthorized')];
            return response()->json($data, 403);
        }

        $cm = ColumnMapping::find(intval($request->query('colmap')));
        if (!$cm) {
            $data = ['error' => 'colmap not found'];
            return response()->json($data, 400);
        }
        $config = $cm->config_array;
        $polygons = [];

        // Polygon coordinates associated with list
        if ($cm->getConfigValue('polygons') == 'list') {
            // Which list
            $list_id = intval($cm->getConfigValue('polygons_list'));
            $colmaps = ColumnMapping:: // where('public', 1)
                // Only columns with data type '_list_' and given list_id from config
                whereHas('column', function ($query) use ($list_id) {
                    $query->where('list_fk', $list_id);
                })
                ->get();

            // Get GeoJSON file name and color for each colmap
            foreach ($colmaps as $list_cm) {
                if ($request->query('revision')) {
                    $element_id = DetailRevision::where('item_revision_fk', $request->query('revision'))
                                        ->where('column_fk', $list_cm->column_fk)
                                        ->first()
                                        ->element_fk;
                }
                else {
                    $element_id = Detail::where('item_fk', $request->query('item'))
                                        ->where('column_fk', $list_cm->column_fk)
                                        ->first()
                                        ->element_fk;
                }

                $filename = $list_cm->getConfigValue('polygon_file');
                // Include into json file only if element for color is set and filename exists
                if ($element_id && $filename) {
                    $element_config = Value::where('element_fk', $element_id)
                                        ->whereHas('attribute', function ($query) {
                                            $query->where('name', 'config');
                                        })
                                        ->first();
                    $color = json_decode(optional($element_config)->value, true)['polygon_color'] ?? '#ffffff';
                    $polygons[] = [
                        'polygon_file' => asset('storage/map/' . $filename),
                        'polygon_color' => $color,
                    ];
                }
            }
        }

        return response()->json($polygons);
    }

    /**
     * Get point features for a given item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPointFeaturesForItem(Request $request)
    {
        $cm = ColumnMapping::find(intval($request->query('colmap')));
        if (!$cm) {
            $data = ['error' => 'colmap not found'];
            return response()->json($data, 400);
        }
        $config = $cm->config_array;

        // Point coordinates from item itself
        if ($cm->getConfigValue('points') == 'self') {
            if ($request->query('revision')) {
                $query = ItemRevision::where('item_revision_id', $request->query('revision'));
            }
            else {
                $query = Item::where('item_id', $request->query('item'));
            }
        }
        // Point coordinates from item's children
        if ($cm->getConfigValue('points') == 'children') {
            if ($request->query('revision')) {
                $item_id = ItemRevision::find($request->query('revision'))->item_fk;
                $query = Item::find($item_id)->descendants();
            }
            else {
                $query = Item::find($request->query('item'))->descendants();
            }
        }
        // Filter items by item_type, if set
        if ($cm->getConfigValue('item_type')) {
            $query = $query->where('item_type_fk', intval($cm->getConfigValue('item_type')));
        }
        // Filter by public items depending on user
        if (!Gate::allows('view', Item::find($request->query('item')))) {
            $query = $query->where('public', 1);
        }

        $items = $query->with('details')->get();

        // Create feature array for GeoJSON
        $features = $this->createPointFeaturesFromItems($items, $config);

        // ...and put all those in a GeoJSON feature collection
        $geojson = $this->createFeatureCollection($features);

        return response()->json($geojson);
    }

    /**
     * Get source URL of image thumbnail or empty string if unavailable.
     *
     * @param  \App\Item  $item
     * @param  Array  $config
     * @return String
     */
    private function getImageSource(Item $item, $config) {
        $src = false;
        if (data_get($config, 'image_col')) {
            $detail = $item->details->firstWhere('column_fk', data_get($config, 'image_col'));
            if ($detail) {
                $src = asset('storage/'. config('media.preview_dir')) . '/' . $detail->value_string;
            }
        }
        return $src;
    }

    /**
     * Create an array of OpenLayers point features from given items
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $items
     * @param  array  $config
     * @return array
     */
    private function createPointFeaturesFromItems($items, $config) {
        $features = false;
        
        foreach ($items as $item) {
            // Check for missing lat/lon details
            if ($item->details->firstWhere('column_fk', data_get($config, 'map_lat_col'))
                && $item->details->firstWhere('column_fk', data_get($config, 'map_lon_col'))) {
                $lat = $item->details->firstWhere('column_fk', data_get($config, 'map_lat_col'))->value_float;
                $lon = $item->details->firstWhere('column_fk', data_get($config, 'map_lon_col'))->value_float;
                // Check for lat/lon beeing 0.0
                if ($lon && $lat) {
                    $features[] = [
                        'type' => 'Feature',
                        'id' => $item->item_id,
                        'properties' => [
                            'name' => optional($item->details->firstWhere(
                                    'column_fk', data_get($config, 'title_col')
                                ))->value_string,
                            'preview' => $this->getImageSource($item, $config),
                            'details' => route('item.show.public', $item),
                        ],
                        'geometry' => [
                            'type' => 'Point',
                            'coordinates' => [$lon, $lat],
                        ],
                    ];
                }
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
