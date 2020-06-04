<?php

namespace App\Http\Controllers\Admin;

use App\Taxon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class TaxonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxa = Taxon::tree()->paginate(10);
        
        return view('admin.taxon.list', compact('taxa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $taxa = Taxon::tree()->get();
        
        return view('admin.taxon.create', compact('taxa'));
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
            'parent_fk' => 'nullable|integer',
            'taxon_name' => 'required|string',
            'taxon_author' => 'nullable|string',
            'native_name' => 'required|string',
            'valid_name' => 'nullable|integer',
            'rank' => 'integer',
        ]);
        
        $data = [
            'parent_fk' => $request->input('parent'),
            'taxon_name' => $request->input('taxon_name'),
            'taxon_author' => $request->input('taxon_author'),
            'native_name' => $request->input('native_name'),
            'valid_name' => $request->input('valid_name'),
            //'rank' => $request->input('rank'),
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
        $taxa = Taxon::tree()->get();
        
        // Remove all descendants to avoid circular dependencies
        $taxa = $taxa->diff($taxon->descendantsAndSelf()->get());
        
        return view('admin.taxon.edit', compact('taxon', 'taxa'));
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
            'parent_fk' => 'nullable|integer',
            'taxon_name' => 'required|string',
            'taxon_author' => 'nullable|string',
            'native_name' => 'required|string',
            'valid_name' => 'nullable|integer',
            'rank' => 'integer',
        ]);
        
        $taxon->parent_fk = $request->input('parent');
        $taxon->taxon_name = $request->input('taxon_name');
        $taxon->taxon_author = $request->input('taxon_author');
        $taxon->native_name = $request->input('native_name');
        $taxon->valid_name = $request->input('valid_name');
        //$taxon->rank = $request->input('rank');
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
        foreach(Taxon::where('parent_fk', $taxon->taxon_id)->get() as $descendant) {
            // Fix parent ID on descendant element
            $descendant->parent_fk = $taxon->parent_fk;
            $descendant->save();
            $success_status_msg .= " ".
                __('elements.hierarchy_fixed', ['id'=>$descendant->taxon_id]);
        }
        
        return Redirect::to('admin/taxon')
            ->with('success', $success_status_msg);
    }
}
