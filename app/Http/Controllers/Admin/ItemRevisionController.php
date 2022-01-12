<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\ItemRevision;
use App\Utils\Localization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemRevisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: implement policy for ItemRevision
        $this->authorize('publish', Item::class);

        $items = ItemRevision::where('revision', '<', 0)
                            ->distinct('item_fk')
                            ->orderBy('item_fk', 'asc')
                            ->orderBy('updated_at', 'desc')
                            ->paginate(10);

        return view('admin.revision.list', compact('items'));
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
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function show(ItemRevision $revision)
    {
        $item = $revision;
        $details = $item->details;

        // Load all revisions of the item
        $revisions = $item->item->revisions()->latest()->get();

        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk);

        // Load all list elements of lists used by this item's columns
        $lists = Element::getTrees($colmap);

        // Get current UI language
        $lang = app()->getLocale();

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');

        return view('admin.revision.show',
            compact('item', 'revisions', 'details', 'colmap','lists', 'translations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemRevision $revision)
    {
        $item = $revision;
        $taxon = $item->taxon;

        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk);

        // Check for missing details and add them
        // Should be not necessary but allows editing items with somehow incomplete data
        $this->addMissingDetails($item);

        // Load all details for this item
        $details = $item->details;

        // Load all list elements of lists used by this item's columns
        $lists = Element::getTrees($colmap);

        // Get current UI language
        $lang = app()->getLocale();

        // Get data types of columns with localized names
        $data_types = Localization::getDataTypes($lang);

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        // Get localized placeholders for columns
        $placeholders = Localization::getTranslations($lang, 'placeholder');
        // Get localized description/help for columns
        $descriptions = Localization::getTranslations($lang, 'description');

        $options = ['edit.meta' => true, 'edit.revision' => true, 'route' => 'item.update'];

        return view('admin.item.edit', compact('item', 'taxon', 'details', 'colmap', 'lists',
            'data_types', 'translations', 'placeholders', 'descriptions', 'options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemRevision $itemRevision)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemRevision $revision)
    {
        // Get the most current revision which is not a draft
        $latest = ItemRevision::where('revision', '>', 0)
            ->where('item_fk', $revision->item_fk)
            ->latest()
            ->first();

        // Don't delete the most current revision which is not a draft
        if ($revision->revision == $latest->revision) {
            return redirect()->route('revision.index')
                             ->with('warning', __('revisions.cannot_delete_current'));
        }
        // Don't delete if this revision is the only one which exists
        elseif ($revision->item->revisions->count() == 1) {
            return redirect()->route('revision.index')
                             ->with('warning', __('revisions.cannot_delete_current'));
        }
        else {
            $revision->deleteRevisionWithDetails();

            return redirect()->route('revision.index')
                             ->with('success', __('revisions.deleted'));
        }
    }

    /**
     * Remove all draft revisions of the specified resource from storage.
     *
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function destroyDraft(ItemRevision $revision)
    {
        $revision->item->deleteAllDrafts();

        return redirect()->route('revision.index')
                         ->with('success', __('revisions.deleted'));
    }

    /**
     * Check for missing details and add them to database.
     *
     * @param  \App\ItemRevision  $item
     * @return void
     */
    private function addMissingDetails(ItemRevision $item)
    {
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk);

        // Check all columns for existing details
        foreach ($colmap as $cm) {
            $d = $item->details()->firstOrCreate([
                'column_fk' => $cm->column_fk,
            ]);
            // Add foreign keys and write to log file
            if ($d->wasRecentlyCreated) {
                $d->item_fk = $item->item_fk;
                $d->detail_fk = $item->item->details()->firstOrCreate([
                    'column_fk' => $cm->column_fk,
                ])->detail_id;
                $d->save();

                Log::info(__('items.added_missing_detail'), [
                    'item' => $d->item_fk, 'column' => $d->column_fk,
                    'item_rev' => $item->item_revision_id, 'rev' => $item->revision
                ]);
            }
        }
    }
}
