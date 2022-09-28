<?php

namespace App\Http\Controllers\Admin;

use App\Taxon;
use App\Http\Controllers\Controller;
use App\Utils\Localization;
use Illuminate\Http\Request;
use Redirect;

class TaxonController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        //Show error if comments are disabled
        abort_if(!config('ui.taxa'), 403, __('common.module_disabled'));

        // Use app\Policies\TaxonPolicy for authorizing ressource controller
        $this->authorizeResource(Taxon::class, 'taxon');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aFilter = [
            'taxon_id' => $request->input('taxon_id'),
            'taxon_name' => $request->input('taxon_name'),
            'native_name' => $request->input('native_name'),
            'valid_name' => $request->input('valid_name'),
            'rank_abbr' => $request->input('rank_abbr'),
            'gsl_id' => $request->input('gsl_id'),
            'bfn_namnr' => $request->input('bfn_namnr'),
            'bfn_sipnr' => $request->input('bfn_sipnr'),
        ];
//        dd($aFilter);
        $orderby = $request->input('orderby', 'taxon_id');
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');

        $aWhere = [];
        if (!is_null($aFilter['taxon_id'])) {
            $aWhere[] = ['taxon_id', '=', $aFilter['taxon_id']];
        }
        if (!is_null($aFilter['taxon_name'])) {
            $aWhere[] = ['taxon_name', 'ilike', '%' . $aFilter['taxon_name'] . '%'];
        }
        if (!is_null($aFilter['native_name'])) {
            $aWhere[] = ['native_name', 'ilike', '%' . $aFilter['native_name'] . '%'];
        }
        if (!is_null($aFilter['valid_name'])) {
            if($aFilter['valid_name'] == -1){
                $aWhere[] = ['valid_name', '!=', null];
            }else{
                $aWhere[] = ['valid_name', '=', null];
            }
        }
        if (!is_null($aFilter['rank_abbr'])) {
            $aWhere[] = ['rank_abbr', 'ilike', '%' . $aFilter['rank_abbr'] . '%'];
        }
        if (!is_null($aFilter['gsl_id'])) {
            $aWhere[] = ['gsl_id', 'ilike', '%' . $aFilter['gsl_id']. '%'];
        }
        if (!is_null($aFilter['bfn_namnr'])) {
            $aWhere[] = ['bfn_namnr', 'ilike', '%' . $aFilter['bfn_namnr']. '%'];
        }
        if (!is_null($aFilter['bfn_sipnr'])) {
            $aWhere[] = ['bfn_sipnr', 'ilike', '%' . $aFilter['bfn_sipnr']. '%'];
        }

        if (count($aWhere) > 0) {
            $taxa = Taxon::orderBy($orderby, $sort)
                    ->orWhere($aWhere)
                    ->paginate($limit)
                    ->withQueryString();
        }
        elseif( !is_null($request->input('sort')) ){
            $taxa = Taxon::orderBy($orderby, $sort)
                    ->paginate($limit)
                    ->withQueryString();
        }
        else {
             $taxa = Taxon::tree()->depthFirst()->paginate($limit);
        }
//        dd($taxa);
        return view('admin.taxon.list', compact('taxa', 'aFilter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.taxon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'parent' => 'nullable|integer',
            'taxon_name' => 'required|string|max:255',
            'taxon_author' => 'nullable|string|max:255',
            'taxon_suppl' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'valid_name' => 'nullable|integer',
            'rank' => 'nullable|integer',
            'rank_abbr' => 'nullable|string|max:255',
            'gsl_id' => 'nullable|integer',
            'bfn_namnr' => 'nullable|integer',
            'bfn_sipnr' => 'nullable|integer',
        ]);
        
        $data = [
            'parent_fk' => $request->input('parent'),
            'taxon_name' => $request->input('taxon_name'),
            'taxon_author' => $request->input('taxon_author'),
            'taxon_suppl' => $request->input('taxon_suppl'),
            'full_name' => $request->input('full_name'),
            'native_name' => $request->input('native_name'),
            'valid_name' => $request->input('valid_name'),
            //'rank' => $request->input('rank'),
            'rank_abbr' => $request->input('rank_abbr'),
            'gsl_id' => $request->input('gsl_id'),
            'bfn_namnr' => $request->input('bfn_namnr'),
            'bfn_sipnr' => $request->input('bfn_sipnr'),
        ];
        Taxon::create($data);
        
        return Redirect::to('admin/taxon')
            ->with('success', __('taxon.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Taxon  $taxon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Taxon $taxon, Request $request)
    {
        // Get current UI language
        $lang = app()->getLocale();
        $item_types = Localization::getItemTypes($lang);

        // Number of anchestor ranks is taken from request param or config file
        $anchestors = $taxon->ancestors()
            ->whereDepth('>=', $request->query('anchestors', config('ui.taxon_anchestors', 5)) * -1)
            ->orderBy('depth')
            ->get();

        return view('admin.taxon.show', compact('taxon', 'anchestors', 'item_types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Taxon  $taxon
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxon $taxon)
    {
        return view('admin.taxon.edit', compact('taxon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Taxon  $taxon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Taxon $taxon)
    {
        $request->validate([
            //'parent' => 'nullable|integer',
            'parent' =>
                // Check for circular dependency (parent must not be its own descendant)
                function ($attribute, $value, $fail) use ($taxon, $request) {
                    // Check for missing attributes, at least one (column) must be selected
                    if ($taxon->descendantsAndSelf()->get()->contains($request->input('parent'))) {
                        $fail(__('taxon.circular_parent'));
                    }
                },
            'taxon_name' => 'required|string|max:255',
            'taxon_author' => 'nullable|string|max:255',
            'taxon_suppl' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'valid_name' => 'nullable|integer',
            'rank' => 'nullable|integer',
            'rank_abbr' => 'nullable|string|max:255',
            'gsl_id' => 'nullable|integer',
            'bfn_namnr' => 'nullable|integer',
            'bfn_sipnr' => 'nullable|integer',
        ]);
        
        $taxon->parent_fk = $request->input('parent');
        $taxon->taxon_name = $request->input('taxon_name');
        $taxon->taxon_author = $request->input('taxon_author');
        $taxon->taxon_suppl = $request->input('taxon_suppl');
        $taxon->full_name = $request->input('full_name');
        $taxon->native_name = $request->input('native_name');
        $taxon->valid_name = $request->input('valid_name');
        //$taxon->rank = $request->input('rank');
        $taxon->rank_abbr = $request->input('rank_abbr');
        $taxon->gsl_id = $request->input('gsl_id');
        $taxon->bfn_namnr = $request->input('bfn_namnr');
        $taxon->bfn_sipnr = $request->input('bfn_sipnr');
        $taxon->save();
        
        return Redirect::to('admin/taxon')
            ->with('success', __('taxon.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Taxon  $taxon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxon $taxon)
    {
        // Check for column mappings owning this taxon
        if ($taxon->column_mapping()->count()) {
            return back()->with('warning', __('taxon.still_owned_by_cm'));
        }
        // Check for items owning this taxon
        if ($taxon->items()->count()) {
            return back()->with('warning', __('taxon.still_owned_by_it'));
        }

        $taxon->delete();
        $success_status_msg = " ". __('taxon.deleted');
        
        // Check for existing descendants of deleted taxon and fix their parents
        foreach (Taxon::where('parent_fk', $taxon->taxon_id)->get() as $descendant) {
            // Fix parent ID on descendant element
            $descendant->parent_fk = $taxon->parent_fk;
            $descendant->save();
            $success_status_msg .= " ".
                __('elements.hierarchy_fixed', ['id'=>$descendant->taxon_id]);
        }

        return back()->with('success', $success_status_msg);
    }

    /**
     * Get resource for AJAX autocompletion search field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        $search = $request->query('search');

        $results = Taxon::select('taxon_id', 'full_name', 'native_name')
            ->when($request->query('valid', false), function ($query) {
                return $query->whereNull('valid_name');
            })
            ->where(function ($query) use ($search) {
                $query->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('native_name', 'ILIKE', "%{$search}%");
            })
            ->orderBy('full_name')
            ->limit(config('ui.autocomplete_results', 5))
            ->get();
        
        $response = array();
        foreach ($results as $result) {
            $response[] = array(
                "value" => $result->taxon_id,
                "label" => $result->full_name ." (". $result->native_name .")",
                "edit_url" => route('taxon.edit', $result->taxon_id),
            );
        }
        
        return response()->json($response);
    }
}
