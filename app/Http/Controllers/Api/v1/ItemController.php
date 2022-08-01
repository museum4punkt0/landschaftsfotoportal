<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\ColumnMapping;
use App\Detail;
use App\Item;
use App\ModuleInstance;
use Auth;
use Debugger;

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
     *
     * @OA\Get(
     *      path="/specimen/{id}",
     *      tags={"specimen"},
     *      summary="Find specimen by ID",
     *      description="Returns a single specimen",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of specimen",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              required={"data"},
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      ref="#/components/schemas/Specimen"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid ID supplied"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Specimen not found"
     *      )
     * )
     *
     * Note: OA Schema is defined in app/Http/Resources/Specimen.php
     */
    public function showSpecimen($id)
    {
        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::getByName('api-specimen-image');

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
        Debugger::startProfiling('processing-colmaps');
        $details = Detail::with('column')
            ->where('item_fk', $item->item_id)
            ->get();

        foreach ($colmap as $cm) {
            $detail = $details->firstWhere('column_fk', $cm->column_fk);

            if ($detail) {
                // Details are stored in different columns within database table
                switch ($detail->column->getDataType()) {
                    case '_integer_':
                        $data[$cm->api_attribute] = $detail->value_int;
                        break;
                    case '_float_':
                        $data[$cm->api_attribute] = $detail->value_float;
                        break;
                    default:
                        $data[$cm->api_attribute] = $detail->value_string;
                }
            }
        }
        Debugger::stopProfiling('processing-colmaps');

        // Data of images belonging to the item
        $images = Item::ofItemType('_image_')
                    ->with('details')
                    ->where('parent_fk', $id)
                    ->where('public', 1)
                    ->get();

        // Prepare meta data of all media/image items
        Debugger::startProfiling('get-details-by-name');
        foreach ($images as $image) {
            $media_meta['filename'] = optional($image->details->firstWhere('column_fk', $image_module->config['columns']['filename'] ?? null))->value_string;
            $media_meta['copyright'] = optional($image->details->firstWhere('column_fk', $image_module->config['columns']['copyright'] ?? null))->value_string;
            $media_meta['title'] = optional($image->details->firstWhere('column_fk', $image_module->config['columns']['title'] ?? null))->value_string;
            $media_meta['ppi'] = optional($image->details->firstWhere('column_fk', $image_module->config['columns']['ppi'] ?? null))->value_int;

            $media[] = [
                'title' => $media_meta['title'],
                'copyright' => $media_meta['copyright'],
                'thumbnail' => asset('storage/' . config('media.preview_dir') .
                    $media_meta['filename']),
                'zoomify' => $this->prepareZoomifyUrl($item, $media_meta),
            ];
        }
        Debugger::stopProfiling('get-details-by-name');
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
        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::getByName('api-random-image');

        $item = Item::ofItemType('_image_')
                    ->with('details')
                    ->where('public', 1)
                    ->inRandomOrder()
                    ->first();

        Debugger::startProfiling('get-details-by-name');

        $city = $item->getDetailByName('city', $image_module);
        $state = $item->getDetailByName('state', $image_module);
        $country = $item->getDetailByName('country', $image_module);
        $country = $country ? $country . ", " : "";
        $state = $state ? $state . ", " : "";

        $data = [
            'description' => $item->getDetailByName('description', $image_module),
            'author' => $item->getDetailByName('author', $image_module),
            'location' => $country . $state . $city,
            'license' => 'CC BY-SA 4.0',
            'extra' => $city ? '' : __(config('ui.frontend_layout') . '.api_missing_location'),
            'tags' => __(config('ui.frontend_layout') . '.api_hashtags'),
            'image' => asset('storage/'. config('media.medium_dir') . $item->getDetailByName('filename', $image_module)),
            'reference' => route('item.show.public', $item),
        ];

        Debugger::stopProfiling('get-details-by-name');

        return response()->json(['data' => $data]);
    }

    /**
     * Prepare URL to Zoomify image viewer.
     *
     * @param  Item  $item
     * @param  Item  $image
     * @param  ModuleInstance  $module
     * @return String
     */
    private function prepareZoomifyUrl(Item $item, $meta)
    {
        if (strpos($meta['title'], 'Gesamtansicht') === false) {
            $url = config('media.zoomify_url') . "&image=" .
                config('media.zoomify_jpg_image_path') .
                pathinfo($meta['filename'], PATHINFO_FILENAME) . ".jpg" .
                "&caption=" . rawurlencode($item->taxon->full_name . "; Barcode: " .
                    explode('_', pathinfo($meta['filename'], PATHINFO_FILENAME))[0]) .
                "&description=" . rawurlencode($meta['title']) .
                "&copyright=" . rawurlencode($meta['copyright']) .
                "&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D" .
                $meta['ppi'] / 25.4;
        }
        else {
            $url = config('media.zoomify_url') . "&image=" .
                config('media.zoomify_zif_image_path') .
                pathinfo($meta['filename'], PATHINFO_FILENAME) . ".zif" .
                "&caption=" . rawurlencode($item->taxon->full_name . "; Barcode: " .
                    explode('_', pathinfo($meta['filename'], PATHINFO_FILENAME))[0]) .
                "&description=" . rawurlencode($meta['title']) .
                "&copyright=" . rawurlencode($meta['copyright']) .
                "&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D" .
                $meta['ppi'] / 25.4;
        }
        return $url;
    }
}
