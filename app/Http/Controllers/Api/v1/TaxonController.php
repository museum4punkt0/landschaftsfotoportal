<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Item;
use App\Taxon;
use Auth;

class TaxonController extends Controller
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
     * Get all specimen items for a given BfN FloraWeb TaxonId (former SIPNR).
     *
     * @param  Integer  $sipnr
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *      path="/taxon/fwTaxonId/{id}/items",
     *      tags={"taxon"},
     *      summary="Finds items by taxon",
     *      description="Returns a list of available items for a given taxon",
     *      @OA\Parameter(
     *          name="id",
     *          description="FloraWeb taxon ID (former SIPNR)",
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
     *                      ref="#/components/schemas/Item"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Taxon not found"
     *      )
     * )
     *
     * Note: OA Schema is defined in app/Http/Resources/Item.php
     */
    public function listItemsByFwTaxon(int $sipnr)
    {
        // Find taxa with given FloraWeb taxon ID
        $taxa = Taxon::where('bfn_sipnr', $sipnr)->get();
        if ($taxa->isEmpty()) {
            return response()->json(['error' => 'taxon not found'], 404);
        }

        // Get items of specific item type for given taxa
        $items = collect([]);
        foreach ($taxa as $taxon) {
            $items = $items->concat(
                Item::ofItemType(config('api.items.item_type'))
                    ->with('taxon')
                    ->where('taxon_fk', $taxon->taxon_id)
                    ->where('public', 1)
                    ->get()
            );
        }

        // Extract data from items
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->item_id,
                'basisOfRecord' => config('api.items.basis_of_record'),
                'scientific_name' => $item->taxon->full_name,
                'modified' => $item->updated_at,
                'last_crawled' => date(DATE_RFC3339),
                'reference' => route(config('api.items.reference_route'), $item),
                'link' => route('item.show.public', $item),
            ];
        }

        return response()->json(['data' => $data]);
    }
}
