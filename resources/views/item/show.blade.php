@extends('layouts.frontend')

@section('sidebar_menu_items')
    @parent
    
    @foreach($items as $it)
        @if(true || $it->depth > 0)
            <li class="nav-item">
                @if($it->item_id == $item->item_id)
                    <a class="nav-link active" href="{{ $it->item_id }}">
                @else
                    <a class="nav-link" href="{{ $it->item_id }}">
                @endif
                @if($it->depth == 1)
                    &nbsp;&nbsp;
                @endif
                @if($it->depth == 2)
                    &nbsp;&nbsp;-->
                @endif
                {{ $it->getTitleColumn() }}
                {{-- Screen readers can mention the currently active menu item --}}
                @if($it->item_id == $item->item_id)
                    <span class="sr-only">(current)</span>
                @endif
                </a>
            </li>
        @endif
    @endforeach
    
@endsection

@section('content')

    <h2>@lang('items.list')</h2>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('lists.parent')</h5>
        </div>
        <div class="card card-body">
            {{ $item->taxon->parent->taxon_name }} ({{ $item->taxon->parent_fk }})
        </div>
    </div>

    @foreach($colmap as $cm)
        <div class="card">
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is taxon --}}
            @case('_taxon_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    @if($cm->getConfigValue('taxon_show') == 'full_name')
                        {{ $item->taxon->full_name }}
                    @endif
                    @if($cm->getConfigValue('taxon_show') == 'native_name')
                        {{ $item->taxon->native_name }}
                    @endif
                    @if($cm->getConfigValue('taxon_show') == 'synonyms')
                        @foreach($item->taxon->synonyms as $synonym)
                            {{ $synonym->full_name }}<br/>
                        @endforeach
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                @foreach($lists[$cm->column->list_fk] as $element)
                    @foreach($element->values as $v)
                        {{$v->value}}, 
                    @endforeach
                @endforeach
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_int) }}
                </div>
                @break
            
            {{-- Data_type of form field is float --}}
            @case('_float_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_float) }}
                </div>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}
                </div>
                @break
            
            {{-- Data_type of form field is string --}}
            @case('_string_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}
                </div>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}
                </div>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    @if($cm->getConfigValue('image_show') == 'preview')
                        @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                            <span>
                            @if($cm->getConfigValue('image_link') == 'zoomify')
                                <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{ Config::get('media.zoomify_image_path') }}{{ pathinfo($details->firstWhere('column_fk', $cm->column->column_id)->value_string, PATHINFO_FILENAME) }}.zif">
                            @endif
                            <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                            />
                            @if($cm->getConfigValue('image_link') == 'zoomify')
                                </a>
                            @endif
                            </span>
                        @else
                            @lang('columns.image_not_available')
                        @endif
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is map --}}
            @case('_map_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                @if($cm->getConfigValue('map') == 'iframe')
                    @if($cm->getConfigValue('map_iframe') == 'url')
                        <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                            src="{{ old('fields.'. $cm->column->column_id, 
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                        >
                    @endif
                    @if($cm->getConfigValue('map_iframe') == 'service')
                        <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                            src="{{ Config::get('media.mapservice_url') }}artid={{ old('fields.'. $cm->column->column_id, 
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                        >
                    @endif
                    <p>@lang('items.no_iframe')</p>
                    </iframe>
                @endif
                </div>
                @break
            
        @endswitch
        </div>
    @endforeach
    
@endsection
