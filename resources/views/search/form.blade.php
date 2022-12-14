@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('sidebar_menu_items')
    @parent
    
    @includeWhen(config('menu.sidebar_max_levels'), 'includes.item_submenu', [
        'sub' => $menu_root,
        'path' => $path,
        'order' => config('menu.sidebar_item_order', []),
        'exclude' => config('menu.sidebar_exclude_item_type', []),
    ])
    
    @if(config('menu.sidebar_max_levels'))
    <script type="text/javascript">
        $(document).ready(function () {
            // Init the menu
            menu.init("{{ route('menu.children') }}");
        });
    </script>
    @endif
@endsection

@section('content')

@includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
    'section_id' => 'search',
    'section_heading' => __('search.header'),
    'section_subheading' => __(config('ui.frontend_layout') . '.search_subheading'),
])

<div class="container">
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    
    <div class="container">
        <div class="row">
            <!-- Search form -->
            <div class="col-lg-2" id="searchForm">
                
                <form action="{{ route('search.index') }}#searchResults" method="GET">
                @if(Config::get('ui.frontend_layout') == 'bestikri')
                    <!-- Input for taxon search -->
                    <div class="form-group">
                        <span>@lang('search.taxon_name')</span>
                        <input type="text" name="taxon_name" class="form-control" value="{{$search_terms['taxon_name'] ?? ""}}" />
                        <span class="text-danger">{{ $errors->first('taxon_name') }}</span>
                    </div>
                @endif
                    <!-- Input for full text search -->
                    <div class="form-group">
                        <span>@lang('search.full_text')</span>
                        <input type="text" name="full_text" class="form-control" value="{{$search_terms['full_text'] ?? ""}}" />
                        <span class="text-danger">{{ $errors->first('full_text') }}</span>
                    </div>

                    <div class="container px-0 px-lg-0">
                        <div class="row mx-n3 mx-lg-0">
                        @foreach($colmap as $cm)

                            <!-- Dropdown menus for latitude / longitude -->
                            @if($cm->getConfigValue('data_subtype') == 'location_lat' ||
                                $cm->getConfigValue('data_subtype') == 'location_lon')
                                <!-- Force next columns to break to new line -->
                                <div class="w-100"></div>
                                <div class="form-group col-12 px-3 px-lg-0">
                                    <span>
                                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                                    </span>
                                    <div class="row">
                                        <div class="col col-6 col-lg-12">
                                            <input
                                                type="text"
                                                name="fields[{{ $cm->column->column_id }}][min]"
                                                class="form-control"
                                                placeholder="min"
                                                value="{{$search_terms['fields'][$cm->column->column_id]['min'] ?? ""}}"
                                            />
                                        </div>
                                        <div class="col col-6 col-lg-12">
                                            <input
                                                type="text"
                                                name="fields[{{ $cm->column->column_id }}][max]"
                                                class="form-control"
                                                placeholder="max"
                                                value="{{$search_terms['fields'][$cm->column->column_id]['max'] ?? ""}}"
                                            />
                                        </div>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                                </div>
                            @endif

                            <!-- Dropdown menus for select lists -->
                            @if($cm->column->data_type_name == '_list_')
                                <div class="form-group col-6 col-lg-12 px-3 px-lg-0">
                                    <span>
                                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                                    </span>
                                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                                        <option value=0>- @lang('common.all') -</option>
                                        @foreach($lists[$cm->column->list_fk] as $element)
                                            <option value="{{$element->element_id}}"
                                                @if(($search_terms['fields'][$cm->column->column_id] ?? "") == 
                                                    $element->element_id)
                                                        selected
                                                @endif
                                            >
                                                @for ($i = 0; $i < $element->depth; $i++)
                                                    |___
                                                @endfor
                                                {{ $element->attributes->firstWhere('name', 'name_' . app()->getLocale())->pivot->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                                </div>
                            @endif

                            <!-- Dropdown menus for date ranges -->
                            @if($cm->column->data_type_name == '_date_range_')
                                <div class="form-group col-6 col-lg-12 px-lg-0">
                                    <span>
                                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                                    </span>
                                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                                        <option value=0>- @lang('common.all') -</option>
                                        <option value=-1
                                            @if(($search_terms['fields'][$cm->column->column_id] ?? "") == -1)
                                                selected
                                            @endif
                                        >
                                        @lang('common.unknown')</option>
                                        @foreach($dateranges[$cm->column_fk] as $range => $count)
                                            @if($count)
                                                <option value="{{$range}}"
                                                    @if(($search_terms['fields'][$cm->column->column_id] ?? "") == $range)
                                                        selected
                                                    @endif
                                                >
                                                    {{$range}}@lang('common.decade_suffix')
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                                </div>
                            @endif
                        
                        @endforeach
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary">@lang('search.search')</button>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('search.index') }}" class="btn btn-danger" role="button">
                            @lang('search.reset')
                        </a>
                    </div>
                </form>
            
            </div>
            <!-- Search results -->
            <div class="col-lg-10" id="searchResults">
                
                <!-- Search results for details -->
                @if($items->count())
                    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.item_search_results')
                @endif
                
                <!-- Search results for taxa -->
                @if($taxa->count())
                    <ul class="list-group">
                    @foreach($taxa as $taxon)
                        <li class="list-group-item">
                            @auth
                                <a href="{{ route('taxon.edit', $taxon) }}" target="_blank">
                            @endauth
                                    {{ $taxon->full_name }}
                            @auth
                                </a>
                            @endauth
                            &nbsp;
                            <span class="badge badge-secondary">
                                {{ count($taxon->items->where('item_type_fk', '<>', 188)) }} Belege
                            </span>
                            @if(count($taxon->items->where('item_type_fk', '<>', 188)))
                                <ul class="list-group">
                                @foreach($taxon->items
                                    ->where('item_type_fk', '<>', 188)
                                    ->sortBy('item_type_fk')
                                    ->sortBy('title')
                                as $item)
                                    <li class="list-group-item">
                                        <a href="{{ route('item.show.public', [$item->item_id]) }}">
                                            {{ $item->title }}
                                        </a>
                                    </li>
                                @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    </ul>
                @endif
                
                @if(env('APP_DEBUG'))
                    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
                @endif
                
            </div>
        </div>
    </div>

</div>

@includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')

@endsection
