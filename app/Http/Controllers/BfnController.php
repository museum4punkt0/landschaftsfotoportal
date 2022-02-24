<?php

namespace App\Http\Controllers;

use App\Item;
use App\Taxon;
use Illuminate\Http\Request;
use Auth;
use Redirect;

class BfnController extends Controller
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
     * Redirect to an item's public show route for a given BfN SIPNR.
     *
     * @param  Integer  $sipnr
     * @return \Illuminate\Http\Response
     */
    public function redirectSipnr(int $sipnr)
    {
        $taxon_id = Taxon::where('bfn_sipnr', $sipnr)->first()->taxon_id;

        $item = Item::where('taxon_fk', $taxon_id)->ofItemType('_species_')->first();

        if ($item) {
            return redirect()->route('item.show.public', $item->item_id);
        }
        else {
            abort(404);
        }
    }
}
