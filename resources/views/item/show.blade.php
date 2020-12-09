@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('sidebar_menu_items')
    @parent
    
    @foreach($menu_root as $it)
        @if($it->public == 1)
            <li class="nav-item">
                @if($it->item_id == $item->item_id)
                    <a class="nav-link active" href="{{ $it->item_id }}">
                @else
                    <a class="nav-link" href="{{ $it->item_id }}">
                @endif
                {{ $it->title }}
                
                {{-- Screen readers can mention the currently active menu item --}}
                @if($it->item_id == $item->item_id)
                    <span class="sr-only">(current)</span>
                @endif
                </a>
                
                @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $it->item_id && count($it->children))
                    <ul>
                        @include('includes.item_submenu', ['sub' => $it->children, 'path' => $path])
                    </ul>
                @endif
            </li>
        @endif
    @endforeach
    
@endsection

@section('content')

{{-- Quick hack for LFP mock-up --}}
@if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
    <!-- Image details -->
    <section class="page-section" id="details">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Bilddetails</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <div class="card">
                <div class="card-body">
@endif

@foreach($colmap->groupBy('column_group_fk') as $cg)
    
    @unless($cg->first()->column_group->getConfigValue('hide_heading'))
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
    @endif
    
    @foreach($cg as $cm)
        @if($cg->first()->column_group->getConfigValue('hide_heading'))
            <div class="container-fluid">
        @else
            @if($cg->first()->column_group->getConfigValue('show_collapsed'))
                <div class="container-fluid collapse show" id="collapseCG{{ $cm->column_group_fk }}">
            @else
                <div class="container-fluid collapse" id="collapseCG{{ $cm->column_group_fk }}">
            @endif
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
                    {{ $details->firstWhere('column_fk', $cm->column->column_id)->element->attributes->
                        firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
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
            {{-- Data_type of form field is image title --}}
            @case('_image_title_')
            {{-- Data_type of form field is image copyright --}}
            @case('_image_copyright_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
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
                            @foreach($items->where('parent_fk', $item->item_id)->sortBy('title') as $specimen)
                                @foreach($items->where('parent_fk', $specimen->item_id) as $it)
                                    {{-- Show specimen thumbnails only, no images of details --}}
                                    @if(strpos($it->getDetailWhereDataType('_image_title_'), 'Gesamtansicht') !== false)
                                    <div class="col-auto">
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{
                                                Config::get('media.zoomify_zif_image_path')
                                            }}{{ pathinfo($it->getDetailWhereDataType('_image_'), PATHINFO_FILENAME)
                                            }}.zif&caption={{ rawurlencode($item->taxon->full_name ."; Barcode: ".
                                                explode('_', pathinfo($it->getDetailWhereDataType('_image_'),
                                                    PATHINFO_FILENAME))[0])
                                            }}&description={{ rawurlencode($it->getDetailWhereDataType('_image_title_'))
                                            }}&copyright={{ rawurlencode($it->getDetailWhereDataType('_image_copyright_')) 
                                            }}&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D{{
                                                $it->getDetailWhereDataType('_image_ppi_')/25.4
                                            }}">
                                        @endif
                                        @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                            $it->getDetailWhereDataType('_image_')))
                                            <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                                $it->getDetailWhereDataType('_image_')) }}"
                                                width={{ Config::get('media.preview_width') }}
                                                title="{{ $it->getDetailWhereDataType('_image_title_') }}"
                                            />
                                        @else
                                            <img src="https://webapp.senckenberg.de/bestikri/files/images_preview/2/{{ $it->getDetailWhereDataType('_image_') }}"
                                            width={{ Config::get('media.preview_width') }}
                                            />
                                        @endif
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            </a>
                                        @endif
                                        <br/><a href="{{ route('item.show.public', $specimen->item_id) }}">
                                        {{ explode('_', pathinfo($it->getDetailWhereDataType('_image_'), PATHINFO_FILENAME))[0] }}</a>
                                    </div>
                                    @endif
                                @endforeach
                            @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($cm->getConfigValue('image_show') == 'specimen')
                        <div class="container">
                            <div class="row">
                                @foreach($items->where('parent_fk', $item->item_id)->sortBy('title') as $it)
                                    <div class="col-auto">
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            {{-- Bestikri images have different pathes and types --}}
                                            @if(strpos($it->getDetailWhereDataType('_image_title_'), 'Gesamtansicht') === false)
                                                <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{
                                                    Config::get('media.zoomify_jpg_image_path')
                                                }}{{ pathinfo($it->getDetailWhereDataType('_image_'), PATHINFO_FILENAME)
                                                }}.jpg&caption={{ rawurlencode($item->taxon->full_name ."; Barcode: ".
                                                    explode('_', pathinfo($it->getDetailWhereDataType('_image_'),
                                                        PATHINFO_FILENAME))[0])
                                                }}&description={{ rawurlencode($it->getDetailWhereDataType('_image_title_'))
                                                }}&copyright={{ rawurlencode($it->getDetailWhereDataType('_image_copyright_'))
                                                }}&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D{{
                                                    $it->getDetailWhereDataType('_image_ppi_')/25.4
                                                }}">
                                            @else
                                                <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{
                                                    Config::get('media.zoomify_zif_image_path')
                                                }}{{ pathinfo($it->getDetailWhereDataType('_image_'), PATHINFO_FILENAME)
                                                }}.zif&caption={{ rawurlencode($item->taxon->full_name ."; Barcode: ".
                                                    explode('_', pathinfo($it->getDetailWhereDataType('_image_'),
                                                        PATHINFO_FILENAME))[0])
                                                }}&description={{ rawurlencode($it->getDetailWhereDataType('_image_title_'))
                                                }}&copyright={{ rawurlencode($it->getDetailWhereDataType('_image_copyright_'))
                                                }}&params=zMeasureVisible%3D1%26zUnits%3Dmm%26zPixelsPerUnit%3D{{
                                                    $it->getDetailWhereDataType('_image_ppi_')/25.4
                                                }}">
                                            @endif
                                        @endif
                                        @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                            $it->getDetailWhereDataType('_image_')))
                                            <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                                $it->getDetailWhereDataType('_image_')) }}"
                                                width={{ Config::get('media.preview_width') }}
                                                title="{{ $it->getDetailWhereDataType('_image_title_') }}"
                                            />
                                        @else
                                            <img src="https://webapp.senckenberg.de/bestikri/files/images_preview/2/{{ $it->getDetailWhereDataType('_image_') }}"
                                            width={{ Config::get('media.preview_width') }}
                                            />
                                        @endif
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            </a>
                                        @endif
                                        <br/>
                                        {{ explode('_', pathinfo($it->getDetailWhereDataType('_image_'), PATHINFO_FILENAME))[0] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($cm->getConfigValue('image_show') == 'preview')
                        @if($details->firstWhere('column_fk', $cm->column->column_id))
                            @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                                <span>
                                @if($cm->getConfigValue('image_link') == 'zoomify')
                                    <a target="_blank" href="{{ Config::get('media.zoomify_url') }}&image={{
                                        Config::get('media.zoomify_zif_image_path')
                                        }}{{ pathinfo($details->firstWhere('column_fk',
                                            $cm->column->column_id)->value_string, PATHINFO_FILENAME)
                                        }}.zif&&caption={{ rawurlencode($item->taxon->full_name)
                                        }}&description={{ rawurlencode($cm->column->translation->attributes
                                            ->firstWhere('name', 'name_'.app()->getLocale())->pivot->value)
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
                    
                    @if($cm->getConfigValue('image_show') == 'filename')
                        {{ $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is map --}}
            @case('_map_')
                @include('includes.column_title')
                <div class="col font-weight-bold">
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
                @if($cm->getConfigValue('map') == 'inline')
                    <div id="map" class="map"></div>
                    <script type="text/javascript">
                        var lon = {{ $details->firstWhere('column_fk', $cm->getConfigValue('map_lon_col'))->value_float }};
                        var lat = {{ $details->firstWhere('column_fk', $cm->getConfigValue('map_lat_col'))->value_float }};
                        var zoom = {{ $cm->getConfigValue('map_zoom') }};
                        
                        {{-- Init and display the map --}}
                        osm_map.display(lon, lat, zoom);
                        
                        {{-- Resize the map after un-collapsing the container --}}
                        $('#collapseCG{{ $cm->column_group_fk }}').on('shown.bs.collapse', function () {
                            osm_map.updateSize();
                        });
                    </script>
                @endif
                </div>
                @break
            
        @endswitch
        </div>
        </div>
    @endforeach
    
@endforeach

{{-- Quick hack for LFP mock-up --}}
@if(Config::get('ui.frontend_layout') == 'landschaftsfotoportal')
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
