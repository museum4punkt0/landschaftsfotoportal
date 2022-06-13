    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('items.menu_title')</h5>
        </div>
        <div class="card card-body">
            {{$item->title}}
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('common.meta_data')</h5>
        </div>
        <div class="card card-body">
            <table class="table table-sm table-borderless">
                <tr>
                    <td>@lang('common.created'):</td>
                    <td>{{ $item->creator->name }}</td><td>{{ $item->created_at }}</td>
                </tr>
                <tr>
                    <td>@lang('common.updated'):</td>
                    <td>{{ $item->editor->name }}</td><td>{{ $item->updated_at }}</td>
                </tr>
                <tr>
                    <td>@lang('common.published'):</td>
                    <td colspan=2>{{ $item->public }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    @if(Config::get('ui.revisions'))
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('revisions.header')</h5>
            </div>
            <div class="card card-body">
                <table class="table table-sm table-borderless">
                @foreach($revisions as $revision)
                    <tr>
                        <td>@lang('revisions.list'):
                            @if($revision->revision < 0)@lang('revisions.draft')@endif{{ $revision->revision }}
                        </td>
                        <td>{{ $revision->editor->name }}</td><td>{{ $revision->updated_at }}</td>
                        <td>
                            <span style="font-size: 0.5rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('revision.show', $revision) }}" title="@lang('common.show')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_show') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span style="font-size: 0.5rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('revision.edit', $revision) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            {{-- Revisions shouldn't be deleted --}}
                            @if(true)
                            <span style="font-size: 0.5rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('revision.destroy', $revision) }}"
                                        data-message="@lang('revisions.confirm_delete')"
                                        data-title="@lang('revisions.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    @endif

    @if($item->parent_fk)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('lists.parent')</h5>
            </div>
            <div class="card card-body">
                {{ $item->parent->title}}, Item ID 
                <a href="{{ route('item.show', $item->parent_fk) }}">{{ $item->parent_fk }}</a>
            </div>
        </div>
    @endif
    @if($item->taxon)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('taxon.parent')</h5>
            </div>
            <div class="card card-body">
                {{ $item->taxon->parent->taxon_name }}
                ({{ $item->taxon->parent->rank_abbr }}, Taxon ID {{ $item->taxon->parent_fk }})
            </div>
        </div>
    @endif

    @foreach($colmap as $cm)
        <div class="card">
        @switch($cm->column->data_type_name)
            
            {{-- Data_type of form field is taxon --}}
            @case('_taxon_')
                @include('includes.column_cardheader')
                
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
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                @if($details->firstWhere('column_fk', $cm->column->column_id))
                    @if($details->firstWhere('column_fk', $cm->column->column_id)->element)
                    {{ $details->firstWhere('column_fk', $cm->column->column_id)->element->attributes->
                        firstWhere('name', 'name_'.app()->getLocale())->pivot->value }}
                    @else
                        @lang('common.not_chosen')
                    @endif
                @else
                    <span class="text-danger">
                        @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                    </span>
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is list with multiple elements --}}
            @case('_multi_list_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
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
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int ? __('common.yes') : __('common.no') }}
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int }}
                </div>
                @break
            
            {{-- Data_type of form field is float --}}
            @case('_float_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_float }}
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
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string }}
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {!! optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string !!}
                </div>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string }}
                </div>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                    {{ optional($details->firstWhere('column_fk', $cm->column->column_id))->value_date }}
                </div>
                @break
            
            {{-- Data_type of form field is date range --}}
            @case('_date_range_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
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
                @include('includes.column_cardheader')
                
                <div class="card card-body">
                @if($cm->getConfigValue('image_show') == 'preview' || $cm->getConfigValue('image_show') == 'filename')
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
                                    }}.zif&&caption={{ rawurlencode($item->taxon->full_name)
                                    }}&description={{ rawurlencode($cm->column->translation->attributes
                                        ->firstWhere('name', 'name_'.app()->getLocale())->pivot->value)
                                    }}">
                            @else
                                <a href="#" data-toggle="modal" data-target="#imageLargeModal"
                                    data-img-source="{{ asset('storage/'. Config::get('media.medium_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}">
                            @endif
                            <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                            />
                                </a>
                            </span>
                        @else
                            @lang('columns.image_not_available')
                        @endif
                    @else
                        <span class="text-danger">
                            @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                        </span>
                    @endif
                @endif
                </div>
                @break
            
            {{-- Data_type of form field is map --}}
            @case('_map_')
                @include('includes.column_cardheader')
                
                <div class="card card-body">
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
                        <span class="text-danger">
                            @lang('items.no_detail_for_column', ['column' => $cm->column->column_id])
                        </span>
                    @endif
                @endif
                @if($cm->getConfigValue('map') == 'inline')
                    <div id="map" class="map"><div id="popup"></div></div>
                    <script type="text/javascript">
                        var lon = {{ optional($details->firstWhere('column_fk', $cm->getConfigValue('map_lon_col')))->value_float ?? 0 }};
                        var lat = {{ optional($details->firstWhere('column_fk', $cm->getConfigValue('map_lat_col')))->value_float ?? 0 }};
                        var zoom = {{ $cm->getConfigValue('map_zoom') }};
                        var coordinatesAvailabe = true;

                        if (!lon && !lat) {
                            lon = {{ Config::get('geo.map_lon', 14.986) }};
                            lat = {{ Config::get('geo.map_lat', 51.15) }};
                            coordinatesAvailabe = false;
                        }
                        
                        // Init and display the map
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
    @endforeach
