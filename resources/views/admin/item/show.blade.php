@extends('layouts.app')

@section('content')

<div class="container">
    <h2>@lang('items.list')</h2>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('items.menu_title'): {{$item->title}}</h5>
            </div>
            <div class="card card-body">
                <span>
                    <a href="{{route('item.show.public', $item->item_id)}}" class="btn btn-primary">
                    @lang('items.show_frontend')
                    </a>
                </span>
            </div>
        </div>
    
    @if($item->taxon)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('lists.parent')</h5>
            </div>
            <div class="card card-body">
                {{ $item->taxon->parent->taxon_name }}
                ({{ $item->taxon->parent->rank_abbr }}, Taxon ID {{ $item->taxon->parent_fk }})
            </div>
        </div>
    @endif

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
                    @if($cm->getConfigValue('taxon_parent'))
                        {{ $item->taxon->getAncestorWhereRank($cm->getConfigValue('taxon_parent'))->taxon_name }}
                        ({{ $item->taxon->getAncestorWhereRank($cm->getConfigValue('taxon_parent'))->native_name }})
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
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
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
            {{-- Data_type of form field is (menu) title --}}
            @case('_title_')
            {{-- Data_type of form field is image title --}}
            @case('_image_title_')
            {{-- Data_type of form field is image copyright --}}
            @case('_image_copyright_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                @if($details->firstWhere('column_fk', $cm->column->column_id))
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}
                @else
                    <span>detail column {{$cm->column->column_id}} for string not found</span>
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
                <div class="card card-body">
                    {!! old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) !!}
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
                    @if($details->firstWhere('column_fk', $cm->column->column_id))
                        {{ $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}
                        @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                            <span>
                            @if($cm->getConfigValue('image_link') == 'zoomify')
                                <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{
                                    Config::get('media.zoomify_zif_image_path')
                                    }}{{ pathinfo($details->firstWhere('column_fk',
                                        $cm->column->column_id)->value_string, PATHINFO_FILENAME)
                                    }}.zif&&caption={{ $item->taxon->full_name
                                    }}&description={{ $cm->column->translation->attributes
                                        ->firstWhere('name', 'name_'.app()->getLocale())->pivot->value
                                    }}">
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
                    @else
                        <span>detail column {{$cm->column->column_id}} for image preview not found</span>
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
                    @if($details->firstWhere('column_fk', $cm->column->column_id))
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
                    @else
                        <span>detail column {{$cm->column->column_id}} for map not found</span>
                    @endif
                @endif
                </div>
                @break
            
        @endswitch
        </div>
    @endforeach
    
    @if(env('APP_DEBUG'))
        [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
    @endif

</div>

@endsection
