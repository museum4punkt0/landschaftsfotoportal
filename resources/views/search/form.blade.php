@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('sidebar_menu_items')
    @parent
    
    @foreach($menu_root as $it)
        @if($it->public == 1)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('item.show.public', [$it->item_id]) }}">
                {{ $it->title }}
            </a>
        </li>
        @endif
    @endforeach
    
@endsection

@section('content')

@includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
    'section_id' => 'search',
    'section_heading' => __('search.header'),
    'section_subheading' => 'Lorem ipsum dolor sit amet consectetur.',
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
                    
                    @foreach($colmap as $cm)
                        
                        <!-- Dropdown menus for select lists -->
                        @if($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value == '_list_')
                            <div class="form-group">
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
                                            @foreach($element->values as $v)
                                                {{$v->value}}, 
                                            @endforeach
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                            </div>
                        @endif
                        
                        <!-- Dropdown menus for date ranges -->
                        @if($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value == '_date_range_')
                            <div class="form-group">
                                <span>
                                    {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                                </span>
                                <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                                    <option value=0>- @lang('common.all') -</option>
                                    @foreach($dateranges[$cm->column_fk] as $range => $count)
                                        @if($count)
                                            <option value="{{$range}}"
                                                @if(($search_terms['fields'][$cm->column->column_id] ?? "") == 
                                                    $range)
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
                @isset($items)
                    <!-- Portfolio Grid, Gallery with search results -->
                    @include('includes.item_gallery', [
                        'items' => $items,
                        'limit' => Config::get('ui.search_results'),
                        'heading' => __('search.results'),
                        'subheading' => 'Lorem ipsum dolor sit amet consectetur.'
                    ])
                    
                    @include('includes.modal_login_request')
                    @include('includes.modal_download')
                    @include('includes.modal_alert')
                    @include('includes.modal_cart_remove')
                    @include('includes.modal_comment_add')
                @endisset
                
                <!-- Search results for taxa -->
                @isset($taxa)
                    <ul class="list-group">
                    @foreach($taxa as $taxon)
                        <li class="list-group-item">
                            @auth
                                <a href="{{ route('taxon.edit', [$taxon->taxon_id]) }}" target="_blank">
                            @endauth
                                    {{ $taxon->full_name }}
                            @auth
                                </a>
                            @endauth
                            &nbsp;
                            <span class="badge badge-secondary">{{ count($taxon->items )}}</span>
                            @if(count($taxon->items))
                                <ul class="list-group">
                                @foreach($taxon->items->sortBy('item_type_fk')->sortBy('title') as $item)
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
                @endisset
                
                @if(env('APP_DEBUG'))
                    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
                @endif
                
            </div>
        </div>
    </div>

</div>

@includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')

@endsection
