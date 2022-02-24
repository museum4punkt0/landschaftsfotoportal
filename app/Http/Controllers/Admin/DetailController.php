<?php

namespace App\Http\Controllers\Admin;

use App\Detail;
use App\DetailRevision;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DetailController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\DetailPolicy for authorizing ressource controller
        $this->authorizeResource(Detail::class, 'detail');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Detail  $detail
     * @return \Illuminate\Http\Response
     */
    public function show(Detail $detail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Detail  $detail
     * @return \Illuminate\Http\Response
     */
    public function edit(Detail $detail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Detail  $detail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Detail $detail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Detail  $detail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Detail $detail)
    {
        //
    }

    /**
     * Remove details from storage that don't belong to any item.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeOrphans()
    {
        // TODO: move this to app/Utils/Admin-whatever because this controller is quite useless
        Gate::authorize('show-admin');

        $count = Detail::doesntHave('item')->delete();
        if (config('ui.revisions')) {
            $count += DetailRevision::doesntHave('item')->delete();
        }
        
        return redirect()->route('item.index')
            ->with('success', __('items.orphans_removed', ['count' => $count]));
    }
}
