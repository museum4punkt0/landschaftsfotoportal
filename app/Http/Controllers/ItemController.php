<?php

namespace App\Http\Controllers;

use App\Item;
use App\Detail;
use App\Column;
use App\ColumnMapping;
use App\DateRange;
use App\Selectlist;
use App\Element;
use App\Taxon;
use App\Utils\Image;
use App\Utils\Localization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $validation_rules['title'] = 'nullable|string';
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
                            $name = $item->item_id ."_". $column_id ."_". date('YmdHis');
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
                            Image::storeImageDimensions($path, $name, $item->item_id, $column_id);
                            Image::storeImageSize($path, $name, $item->item_id, $column_id);
                            
                            // Create resized images
                            Image::processImageResizing($path, $name);
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
        $this->addMissingDetails($item, $colmap);
        
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
        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_type = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })
            ->first()->element_id;
        
        $items = Item::myOwn(Auth::user()->id)->with('details')
            ->where('item_type_fk', $item_type)->latest()->paginate(12);
        
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
            ->where('public', 1)->where('item_type_fk', $item_type)->latest()->take(3)->get();
        $items['random'] = Item::with('details')
            ->where('public', 1)->where('item_type_fk', $item_type)->inRandomOrder()->take(3)->get();
        $items['incomplete'] = Item::with('details')
            ->where('public', 1)
            ->where('item_type_fk', $item_type)
            ->whereHas('details', function (Builder $query) {
                // Details with missing location/city value
                $query->where('column_fk', 22)
                    ->where('value_string', '');
            })
            ->inRandomOrder()
            ->take(3)
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
        // Only columns associated with this item's taxon or its descendants
        $colmap = ColumnMapping::forItem($item->item_type_fk, $item->taxon_fk)->where('public', 1);
        
        // Check for missing details and add them
        // Should be not necessary but allows editing items with somehow incomplete data
        $this->addMissingDetails($item, $colmap);
        
        // Load all details for this item
        $details = Detail::where('item_fk', $item->item_id)->get();
        
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
        $validation_rules['title'] = 'nullable|string';
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
        
        $item->title = $request->input('title');
        $item->parent_fk = $request->input('parent');
        $item->taxon_fk = $request->input('taxon');
        $item->public = 0;
        $item->updated_by = $request->user()->id;
        $item->save();
        
        $details = Detail::where('item_fk', $item->item_id)->get();
        
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
            }
            $detail->save();
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
                            $name = $item->item_id ."_". $column_id ."_". date('YmdHis');
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
                            Image::storeImageDimensions($path, $name, $item->item_id, $column_id);
                            Image::storeImageSize($path, $name, $item->item_id, $column_id);
                            
                            // Create resized images
                            Image::processImageResizing($path, $name);
                        }
                        break;
                }
                $detail->save();
            }
        }
        
        return redirect()->route('item.show.own')
            ->with('success', __('items.updated'));
    }

    /**
     * Check for missing details and add them to database.
     *
     * @param  \App\Item  $item
     * @param  \Illuminate\Database\Eloquent\Collection  $colmap
     * @return void
     */
    private function addMissingDetails(Item $item, $colmap)
    {
        // Check all columns for existing details
        foreach ($colmap as $cm) {
            Detail::firstOrCreate([
                'item_fk' => $item->item_id,
                'column_fk' => $cm->column->column_id,
            ]);
        }

        // TODO: logging for debug purpose
    }
}
