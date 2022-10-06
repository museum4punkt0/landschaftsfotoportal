<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\ItemRevision;
use App\Notifications\ItemRejected;
use App\Utils\Localization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ItemRevisionController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\ItemRevisionPolicy for authorizing ressource controller
        $this->authorizeResource(ItemRevision::class, 'revision');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = ItemRevision::where('revision', '<', 0)
                            ->whereHas('item')
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
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

        // Load all list elements of lists used by this item's columns
        $lists = Element::getTrees($colmap);

        // Get current UI language
        $lang = app()->getLocale();

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);

        return view('admin.revision.show',
            compact('item', 'revisions', 'details', 'colmap','lists', 'item_types', 'translations'));
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
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

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

        // Save editor of this revision to session
        session(['delete_revisions_of_user' => $item->updated_by]);

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
            return back()->with('warning', __('revisions.cannot_delete_current'));
        }
        // Don't delete if this revision is the only one which exists
        elseif ($revision->item->revisions->count() == 1) {
            return back()->with('warning', __('revisions.cannot_delete_current'));
        }
        else {
            $revision->deleteRevisionWithDetails();

            return back()->with('success', __('revisions.deleted'));
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
        $this->authorize('deleteDraft', $revision);

        $revision->item->deleteAllDrafts();

        // Check for other revisions of the item owning this revision
        if ($revision->item->revisions->count() == 0) {
            // No other revisions found, that means: the deleted was the only one
            Log::info(__('items.removed_after_deleting_drafts'), [
                'item' => $revision->item_fk,
            ]);
            // Let's delete the remaining orphaned stuff
            // TODO: move this to the model, affects (User)ItemController, too!
            $revision->item->carts()->delete();
            $revision->item->comments()->delete();
            $revision->item->details()->delete();
            $revision->item->delete();
        }

        // Notify the editor of the given revision
        Notification::send($revision->editor, new ItemRejected($revision));

        return redirect()->route('revision.index')
                         ->with('success', __('revisions.deleted'));
    }

    /**
     * Display a listing of revisions belonging to deleted items.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted()
    {
        $this->authorize('viewDeleted');

        $items = ItemRevision::doesntHave('item')
                            ->distinct('item_fk')
                            ->orderBy('item_fk', 'asc')
                            ->orderBy('updated_at', 'desc')
                            ->paginate(10);

        return view('admin.revision.deleted', compact('items'));
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
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

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
