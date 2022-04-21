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
     */
    public function listItemsByFwTaxon(int $sipnr)
    {
        // Find taxa with given FloraWeb taxon ID
        $taxa = Taxon::where('bfn_sipnr', $sipnr)->get();
        if ($taxa->isEmpty()) {
            return response()->json(['error' => 'taxon not found'], 404);
        }

        // Get specimen items for given taxa
        $items = collect([]);
        foreach ($taxa as $taxon) {
            $items = $items->concat(
                Item::ofItemType('_specimen_')
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
                'basisOfRecord' => 'PRESERVED_SPECIMEN',
                'scientific_name' => $item->taxon->full_name,
                'modified' => $item->updated_at,
                'reference' => route('api.item.show.specimen', $item),
                'link' => route('item.show.public', $item),
            ];
        }

        return response()->json(['data' => $data]);
    }
}
