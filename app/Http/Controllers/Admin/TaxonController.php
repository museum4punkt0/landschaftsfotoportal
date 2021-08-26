<?php

namespace App\Http\Controllers\Admin;

use App\Taxon;
use App\Http\Controllers\Controller;
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxa = Taxon::tree()->depthFirst()->paginate(10);
        
        return view('admin.taxon.list', compact('taxa'));
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
            'taxon_name' => 'required|string',
            'taxon_author' => 'nullable|string',
            'taxon_suppl' => 'nullable|string',
            'full_name' => 'required|string',
            'native_name' => 'nullable|string',
            'valid_name' => 'nullable|integer',
            'rank' => 'nullable|integer',
            'rank_abbr' => 'nullable|string',
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
     * @return \Illuminate\Http\Response
     */
    public function show(Taxon $taxon)
    {
        //
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
            'taxon_name' => 'required|string',
            'taxon_author' => 'nullable|string',
            'taxon_suppl' => 'nullable|string',
            'full_name' => 'required|string',
            'native_name' => 'nullable|string',
            'valid_name' => 'nullable|integer',
            'rank' => 'nullable|integer',
            'rank_abbr' => 'nullable|string',
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
        
        return Redirect::to('admin/taxon')
            ->with('success', $success_status_msg);
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
