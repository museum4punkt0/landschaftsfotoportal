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

@foreach($colmap->groupBy('column_group_fk') as $cg)
    
    <div class="mt-4 mb-0">
    @if($cg->first()->column_group->getConfigValue('show_collapsed'))
        <a class="font-weight-bold" data-toggle="collapse" href="#collapseCG{{ $cg->first()->column_group_fk }}" role="button" aria-expanded="true" aria-controls="collapseCG{{ $cg->first()->column_group_fk }}">
            {{ $cg->first()->column_group->attributes
            ->firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}
        </a>
    @else
        <a class="font-weight-bold" data-toggle="collapse" href="#collapseCG{{ $cg->first()->column_group_fk }}" role="button" aria-expanded="false" aria-controls="collapseCG{{ $cg->first()->column_group_fk }}">
            {{ $cg->first()->column_group->attributes
            ->firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}
        </a>
    @endif
    </div>
    <hr class="my-0">
    
    @foreach($cg as $cm)
        @if($cg->first()->column_group->getConfigValue('show_collapsed'))
            <div class="container-fluid collapse show" id="collapseCG{{ $cm->column_group_fk }}">
        @else
            <div class="container-fluid collapse" id="collapseCG{{ $cm->column_group_fk }}">
        @endif
        <div class="row my-2">
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is taxon --}}
            @case('_taxon_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
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
                @include('includes.column_title')
                <div class="col font-weight-bold">
                @foreach($lists[$cm->column->list_fk] as $element)
                    @foreach($element->values as $v)
                        {{$v->value}}, 
                    @endforeach
                @endforeach
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_int) }}
                </div>
                @break
            
            {{-- Data_type of form field is float --}}
            @case('_float_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_float) }}
                </div>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}
                </div>
                @break
            
            {{-- Data_type of form field is string --}}
            @case('_string_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {!! old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) !!}
                </div>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    {{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}
                </div>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
                    @if($cm->getConfigValue('image_show') == 'gallery')
                        <div class="container">
                            <div class="row">
                                @foreach($items->where('parent_fk', $item->item_id) as $it)
                                    <div class="col-auto">
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{ Config::get('media.zoomify_image_path') }}{{ pathinfo($it->getTitleColumn() .'.jpg', PATHINFO_FILENAME) }}.zif">
                                        @endif
                                        @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                            $it->getTitleColumn() .'.jpg'))
                                            <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                                $it->getTitleColumn() .'.jpg') }}" height=168
                                            />
                                        @else
                                            <img src="https://webapp.senckenberg.de/bestikri/files/images_preview/2/{{ $it->getTitleColumn() .'.jpg' }}" height=168
                                            />
                                        @endif
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            </a>
                                        @endif
                                        <br/><a href="{{ route('item.show.public', $it->item_id) }}">{{ $it->getTitleColumn() }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($cm->getConfigValue('image_show') == 'specimen')
                        <span>
                            @if($cm->getConfigValue('image_link') == 'zoomify')
                                <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{ Config::get('media.zoomify_image_path') }}{{ pathinfo($item->getTitleColumn() .'.jpg', PATHINFO_FILENAME) }}.zif">
                            @endif
                            @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                $item->getTitleColumn() .'.jpg'))
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->getTitleColumn() .'.jpg') }}" height=168
                                />
                            @else
                                <img src="https://webapp.senckenberg.de/bestikri/files/images_preview/2/{{ $item->getTitleColumn() .'.jpg' }}" height=168
                                />
                            @endif
                            @if($cm->getConfigValue('image_link') == 'zoomify')
                                </a>
                            @endif
                            <br/>{{ $item->getTitleColumn() }}
                        </span>
                    @endif
                    
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
                @include('includes.column_title')
                <div class="col font-weight-bold">
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
        </div>
    @endforeach
    
@endforeach

@endsection
