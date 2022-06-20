@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('sidebar_menu_items')
    @parent
    
    @include('includes.item_submenu', [
        'sub' => $menu_root,
        'path' => $path,
        'order' => config('menu.sidebar_item_order', []),
        'exclude' => config('menu.sidebar_exclude_item_type', []),
    ])
    
    <script type="text/javascript">
        $(document).ready(function () {
            // Init the menu
            menu.init("{{ route('menu.children') }}");
        });
    </script>
@endsection

@section('content')

    @if($item->item_type->attributes->firstWhere('name', 'code')->pivot->value != '_static_')
        <!-- Image details -->
        @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
            'section_id' => 'details',
            'section_heading' => __(config('ui.frontend_layout') . '.details_heading'),
            'section_subheading' => __(config('ui.frontend_layout') . '.details_subheading'),
            'options' => ['image_medium' => true],
        ])
        
        <!-- Icons for user interaction -->
        @includeIf('includes.' . Config::get('ui.frontend_layout') . '.item_buttons')
    @else
        <!-- Display only the heading from database item -->
        @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
            'section_id' => 'details',
            'section_heading' => $item->title,
            'section_subheading' => '',
        ])
    @endif
    
@foreach($colmap->groupBy('column_group_fk') as $cg)
    
    @unless($cg->first()->column_group->getConfigValue('hide_heading'))
        <div class="column-group-title pt-2 mt-2 mb-0">
        @if($cg->first()->column_group->getConfigValue('show_collapsed'))
            <a data-toggle="collapse" href="#collapseCG{{ $cg->first()->column_group_fk }}" role="button" aria-expanded="true" aria-controls="collapseCG{{ $cg->first()->column_group_fk }}">
                <i class="fa" aria-hidden="true"></i>
                {{ optional(optional($cg->first()->column_group->attributes
                ->firstWhere('name', 'name_'.app()->getLocale()))->pivot)->value }}
            </a>
        @else
            <a data-toggle="collapse" href="#collapseCG{{ $cg->first()->column_group_fk }}" role="button" aria-expanded="false" aria-controls="collapseCG{{ $cg->first()->column_group_fk }}" class="collapsed">
                <i class="fa" aria-hidden="true"></i>
                {{ optional(optional($cg->first()->column_group->attributes
                ->firstWhere('name', 'name_'.app()->getLocale()))->pivot)->value }}
            </a>
        @endif
        </div>
    @endif
    
    @foreach($cg as $cm)
        
        @if($cg->first()->column_group->getConfigValue('hide_heading'))
            <div class="container-fluid px-0">
        @else
            @if($cg->first()->column_group->getConfigValue('show_collapsed'))
                <div class="container-fluid collapse show px-0" id="collapseCG{{ $cm->column_group_fk }}">
            @else
                <div class="container-fluid collapse px-0" id="collapseCG{{ $cm->column_group_fk }}">
            @endif
        @endif
        
        <div class="row my-2">
        @switch($cm->column->data_type_name)
            
            {{-- Data_type of form field is taxon --}}
            @case('_taxon_')
                @include('includes.column_title')
                <div class="col column-content">
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
                        @if($item->taxon->getAncestorWhereRank($cm->getConfigValue('taxon_parent')))
                            {{ $item->taxon->getAncestorWhereRank($cm->getConfigValue('taxon_parent'))->taxon_name }}
                            ({{ $item->taxon->getAncestorWhereRank($cm->getConfigValue('taxon_parent'))->native_name }})
                        @else
                            @lang('common.not_applicable')
                        @endif
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                @include('includes.column_title')
                <div class="col column-content">
                @if($details->firstWhere('column_fk', $cm->column->column_id))
                    @if($details->firstWhere('column_fk', $cm->column->column_id)->element)
                    {{ $details->firstWhere('column_fk', $cm->column->column_id)->element->attributes->
                        firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}
                    @else
                        @lang('common.not_chosen')
                    @endif
                @else
                    @can('show-admin')
                        <span class="text-danger">
                            @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                        </span>
                    @endcan
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is list with multiple elements --}}
            @case('_multi_list_')
                @include('includes.column_title')
                <div class="col column-content">
                @if($details->firstWhere('column_fk', $cm->column->column_id))
                    <ul class="list-unstyled">
                    @foreach($details->firstWhere('column_fk', $cm->column->column_id)->elements()->get() as $element)
                        <li>{{ $element->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}</li>
                    @endforeach
                    </ul>
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is boolean --}}
            @case('_boolean_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int ? __('common.yes') : __('common.no') }}
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{-- TODO: move scaling to controller or model --}}
                    @if($cm->getConfigValue('scale_factor'))
                        {{ round(optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int * $cm->getConfigValue('scale_factor'), $cm->getConfigValue('precision')) }}
                    @else
                        {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int }}
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is float --}}
            @case('_float_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_float }}
                </div>
                @break
            
            {{-- Data_type of form field is string --}}
            @case('_string_')
            {{-- Data_type of form field is image title --}}
            @case('_image_title_')
            {{-- Data_type of form field is image copyright --}}
            @case('_image_copyright_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string }}
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                @include('includes.column_title')
                <div class="col column-content column-content-html">
                    {!! optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string !!}
                </div>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string }}
                </div>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                @include('includes.column_title')
                <div class="col column-content">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_date }}
                </div>
                @break
            
            {{-- Data_type of form field is date range --}}
            @case('_date_range_')
                @include('includes.column_title')
                <div class="col column-content">
                @if($details->firstWhere('column_fk', $cm->column->column_id))
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from())->toDateString() }}
                    @if($details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from() != $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to())
                        - {{ $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to()->toDateString() }}
                    @endif
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                @include('includes.column_title')
                <div class="col column-content">
                    @if($cm->getConfigValue('image_show') == 'gallery')
                        <div class="container-fluid">
                            <div class="row align-items-end">
                            @foreach($items->where('parent_fk', $item->item_id)->sortBy('title') as $specimen)
                                @foreach($items->where('parent_fk', $specimen->item_id) as $it)
                                    {{-- Show specimen thumbnails only, no images of details --}}
                                    @if(strpos($it->getDetailWhereDataType('_image_title_'), 'Gesamtansicht') !== false)
                                    <div class="col-auto py-2">
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
                                                title="{{ $it->getDetailWhereDataType('_image_title_') }}"
                                            />
                                        @endif
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            </a>
                                        @endif
                                        <br/>
                                        <a href="{{ route('item.show.public', $specimen->item_id) }}"
                                            title="{{ $specimen->title }}">
                                            {{ Str::limit($specimen->title, 12) }}
                                        </a>
                                    </div>
                                    @endif
                                @endforeach
                            @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($cm->getConfigValue('image_show') == 'specimen')
                        <div class="container-fluid">
                            <div class="row align-items-end">
                                @foreach($items->where('parent_fk', $item->item_id)->sortBy('title') as $it)
                                    <div class="col-auto py-2">
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
                                                height={{ Config::get('media.preview_height') }}
                                                title="{{ $it->getDetailWhereDataType('_image_title_') }}"
                                            />
                                        @else
                                            <img src="https://webapp.senckenberg.de/bestikri/files/images_preview/2/{{ $it->getDetailWhereDataType('_image_') }}"
                                                height={{ Config::get('media.preview_height') }}
                                                title="{{ $it->getDetailWhereDataType('_image_title_') }}"
                                            />
                                        @endif
                                        @if($cm->getConfigValue('image_link') == 'zoomify')
                                            </a>
                                        @endif
                                        <br/>
                                        {{ $it->getDetailWhereDataType('_image_title_') }}
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
                            @can('show-admin')
                                <span class="text-danger">
                                    @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                                </span>
                            @endcan
                        @endif
                    @endif
                    
                    @if($cm->getConfigValue('image_show') == 'filename')
                        {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string }}
                    @endif
                </div>
                @break
            
            {{-- Data_type of form field is map --}}
            @case('_map_')
                @include('includes.column_title')
                <div class="col column-content">
                @if($cm->getConfigValue('map') == 'iframe')
                    @if($details->firstWhere('column_fk', $cm->column->column_id))
                        @if($cm->getConfigValue('map_iframe') == 'url')
                            <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                                src="{{ $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}"
                            >
                        @endif
                        @if($cm->getConfigValue('map_iframe') == 'service')
                            <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                                src="{{ Config::get('media.mapservice_url') }}artid={{ 
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}"
                            >
                        @endif
                        <p>@lang('items.no_iframe')</p>
                        </iframe>
                    @else
                        @can('show-admin')
                            <span class="text-danger">
                                @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                            </span>
                        @endcan
                    @endif
                @endif
                @if($cm->getConfigValue('map') == 'inline')
                    <div id="map" class="map"
                        data-colmap="{{ $cm->colmap_id }}"
                        data-item="{{ $item->item_id }}"
                        data-map-config="{{ route('map.config', ['colmap' => $cm->colmap_id]) }}"
                    >
                        <div id="popup"></div>
                        <div id="mapError" style="display:none;"><b>@lang("items.no_position_for_map")</b></div>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            var colmapId = $('#map').data('colmap');
                            var itemId = $('#map').data('item');
                            var mapConfig = $('#map').data('map-config');
                            osm_map.init(colmapId, itemId, mapConfig);
                        });
                        /*
                        var lon = {{ optional($details->firstWhere('column_fk', $cm->getConfigValue('map_lon_col')))->value_float ?? 0 }};
                        var lat = {{ optional($details->firstWhere('column_fk', $cm->getConfigValue('map_lat_col')))->value_float ?? 0 }};
                        var zoom = {{ $cm->getConfigValue('map_zoom') }};
                        var coordinatesAvailabe = true;

                        if (!lon && !lat) {
                            lon = {{ Config::get('geo.map_lon', 14.986) }};
                            lat = {{ Config::get('geo.map_lat', 51.15) }};
                            coordinatesAvailabe = false;
                        }
                        
                        {{-- Init and display the map --}}
                        osm_map.display(lon, lat, zoom);
                        if (coordinatesAvailabe) {
                            osm_map.addMarker(lon, lat, '{{ asset("storage/images/dot.svg") }}');
                        }
                        else {
                            var coordinates = osm_map.map.getView().getCenter();
                            osm_map.popup.setPosition(coordinates);
                            var content = '<b>@lang("items.no_position_for_map")</b>';
                            $('#popup').popover({
                                placement: 'bottom',
                                html: true,
                                title: '',
                                content: content,
                            });
                            $('#popup').popover('show');
                            
                            // Move popover after moving the map
                            osm_map.map.on('moveend', function (evt) {
                                var coordinates = osm_map.map.getView().getCenter();
                                osm_map.popup.setPosition(coordinates);
                            });
                        }
                        */
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

    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
    
    @include('includes.modal_login_request')
    @include('includes.modal_download')
    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')

@endsection
