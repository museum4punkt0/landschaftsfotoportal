<?php

namespace App\Http\Controllers;

use App\Item;
use App\ItemRevision;
use App\Detail;
use App\DetailRevision;
use App\Column;
use App\ColumnMapping;
use App\DateRange;
use App\Selectlist;
use App\Element;
use App\Taxon;
use App\User;
use App\Notifications\ItemAdded;
use App\Notifications\ItemUpdated;
use App\Utils\Image;
use App\Utils\Localization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Debugbar;

class ItemController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified')->except(['show', 'download', 'gallery', 'map', 'timeline']);

        // Use app\Policies\ItemPolicy for authorizing ressource controller
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($request->item_type, $request->taxon)->where('public', 1);
        
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
        
        // Save item_type ID to session
        $request->session()->put('item_type', $request->item_type);
        // Save taxon ID to session
        $request->session()->put('taxon', $request->taxon);
        
        $options = ['edit.meta' => false, 'route' => 'item.store.own'];
        
        return view('item.create', compact('colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions', 'options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get item_type ID from session
        $item_type = $request->session()->get('item_type');
        
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item_type, $request->taxon)->where('public', 1);
        
        // Validation rules for fields associated with this item
        $validation_rules['title'] = 'nullable|string|max:255';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        #$validation_rules['public'] = 'required|integer';
        $validation_rules['fields'] = 'required|array';
        
        // Validation rules for all fields associated with columns
        foreach ($request->input('fields') as $column_id => $value) {
            $required = $colmap->firstWhere('column_fk', $column_id)->getRequiredRule();
            $rule = Column::find($column_id)->getValidationRule();
            $validation_rules['fields.'.$column_id] = $required . $rule[0];
            
            // Special treatment for arrays
            if (sizeof($rule) > 1) {
                foreach ($rule[1] as $key => $value) {
                    $validation_rules['fields.'.$column_id.'.'.$key] = $required . $value;
                }
            }
        }
        
        #dd($validation_rules);
        $request->validate($validation_rules);
        
        // Save new item to database
        $item_data = [
            'title' => $request->input('title'),
            'parent_fk' => $request->input('parent'),
            'taxon_fk' => $request->input('taxon'),
            'public' => 0,
            'item_type_fk' => $item_type,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        $item = Item::create($item_data);
        
        // Save the details for all columns that belong to the item
        foreach ($request->input('fields') as $column_id => $value) {
            $data_type = Column::find($column_id)->getDataType();
            
            $detail_elements = null;
            
            $detail_data = [
                'item_fk' => $item->item_id,
                'column_fk' => $column_id,
            ];
            switch ($data_type) {
                case '_list_':
                    $detail_data['element_fk'] = $value == '' ? null : intval($value);
                    break;
                case '_multi_list_':
                    $detail_elements = array_values(array_filter($value, function ($v, $k) {
                        return $k !== 'dummy';
                    }, ARRAY_FILTER_USE_BOTH));
                    break;
                case '_boolean_':
                case '_integer_':
                case '_image_ppi_':
                    $detail_data['value_int'] = $value == '' ? null : intval($value);
                    break;
                case '_float_':
                    $detail_data['value_float'] = $value == '' ? null : floatval($value);
                    break;
                case '_date_':
                    $detail_data['value_date'] = $value;
                    break;
                case '_date_range_':
                    $detail_data['value_daterange'] = new DateRange($value['start'], $value['end']);
                    break;
                case '_string_':
                case '_title_':
                case '_image_title_':
                case '_image_copyright_':
                case '_redirect_':
                case '_url_':
                case '_map_':
                case '_html_':
                    $detail_data['value_string'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $value);
                    break;
                case '_image_':
                    $detail_data['value_string'] = null;
                    break;
            }
            $detail = Detail::create($detail_data);
            
            // Save chosen elements for drop-down lists with multiple selections
            if ($detail_elements) {
                $detail->elements()->attach($detail_elements);
            }
        }
        
        // Save uploaded files and their details
        if ($request->file('fields')) {
            foreach ($request->file('fields') as $column_id => $value) {
                $data_type = Column::find($column_id)->getDataType();
                
                $detail_data = null;
                
                $file = $request->file('fields.'.$column_id.'.file');
                switch ($data_type) {
                    case '_image_':
                        if ($file->isValid()) {
                            $path = config('media.full_dir');
                            $name = $item->original_item_id ."_". $column_id ."_". date('YmdHis');
                            if (config('media.append_original_filename')) {
                                $name .= "_" . $file->getClientOriginalName();
                            }
                            else {
                                $name .= "." . $file->extension();
                            }

                            // Store on local 'public' disc
                            $file->storeAs($path, $name, 'public');
                            $detail_data['value_string']  = $name;
                            
                            // Store image dimensions in database
                            Image::storeImageDimensions($item, $path, $name, $column_id);
                            Image::storeImageSize($item, $path, $name, $column_id);
                            
                            // Create resized images
                            Image::processImageResizing($path, $name);
                        }
                        break;
                }
                $item->details()
                    ->where('column_fk', $column_id)
                    ->update($detail_data);
            }
        }
        
        // Check for missing details from form input and add them
        // especially useful for drop-down lists with multiple selection if no option was selected
        $this->addMissingDetails($item);
        
        // Copy this updated item to revisions archive
        if (config('ui.revisions')) {
            $item->createRevisionWithDetails(Auth::user()->isModerated());

            // Notify all users with moderation privileges
            if (Auth::user()->isModerated()) {
                Notification::send(User::moderators()->get(), new ItemAdded($item));
            }
        }
        else {
            Notification::send(User::moderators()->get(), new ItemAdded($item));
        }

        return redirect()->route('item.show.own')
            ->with('success', __('items.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        // Check for redirects
        $target = $item->getDetailWhereDataType('_redirect_');
        if ($target && $target != __('items.no_detail_with_data_type')) {
            return Redirect::to($target);
        }
        
        // All items for the show blade, used for image galleries
        $items = Item::find($item->item_id)
            ->descendantsAndSelf()
            ->where('public', 1)
            ->orderBy('title')
            ->get();
        
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->where('public', 1)->orderBy('item_id')->get();
        
        // Get the menu path of the requested item
        $ancestors = Item::find($item->item_id)->ancestorsAndSelf()->orderBy('depth', 'asc')->first();
        $path = array_reverse(explode('.', $ancestors->path));
        
        // Details of selected item
        $details = Detail::where('item_fk', $item->item_id)->get();
        
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->where('public', 1);
        
        // Load all list elements of lists used by this item's columns
        $lists = Element::getTrees($colmap);
        
        // Get current UI language
        $lang = app()->getLocale();
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        return view('item.show', compact('item', 'items', 'details', 'menu_root', 'path', 'colmap', 'lists', 'translations'));
    }

    /**
     * Display items owned by the current authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function own()
    {
        $this->authorize('viewOwn', Item::class);

        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_type = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })
            ->first()->element_id;
        
        $items = Item::myOwn(Auth::user()->id)->with('details')
            ->where('item_type_fk', $item_type)
            ->latest()
            ->paginate(config('ui.cart_items'));
        
        return view('item.own', compact('items', 'item_type'));
    }

    /**
     * Forces the user's browser to download the image belonging to this item.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function download(Item $item)
    {
        $filename = $item->details->firstWhere('column_fk', 13)->value_string;
        $pathToFile = 'public/'. config('media.full_dir') . $filename;
        
        if (Storage::missing($pathToFile)) {
            abort(404);
        }
        
        return Storage::download($pathToFile);
    }

    /**
     * Display the image gallery containing latest + random items.
     *
     * @return \Illuminate\Http\Response
     */
    public function gallery()
    {
        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_type = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })
            ->first()->element_id;
        
        $items['latest'] = Item::with('details')
            ->where('public', 1)
            ->where('item_type_fk', $item_type)
            ->latest()
            ->take(config('ui.gallery_items'))
            ->get();
        $items['random'] = Item::with('details')
            ->where('public', 1)
            ->where('item_type_fk', $item_type)
            ->inRandomOrder()
            ->take(config('ui.gallery_items'))
            ->get();
        $items['incomplete'] = Item::with('details')
            ->where('public', 1)
            ->where('item_type_fk', $item_type)
            ->whereHas('details', function (Builder $query) {
                // Details with missing location/city value
                $query->where('column_fk', 22)
                    ->where('value_string', '');
            })
            ->inRandomOrder()
            ->take(config('ui.gallery_items'))
            ->get();
        
        return view('item.gallery', compact('items'));
    }

    /**
     * Display the timeline for all items.
     *
     * @return \Illuminate\Http\Response
     */
    public function timeline()
    {
        // TODO: remove hard-coded daterange column
        $daterange_column = 27;
        
        // Get bounds for daterange
        $bounds = Detail::selectRaw("
                EXTRACT(DECADE FROM MIN(LOWER(value_daterange)))*10 AS lower,
                EXTRACT(DECADE FROM MAX(UPPER(value_daterange)))*10 AS upper
            ")
            ->whereNotNull('value_daterange')
            ->whereHas('item', function (Builder $query) {
                $query->where('public', 1);
            })
            ->where('column_fk', $daterange_column) 
            ->first();
        
        // For each decade...
        for ($decade = $bounds->lower; $decade <= $bounds->upper; $decade += 10) {
            $daterange = '['. date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade)) .','.
                date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade + 10)) .')';
            
            // Get number of items per decade
            $decades[$decade] = Detail::
                whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
                })
                ->where('column_fk', $daterange_column)
                ->whereRaw("value_daterange && '$daterange'")
                ->count();
            
            // Get some random items per decade, to be shown as examples
            $details[$decade] = Detail::with('item')
                ->whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
                })
                ->where('column_fk', $daterange_column)
                ->whereRaw("value_daterange && '$daterange'")
                ->inRandomOrder()
                ->take(config('ui.timeline_items'))
                ->get();
        }
        
        // For items without any date (value_daterange = null)
        // Get number of items w/o date
        $decades[-1] = Detail::
            whereHas('item', function (Builder $query) {
                $query->where('public', 1);
            })
            ->where('column_fk', $daterange_column)
            ->where('value_daterange', null)
            ->count();
        
        // Get some random items w/o date, to be shown as examples
        $details[-1] = Detail::with('item')
            ->whereHas('item', function (Builder $query) {
                $query->where('public', 1);
            })
            ->where('column_fk', $daterange_column)
            ->where('value_daterange', null)
            ->inRandomOrder()
            ->take(config('ui.timeline_items'))
            ->get();
        
        return view('item.timeline', compact('decades', 'details'));
    }

    /**
     * Display a map showing all items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function map(Request $request)
    {
        $column_ids['lon'] = Column::ofDataType('_float_')->ofItemType('_image_')->ofSubType('location_lon')
            ->first()->column_id;
        $column_ids['lat'] = Column::ofDataType('_float_')->ofItemType('_image_')->ofSubType('location_lat')
            ->first()->column_id;
        Debugbar::debug($column_ids);
        
        // There are different URLs for AJAX requests to get the items to be displayed on the map
        if ($request->query('source') == 'search') {
            $options = [
                'ajax_url' => route('map.search'),
                'search_url' => route('search.index', $request->query()),
            ];
        }
        else {
            $options = [
                'ajax_url' => route('map.all'),
                'search_url' => route('search.index', ['source' => 'all']),
            ];
        }
        
        return view('item.map', compact('column_ids', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        // Check for moderation: changes must be approved by an user with appropriate permissions
        $moderated = Auth::user()->isModerated();
        if (config('ui.revisions') && $moderated) {
            $revision = $item->moderatedRevisionAvailable();
            // Check if a draft revision is available
            if ($revision) {
                Debugbar::info('rev: ' . $revision);
                // Load the (draft) revision instead of the original item
                $item = $item->revisions()->myOwn(Auth::user()->id)
                        ->where('revision', $revision)->first();
                // Put some info for the users into the session
                session()->flash('info', __('items.editing_draft'));
            }
        }

        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->where('public', 1);
        
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
        
        $options = ['edit.meta' => false, 'route' => 'item.update.own'];
        
        return view('item.edit', compact('item', 'details', 'colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions', 'options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->where('public', 1);
        
        // Validation rules for fields associated with this item
        $validation_rules['title'] = 'nullable|string|max:255';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        #$validation_rules['public'] = 'required|integer';
        $validation_rules['fields'] = 'required|array';
        
        // Validation rules for all fields associated with columns
        foreach ($request->input('fields') as $column_id => $value) {
            // Uploading a new image is never required on updating items
            if (Column::find($column_id)->getDataType() == '_image_') {
                $required = 'nullable|';
            }
            else {
                $required = $colmap->firstWhere('column_fk', $column_id)->getRequiredRule();
            }
            $rule = Column::find($column_id)->getValidationRule();
            $validation_rules['fields.'.$column_id] = $required . $rule[0];
            
            // Special treatment for arrays
            if (sizeof($rule) > 1) {
                foreach ($rule[1] as $key => $value) {
                    $validation_rules['fields.'.$column_id.'.'.$key] = $required . $value;
                }
            }
        }
        #dd($validation_rules);
        
        $request->validate($validation_rules);

        // Check for moderation: changes must be approved by an user with appropriate permissions
        $moderated = Auth::user()->isModerated();
        if (config('ui.revisions') && $moderated) {
            $revision = $item->moderatedRevisionAvailable();
            // Check if a draft revision is available
            if (!$revision) {
                $revision = $item->getLatestRevisionNumber(false);
            }
            // Load the (draft) revision instead of the original item
            $item = $item->revisions()->myOwn(Auth::user()->id)
                    ->where('revision', $revision)->first();
            // Copy that revision to a new one
            $item = $item->cloneRevisionWithDetails($moderated);
            // Check for missing revision details and add them
            $this->addMissingDetails($item);
            // Reload the item and eager load all of its details
            $item->refresh();
            $item->load('details');
        }

        $item->title = $request->input('title');
        $item->parent_fk = $request->input('parent');
        $item->taxon_fk = $request->input('taxon');
        $item->public = 0;
        $item->updated_by = $request->user()->id;
        $item->save();

        // Load all details for this item
        $details = $item->details;

        // Save the details for all columns that belong to the item
        foreach ($request->input('fields') as $column_id => $value) {
            $detail = $details->where('column_fk', $column_id)->first();
            $detail_elements = null;
            
            $data_type = Column::find($column_id)->getDataType();
            
            switch ($data_type) {
                case '_list_':
                    $detail->element_fk = $value == '' ? null : intval($value);
                    break;
                case '_multi_list_':
                    $detail_elements = array_values(array_filter($value, function ($v, $k) {
                        return $k !== 'dummy';
                    }, ARRAY_FILTER_USE_BOTH));
                    break;
                case '_boolean_':
                case '_integer_':
                case '_image_ppi_':
                    $detail->value_int = $value == '' ? null : intval($value);
                    break;
                case '_float_':
                    $detail->value_float = $value == '' ? null : floatval($value);
                    break;
                case '_date_':
                    $detail->value_date = $value;
                    break;
                case '_date_range_':
                    $detail->value_daterange = new DateRange($value['start'], $value['end']);
                    break;
                case '_string_':
                case '_title_':
                case '_image_title_':
                case '_image_copyright_':
                case '_redirect_':
                case '_url_':
                case '_map_':
                case '_html_':
                    $detail->value_string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $value);
                    break;
            }
            $detail->save();

            // Save chosen elements for drop-down lists with multiple selections
            if ($detail_elements) {
                $detail->elements()->sync($detail_elements);
            }
        }
        
        // Save uploaded files and their details
        if ($request->file('fields')) {
            foreach ($request->file('fields') as $column_id => $value) {
                $detail = $details->where('column_fk', $column_id)->first();
                
                $data_type = Column::find($column_id)->getDataType();
                
                $file = $request->file('fields.'.$column_id.'.file');
                switch ($data_type) {
                    case '_image_':
                        if ($file->isValid()) {
                            $path = config('media.full_dir');
                            $name = $item->original_item_id ."_". $column_id ."_". date('YmdHis');
                            if (config('media.append_original_filename')) {
                                $name .= "_" . $file->getClientOriginalName();
                            }
                            else {
                                $name .= "." . $file->extension();
                            }

                            // Store on local 'public' disc
                            $file->storeAs($path, $name, 'public');
                            $detail->value_string  = $name;
                            
                            // Store image dimensions in database
                            Image::storeImageDimensions($item, $path, $name, $column_id);
                            Image::storeImageSize($item, $path, $name, $column_id);
                            
                            // Create resized images
                            Image::processImageResizing($path, $name);
                        }
                        break;
                }
                $detail->save();
            }
        }

        if (config('ui.revisions')) {
            if ($moderated) {
                // Notify all users with moderation privileges
                Notification::send(User::moderators()->get(), new ItemUpdated($item));
            }
            else {
                // Copy this updated item to revisions archive
                $item->createRevisionWithDetails(false);
            }
        }
        else {
            Notification::send(User::moderators()->get(), new ItemUpdated($item));
        }

        return redirect()->route('item.show.own')
            ->with('success', __('items.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        // Delete this item from all carts
        $item->carts()->delete();

        // Delete all comments owned by this item
        $item->comments()->delete();

        // Delete all details owned by this item
        $item->details()->delete();

        // Delete the item itself
        $item->delete();

        return redirect()->route('item.show.own')
                        ->with('success', __('items.deleted'));
    }

    /**
     * Remove all draft revisions of the specified resource from storage.
     *
     * @param  \App\ItemRevision  $itemRevision
     * @return \Illuminate\Http\Response
     */
    public function destroyDraft(Item $item)
    {
        $this->authorize('deleteDraft', $item);

        if (config('ui.revisions')) {
            $item->deleteAllDrafts(Auth::user()->id);
        }

        return redirect()->route('item.show.own')
                         ->with('success', __('revisions.deleted'));
    }

    /**
     * Check for missing details and add them to database.
     *
     * @param  \App\Item  $item
     * @return void
     */
    private function addMissingDetails(Item $item)
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
                if ($d instanceof DetailRevision) {
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
                // $d is instanceof Detail
                else {
                    Log::info(__('items.added_missing_detail'), [
                        'item' => $d->item_fk, 'column' => $d->column_fk
                    ]);
                }
            }
        }
    }
}
