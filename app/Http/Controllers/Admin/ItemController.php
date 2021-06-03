<?php

namespace App\Http\Controllers\Admin;

use App\Item;
use App\Taxon;
use App\Cart;
use App\Comment;
use App\DateRange;
use App\Detail;
use App\Column;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
use App\Http\Controllers\Controller;
use App\Utils\Localization;
use App\Utils\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
    public function index()
    {
        $items = Item::orderBy('item_id', 'desc')->paginate(10);
        
        return view('admin.item.list', compact('items'));
    }

    /**
     * Show the form to select the type of the new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        $this->authorize('new', Item::class);
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        // Check for existing item_type, otherwise redirect back with warning message
        if ($item_types->isEmpty()) {
            return redirect()->route('list.internal')
                ->with('warning', __('colmaps.no_item_type'));
        }
        
        $taxa = Taxon::tree()->depthFirst()->get();
        
        return view('admin.item.new', compact('item_types', 'taxa'));
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
        $taxa = Taxon::tree()->depthFirst()->get();
        
        // Only columns associated with this item's taxon or its descendants
        $taxon_id = $request->taxon;
        $colmap = ColumnMapping::where('item_type_fk', $request->item_type)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->orderBy('column_order')->get();
        
        $lists = null;
        // Load all list elements of lists used by this item's columns
        foreach ($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if ($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', null)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
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
        
        return view('admin.item.create', compact('items', 'taxa', 'colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions'));
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
        $taxon_id = $request->taxon;
        $colmap = ColumnMapping::where('item_type_fk', $item_type)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->with('column')
            ->get();
        
        // Validation rules for fields associated with this item
        $validation_rules['title'] = 'nullable|string';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        $validation_rules['public'] = 'required|integer';
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
                    $detail_elements = array_values($value);
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
                            $name = $item->item_id ."_". $column_id ."_". date('YmdHis') ."_";
                            $name .= $file->getClientOriginalName();
                            
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
        
        // Only columns associated with this item's taxon or its descendants
        $taxon_id = $item->taxon_fk;
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->orderBy('column_order')->get();
        
        $lists = null;
        // Load all list elements of lists used by this item's columns
        foreach ($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if ($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', null)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
        // Get current UI language
        $lang = app()->getLocale();
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        return view('admin.item.show', compact('item', 'details', 'colmap', 'lists', 'translations'));
    }

    /**
     * Fill title column of items table from details table.
     *
     * @return \Illuminate\Http\Response
     */
    public function titles()
    {
        $this->authorize('titles', Item::class);
        
        $items = Item::orderBy('item_id')->get();
        
        $count = 0;
        // Copy title string for all items if doesn't exist yet
        foreach ($items as $item) {
            if (!$item->title) {
                $item->title = $item->getTitleColumn();
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
        $this->authorize('unpublished', Item::class);
        
        $items = Item::where('public', 0)->latest('updated_at')->paginate(10);
        
        return view('admin.item.publish', compact('items'));
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
        
        $taxa = Taxon::tree()->depthFirst()->get();
        
        // Only columns associated with this item's taxon or its descendants
        $taxon_id = $item->taxon_fk;
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->with('column')
            ->orderBy('column_order')->get();
        
        // Check for missing details and add them
        // Should be not necessary but allows editing items with somehow incomplete data
        $this->addMissingDetails($item, $colmap);
        
        // Load all details for this item
        $details = Detail::where('item_fk', $item->item_id)->get();
        
        $lists = null;
        // Load all list elements of lists used by this item's columns
        foreach ($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if ($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', null)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
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
        
        return view('admin.item.edit', compact('item', 'items', 'taxa', 'details', 'colmap', 'lists', 'data_types', 'translations', 'placeholders', 'descriptions'));
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
        $taxon_id = $item->taxon_fk;
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->get();
        
        // Validation rules for fields associated with this item
        $validation_rules['title'] = 'nullable|string';
        $validation_rules['parent'] = 'nullable|integer';
        $validation_rules['taxon'] = 'nullable|integer';
        $validation_rules['public'] = 'required|integer';
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
        
        $request->validate($validation_rules);
        
        $item->title = $request->input('title');
        $item->parent_fk = $request->input('parent');
        $item->taxon_fk = $request->input('taxon');
        $item->public = $request->input('public');
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
                    $detail->elements()->sync(array_values($value));
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
                            $name = $item->item_id ."_". $column_id ."_". date('YmdHis') ."_";
                            $name .= $file->getClientOriginalName();
                            
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
        Cart::where('item_fk', $item->item_id)->delete();
        
        // Delete all comments owned by this item
        Comment::where('item_fk', $item->item_id)->delete();
        
        // Delete all details owned by this item
        Detail::where('item_fk', $item->item_id)->delete();
        
        // Delete the item itself
        $item->delete();
        
        return Redirect::to('admin/item')
            ->with('success', __('items.deleted'));
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
