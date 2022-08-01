<?php

namespace App\Http\Controllers\Admin;

use App\Item;
use App\ItemRevision;
use App\Taxon;
use App\User;
use App\DateRange;
use App\Detail;
use App\Column;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
use App\ModuleInstance;
use App\Value;
use App\Http\Controllers\Controller;
use App\Notifications\ItemPublished;
use App\Utils\Localization;
use App\Utils\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Redirect;

class ItemController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\ItemPolicy for authorizing ressource controller
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aFilter = [
            'id' => $request->input('id'),
            'title' => $request->input('title'),
            'item_type' => $request->input('item_type'),
        ];
        $orderby = $request->input('orderby', 'item_id');
        $sort = $request->input('sort', 'desc');
        $limit = $request->input('limit', 10);

        $aWhere = [];
        if (!is_null($aFilter['id'])) {
            $aWhere[] = ['item_id', '=', $aFilter['id']];
        }
        if (!is_null($aFilter['title'])) {
            $aWhere[] = ['title', 'ilike', '%' . $aFilter['title'] . '%'];
        }
        if (!is_null($aFilter['item_type'])) {
            $aWhere[] = ['item_type_fk', '=', $aFilter['item_type']];
        }

        if (count($aWhere) > 0) {
            $items = Item::orderBy($orderby, $sort)
                    ->orWhere($aWhere)
                    ->paginate($limit)
                    ->withQueryString(); //append the get parameters
        }
        else {
            $items = Item::orderBy($orderby, $sort)->paginate($limit);
        }

        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::getByName('gallery');

        // Get current UI language
        $lang = app()->getLocale();

        $item_types = Localization::getItemTypes($lang);
        
        return view('admin.item.list', compact('items', 'item_types', 'image_module', 'aFilter'));
    }

    /**
     * Show the form to select the type of the new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        $this->authorize('create', Item::class);

        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();

        // Check for existing item_type, otherwise redirect back with warning message
        if ($item_types->isEmpty()) {
            return redirect()->route('list.internal')
                             ->with('warning', __('colmaps.no_item_type'));
        }

        return view('admin.item.new', compact('item_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $items = Item::tree()->depthFirst()->get();
        $taxon = Taxon::find($request->taxon);

        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($request->item_type, $request->taxon)->get();

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

        $options = ['edit.meta' => true, 'route' => 'item.store'];

        return view('admin.item.create', compact('items', 'taxon', 'colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions', 'options'));
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
        $colmap = ColumnMapping::forItem($item_type, $request->taxon)->get();

        // Validation rules for fields associated with this item
        $validation_rules['menu_title'] = 'nullable|string|max:255';
        $validation_rules['page_title'] = 'nullable|string|max:1024';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        $validation_rules['public'] = 'required|integer';
        $validation_rules['fields'] = 'required|array';

        // Validation rules for all fields associated with columns
        foreach ($request->input('fields') as $column_id => $value) {
            $required = $colmap->firstWhere('column_fk', $column_id)->getRequiredRule();
            $rule = Column::find($column_id)->getValidationRule();
            $validation_rules['fields.' . $column_id] = $required . $rule[0];

            // Special treatment for arrays
            if (sizeof($rule) > 1) {
                foreach ($rule[1] as $key => $value) {
                    $validation_rules['fields.' . $column_id . '.' . $key] = $required . $value;
                }
            }
        }

        #dd($validation_rules);
        $request->validate($validation_rules);

        // Save new item to database
        $item_data = [
            'menu_title' => $request->input('menu_title'),
            'page_title' => preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $request->input('page_title')),
            'parent_fk' => $request->input('parent'),
            'taxon_fk' => $request->input('taxon'),
            'public' => $request->input('public'),
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

                $file = $request->file('fields.' . $column_id . '.file');
                switch ($data_type) {
                    case '_image_':
                        if ($file->isValid()) {
                            $path = config('media.full_dir');
                            $name = $item->item_id . "_" . $column_id . "_" . date('YmdHis');
                            if (config('media.append_original_filename')) {
                                $name .= "_" . $file->getClientOriginalName();
                            }
                            else {
                                $name .= "." . $file->extension();
                            }

                            // Store on local 'public' disc
                            $file->storeAs($path, $name, 'public');
                            $detail_data['value_string'] = $name;

                            // Create resized images
                            Image::processImageResizing($path, $name);

                            // Store image dimensions in database
                            Image::storeImageDimensions($item, $path, $name, $column_id);
                            Image::storeImageSize($item, $path, $name, $column_id);
                        }
                        break;
                }
                Detail::where('item_fk', $item->item_id)
                        ->where('column_fk', $column_id)
                        ->update($detail_data);
            }
        }

        // Check for missing details from form input and add them
        // especially useful for drop-down lists with multiple selection if no option was selected
        $this->addMissingDetails($item);

        // Copy this updated item to revisions archive
        if (config('ui.revisions')) {
            $item->createRevisionWithDetails();
        }

        return Redirect::to('admin/item')
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
        $details = Detail::where('item_fk', $item->item_id)->get();

        // Load all revisions of the item
        $revisions = null;
        if (config('ui.revisions')) {
            $revisions = $item->revisions()->latest()->get();
        }

        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

        // Load all list elements of lists used by this item's columns
        $lists = Element::getTrees($colmap);

        // Get current UI language
        $lang = app()->getLocale();

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');

        return view('admin.item.show',
            compact('item', 'revisions', 'details', 'colmap', 'lists', 'translations'));
    }

    /**
     * Fill title column of items table from details table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function titles(Request $request)
    {
        $this->authorize('titles', Item::class);

        $query = Item::orderBy('item_id');
        if ($request->item_type) {
            $query = $query->where('item_type_fk', intval($request->item_type));
        }
        if (!$request->update) {
            $query = $query->whereNull('title');
        }
        $items = $query->get();
        #dd($items);

        $count = 0;
        $from_taxon = intval($request->taxon_schema) ? true : false;
        // Copy title string for all items if doesn't exist yet
        foreach ($items as $item) {
            if ($request->update || !$item->title) {
                $item->title = substr(
                    $item->getTitleColumn($from_taxon, intval($request->taxon_schema)), 0, 255);
                $item->save();
                $count++;
            }
        }

        return Redirect::to('admin/item')
                        ->with('success', __('items.titles_added', ['count' => $count]));
    }

    /**
     * Display a listing of non-public items for publishing.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_unpublished()
    {
        $this->authorize('publish', Item::class);

        $image_module = ModuleInstance::getByName('gallery');

        $items = Item::where('public', 0)->latest('updated_at')->paginate(10);

        return view('admin.item.publish', compact('items', 'image_module'));
    }

    /**
     * Publish a single or all non-public items.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function publish(Item $item)
    {
        $this->authorize('publish', $item);

        // Prevent one-click publishing if revisions are enabled
        if (config('ui.revisions')) {
            return redirect()->route('revision.index')
                ->with('warning', __('items.publish_not_available'));
        }

        // Check for single item or batch
        if ($item->item_id) {
            $items = [Item::find($item->item_id)];
        } else {
            $items = Item::where('public', 0)->orderBy('item_id')->get();
        }

        $count = 0;
        // Set public flag on all given items
        foreach ($items as $item) {
            $item->public = 1;
            $item->save();
            $count++;
        }

        return Redirect::to('admin/item/unpublished')
                        ->with('success', __('items.published', ['count' => $count]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        $items = Item::tree()->depthFirst()->get();
        // Remove all descendants to avoid circular dependencies
        $items = $items->diff($item->descendantsAndSelf()->get());

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

        $options = ['edit.meta' => true, 'route' => 'item.update'];

        return view('admin.item.edit', compact('item', 'items', 'taxon', 'details', 'colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions', 'options'));
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
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

        // Validation rules for fields associated with this item
        $validation_rules['menu_title'] = 'nullable|string|max:255';
        $validation_rules['page_title'] = 'nullable|string|max:1024';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        $validation_rules['public'] = 'required|integer';
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
            $validation_rules['fields.' . $column_id] = $required . $rule[0];

            // Special treatment for arrays
            if (sizeof($rule) > 1) {
                foreach ($rule[1] as $key => $value) {
                    $validation_rules['fields.' . $column_id . '.' . $key] = $required . $value;
                }
            }
        }

        $request->validate($validation_rules);

        $item->menu_title = $request->input('menu_title');
        $item->page_title = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $request->input('page_title'));
        $item->parent_fk = $request->input('parent');
        $item->taxon_fk = $request->input('taxon');
        $item->public = $request->input('public');
        $item->updated_by = $request->user()->id;
        $item->save();

        // Load all details for this item
        $details = $item->details;

        // Save the details for all columns that belong to the item
        foreach ($request->input('fields') as $column_id => $value) {
            $detail = $details->where('column_fk', $column_id)->first();

            $data_type = Column::find($column_id)->getDataType();

            switch ($data_type) {
                case '_list_':
                    $detail->element_fk = $value == '' ? null : intval($value);
                    break;
                case '_multi_list_':
                    $detail->elements()->sync(array_values(array_filter($value, function ($v, $k) {
                        return $k !== 'dummy';
                    }, ARRAY_FILTER_USE_BOTH)));
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
                case '_image_':
                    $valid = Image::checkFileExists(config('media.full_dir') . $value['filename']);
                    $detail->value_string = $valid ? $value['filename'] : null;
                    break;
            }
            $detail->save();
        }

        // Save uploaded files and their details
        if ($request->file('fields')) {
            foreach ($request->file('fields') as $column_id => $value) {
                $detail = $details->where('column_fk', $column_id)->first();

                $data_type = Column::find($column_id)->getDataType();

                $file = $request->file('fields.' . $column_id . '.file');
                switch ($data_type) {
                    case '_image_':
                        if ($file->isValid()) {
                            $path = config('media.full_dir');
                            $name = $item->item_id . "_" . $column_id . "_" . date('YmdHis');
                            if (config('media.append_original_filename')) {
                                $name .= "_" . $file->getClientOriginalName();
                            }
                            else {
                                $name .= "." . $file->extension();
                            }

                            // Store on local 'public' disc
                            $file->storeAs($path, $name, 'public');
                            $detail->value_string = $name;

                            // Create resized images
                            Image::processImageResizing($path, $name);

                            // Store image dimensions in database
                            Image::storeImageDimensions($item, $path, $name, $column_id);
                            Image::storeImageSize($item, $path, $name, $column_id);
                        }
                        break;
                }
                $detail->save();
            }
        }

        // Copy this updated item to revisions archive
        if (config('ui.revisions')) {
            $item->createRevisionWithDetails();

            // Delete all draft revisions
            if ($request->session()->has('delete_revisions_of_user')) {
                // Get creator of revision from session (and delete value from session)
                $revision_editor = $request->session()->pull('delete_revisions_of_user');
                $item->deleteAllDrafts($revision_editor);

                // Notify the owner of the item if item was published
                if ($item->public == 1) {
                    Notification::send(User::find($revision_editor), new ItemPublished($item));
                }

                return redirect()->route('revision.index')->with('success', __('items.updated'));
            }
        }

        return Redirect::to('admin/item')
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

        return Redirect::to('admin/item')
                        ->with('success', __('items.deleted'));
    }

    /**
     * Remove orphaned items which have no revisions.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeOrphans()
    {
        Gate::authorize('show-admin');

        if (config('ui.revisions')) {
            $items = Item::doesntHave('revisions')
                    ->orderBy('item_id', 'asc');
            //dd($items->get());
            $count = $items->delete();
        }

        return redirect()->route('item.index')
            ->with('success', __('items.orphans_removed', ['count' => $count]));
    }

    /**
     * Get resource for AJAX autocompletion search field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        // Get current UI language
        $lang = app()->getLocale();

        $results = Item::select('item_id', 'title', 'item_type_fk')
            ->where('title', 'ILIKE', "%{$request->search}%")
            ->orderBy('title')
            ->limit(config('ui.autocomplete_results', 5))
            ->get();
        
        $response = array();
        foreach ($results as $result) {
            $item_type = $result->item_type->attributes()->firstWhere('name', 'name_' . $lang);
            $response[] = array(
                "value" => $result->item_id,
                "label" => $result->title . " (" . $item_type->pivot->value . ")",
                "edit_url" => route('item.edit', $result->item_id),
            );
        }
        
        return response()->json($response);
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
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->get();

        // Check all columns for existing details
        foreach ($colmap as $cm) {
            $d = $item->details()->firstOrCreate([
                'column_fk' => $cm->column_fk,
            ]);
            // Logging for debug purpose
            if ($d->wasRecentlyCreated) {
                Log::info(__('items.added_missing_detail'), [
                    'item' => $d->item_fk, 'column' => $d->column_fk
                ]);
            }
        }
    }
}
