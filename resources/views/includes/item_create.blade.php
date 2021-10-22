{{-- Don't include if we are using a backend route --}}
@unless (Route::currentRouteName() == 'item.create')
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'edit_form',
        'section_heading' => __('items.my_own'),
        'section_subheading' => 'Lorem ipsum dolor sit amet consectetur.',
    ])
@endunless

<div class="container">
@if ($errors->any())
    <div class="alert alert-danger">
        @lang('common.form_validation_error')
    </div>
@endif

<h2>@lang('items.new')</h2>

@if(count($colmap)==0)
    <div class="alert alert-info">
        @lang('colmaps.none_available')
    </div>
    <div>
        <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
    </div>
@else

<form id="itemCreateForm" action="{{ route($options['route']) }}" method="POST" enctype="multipart/form-data">
    
@if($options['edit.meta'])
    <div class="form-group">
        <span>@lang('items.menu_title')</span>
        <input type="text" name="title" class="form-control" value="{{old('title')}}" autofocus />
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public') == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public') == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($items as $item)
                <option value="{{$item->item_id}}"
                    @if(old('parent') == $item->item_id) selected @endif>
                    @for ($i = 0; $i < $item->depth + 1; $i++)
                        |___
                    @endfor
                    {{ $item->title }}
                    ({{ $item->item_type_fk }})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.list')</span>
        <input
            type="text"
            name="taxon_name"
            class="form-control"
            value="@if($taxon) {{$taxon->full_name}} ({{$taxon->native_name}}) @else @lang('common.none') @endif"
            readonly
        />
        <input type="hidden" name="taxon" value="{{optional($taxon)->taxon_id}}" />
    </div>
@endif
    
    @foreach($colmap as $cm)
        
        {{-- Don't show columns which have auto generated content, e.g. image size/dimensions --}}
        @unless($cm->getConfigValue('editable') === false)
        
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <select
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        size=1
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >
                        <option value="">@lang('common.choose')</option>
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id) == $element->element_id)
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
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is list with multiple elements --}}
            @case('_multi_list_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input type="hidden" name="fields[{{ $cm->column->column_id }}][dummy]" value="0" />
                    <select
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}][]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        size=5
                        multiple
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(collect(old('fields.'. $cm->column->column_id))->contains($element->element_id))
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
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is boolean --}}
            @case('_boolean_')
                <div class="form-group">
                    <div class="form-check @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif">
                        <input type="hidden" name="fields[{{ $cm->column->column_id }}]" value=0 />
                        <input
                            type="checkbox"
                            id="fieldsInput-{{ $cm->column->column_id }}"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-check-input"
                            value=1 
                            @if(old('fields.'. $cm->column->column_id)) checked @endif
                            @if($loop->first && !$options['edit.meta']) autofocus @endif
                        />
                        @include('includes.column_label', ['css_class' => 'form-check-label'])
                    </div>
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
            {{-- Data_type of form field is float --}}
            @case('_float_')
            {{-- Data_type of form field is string --}}
            @case('_string_')
            {{-- Data_type of form field is (menu) title --}}
            @case('_title_')
            {{-- Data_type of form field is image title --}}
            @case('_image_title_')
            {{-- Data_type of form field is image copyright --}}
            @case('_image_copyright_')
            {{-- Data_type of form field is redirect --}}
            @case('_redirect_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    @if($cm->getConfigValue('textarea'))
                        <textarea
                            id="fieldsInput-{{ $cm->column->column_id }}"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-control {{ $cm->getConfigValue('data_subtype') }} @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                            placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                            rows="$cm->getConfigValue('textarea')"
                            @if($loop->first && !$options['edit.meta']) autofocus @endif
                        >{{
                            old('fields.'. $cm->column->column_id)
                        }}</textarea>
                    @else
                        <input
                            type="text"
                            id="fieldsInput-{{ $cm->column->column_id }}"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-control {{ $cm->getConfigValue('data_subtype') }}@if($cm->getConfigValue('search') == 'address') autocomplete @endif @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                            placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                            value="{{old('fields.'. $cm->column->column_id)}}"
                            @if($cm->getConfigValue('editable') == 'readonly' && !$options['edit.meta']) readonly @endif
                            @if($loop->first && !$options['edit.meta']) autofocus @endif
                        />
                    @endif
                    @if($cm->getConfigValue('data_subtype') == 'location_city')
                        <button type="button" class="btn btn-primary btn-sm searchAddressBtn">
                            @lang('common.get_latlon')
                        </button>
                    @endif
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is html --}}
            @case('_html_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <textarea
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control summernote @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        rows=5
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >{!!
                        old('fields.'. $cm->column->column_id)
                    !!}</textarea>
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('.summernote').summernote({
                            tabsize: 4,
                            height: 200
                        });
                    });
                </script>
                @break
            
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input
                        type="url"
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        value="{{old('fields.'. $cm->column->column_id, 'https://')}}"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input
                        type="date"
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        value="{{old('fields.'. $cm->column->column_id)}}"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is date range --}}
            @case('_date_range_')
                <div class="form-group">
                    @include('includes.column_label')
                    <br/>
                    <!-- Radio buttons to switch the type of date -->
                    <div class="form-check form-check-inline">
                        <input
                            type="radio"
                            id="datePointRadio-{{ $cm->column->column_id }}"
                            name="date_type"
                            class="form-check-input"
                            data-column="{{ $cm->column->column_id }}"
                            value="point"
                            @if(old('date_type') == 'point')
                                checked
                            @endif
                        >
                        <label class="form-check-label" for="datePointRadio-{{ $cm->column->column_id }}">@lang('common.date_point')</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input
                            type="radio"
                            id="datePeriodRadio-{{ $cm->column->column_id }}"
                            name="date_type"
                            class="form-check-input"
                            data-column="{{ $cm->column->column_id }}"
                            value="period"
                            @if(old('date_type') == 'period')
                                checked
                            @endif
                        >
                        <label class="form-check-label" for="datePeriodRadio-{{ $cm->column->column_id }}">@lang('common.date_period')</label>
                    </div>
                    <!-- Form field for the date (point in time) -->
                    @if(old('date_type') == 'point')
                        <div class="collapse show date-point" data-column="{{ $cm->column->column_id }}">
                    @else
                        <div class="collapse date-point" data-column="{{ $cm->column->column_id }}">
                    @endif
                        <input
                            type="date"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-control @if($errors->has('fields.'.$cm->column->column_id.'.start')) is-invalid @endif"
                            data-column="{{ $cm->column->column_id }}"
                            value="{{ old('fields.'. $cm->column->column_id .'.start') }}"
                        />
                        @include('includes.form_input_help')
                        <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id .'.start') }}</span>
                    </div>
                    <!-- Form fields for the date (period of time) -->
                    @if(old('date_type') == 'period')
                        <div class="collapse show date-period" data-column="{{ $cm->column->column_id }}">
                    @else
                        <div class="collapse date-period" data-column="{{ $cm->column->column_id }}">
                    @endif
                        @include('includes.form_date_range', [
                            'start_date' => [
                                old('start_year', date('Y')),
                                old('start_month', 1),
                                old('start_day', 1),
                            ],
                            'end_date' => [
                                old('end_year', date('Y')),
                                old('end_month', date('n')),
                                old('end_day', date('j')),
                            ],
                        ])
                        @include('includes.form_input_help')
                        <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id .'.start') }}</span>
                        <br/>
                        <input
                            type="button"
                            class="btn btn-primary"
                            onClick="checkDateRange({{ $cm->column->column_id }});"
                            value="@lang('common.apply')"
                        />
                    </div>
                    <!-- Hidden form fields for time range passed to laravel controller -->
                    <input
                        type="hidden"
                        name="fields[{{ $cm->column->column_id }}][start]"
                        class="form-control date-period-start"
                        data-column="{{ $cm->column->column_id }}"
                        value="{{ old('fields.'. $cm->column->column_id .'.start') }}"
                    />
                    <input
                        type="hidden"
                        name="fields[{{ $cm->column->column_id }}][end]"
                        data-column="{{ $cm->column->column_id }}"
                        class="form-control date-period-end"
                        value="{{ old('fields.'. $cm->column->column_id .'.end') }}"
                    />
                </div>
                <script type="text/javascript">
                    // Triggered when radio changed
                    $('.form-check input[name=date_type][value=point]').click(function(event) {
                        var column = $(this).data('column');
                        //alert('point ' + column);
                        $('.date-point[data-column='+column+']').collapse('show');
                        $('.date-period[data-column='+column+']').collapse('hide');
                    });
                    $('.form-check input[name=date_type][value=period]').click(function(event) {
                        var column = $(this).data('column');
                        //alert('period ' + column);
                        $('.date-point').filter(function () {
                            return $(this).data("column") === column;
                        }).collapse('hide');
                        $('.date-period').filter(function () {
                            return $(this).data("column") === column;
                        }).collapse('show');
                    });
                    // Triggered when date (point in time) was edited
                    $('.date-point [type=date]').blur(function(event) {
                        var column = $(this).data('column');
                        //alert('point blur ' + column + ' ' + $(this).val());
                        var date_start = $('.date-period-start').filter(function () {
                            return $(this).data("column") === column;
                        });
                        var date_end = $('.date-period-end').filter(function () {
                            return $(this).data("column") === column;
                        });
                        // Set hidden form fields to be sent to laravel controller
                        date_start.val($(this).val());
                        date_end.val($(this).val());
                    });
                    // Perform checks on date range and merge values from all six single-value fields
                    // to two date fields, to be sent to laravel controller
                    function checkDateRange(column) {
                        //var column = $(this).data('column');
                        var date_start = $(".date-period-start").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var date_end = $(".date-period-end").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var start_day = $("select[name='start_day']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var start_month = $("select[name='start_month']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var start_year = $("select[name='start_year']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var end_day = $("select[name='end_day']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var end_month = $("select[name='end_month']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        var end_year = $("select[name='end_year']").filter(function () {
                            return $(this).data("column") === column;
                        });
                        // Check for invalid dates and adjust the day if necessary
                        checkValidDate(start_day, start_month);
                        checkValidDate(end_day, end_month);
                        checkValidRange(start_day, start_month, start_year, end_day, end_month, end_year);
                        // Set hidden form fields to be sent to laravel controller
                        date_start.val(start_year.val() + "-" + start_month.val() + "-" + start_day.val());
                        date_end.val(end_year.val() + "-" + end_month.val() + "-" + end_day.val());
                    }
                    // Check for invalid day values. Each month has a different amount of days
                    function checkValidDate(day, month) {
                        if (month.val() == 2 && day.val() > 28) {
                            day.val("28");
                        }
                        if ((month.val() == 4 || month.val() == 6 || month.val() == 9 || month.val() == 11) && day.val() > 30) {
                            day.val("30");
                        }
                    }
                    // Check for end date to be later than start date
                    function checkValidRange(d1, m1, y1, d2, m2, y2) {
                        var start = new Date(y1.val(), m1.val(), d1.val());
                        var end = new Date(y2.val(), m2.val(), d2.val());
                        if ((end.getTime() - start.getTime()) < 0) {
                            y2.val(parseInt(y1.val())+1);
                        }
                    }
                </script>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input type="hidden" name="fields[{{ $cm->column->column_id }}][dummy]" value="0" />
                    <input
                        type="file"
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}][file]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control-file @if($errors->has('fields.'.$cm->column->column_id.'.file')) is-invalid @endif"
                        accept=".jpg, .jpeg"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="form-text text-muted">@lang('columns.image_hint') @lang('items.file_max_size', ['max' => config('media.image_max_size', 2048)])</span>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id .'.file') }}</span>
                </div>

                <script type="text/javascript">
                    $('#fieldsInput-{{ $cm->column->column_id }}').on('change', function (e) {
                        if (this.files[0].size > {{ intval(config('media.image_max_size', 2048)) * 1024 }}) {
                            //console.log(this.files);
                            this.value = '';
                            let error_message = '@lang("items.file_max_size", ["max" => config("media.image_max_size", 2048)])';
                            // Show modal with error message
                            //$('#alertModalLabel').text('@lang("common.laravel_error")');
                            $('#alertModalContent').html('<div class="alert alert-danger">' + error_message + '</div>');
                            $('#alertModal').modal('show');
                        }
                    });
                </script>
                @break
            
            {{-- Data_type of form field is map --}}
            @case('_map_')
                <div class="form-group">
                    @include('includes.column_label')
                    
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
                        // Default values, used if geolocation API fails or is disabled
                        var lon = {{ floatval(old('fields.'. $cm->getConfigValue('map_lon_col'), Config::get('geo.map_lon', 14.986))) }};
                        var lat = {{ floatval(old('fields.'. $cm->getConfigValue('map_lat_col'), Config::get('geo.map_lat', 51.15))) }};
                        var zoom = {{ $cm->getConfigValue('map_zoom') }};
                        
                        // Use browser's geolocation API if available and enabled in config
                        if (Boolean({{ Config::get('geo.use_geolocation', false) }}) && navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                lon = position.coords.longitude;
                                lat = position.coords.latitude;
                                initMap();
                            }, function (error) {
                                initMap();
                            }, {
                                timeout:2500
                            });
                        }
                        else {
                            initMap();
                        }
                        
                        function initMap() {
                            // Init and display the map
                            osm_map.display(lon, lat, zoom);
                            osm_map.addMarker(lon, lat, '{{ asset("storage/images/dot.svg") }}');
                            
                            // Click on map to update position of marker
                            osm_map.map.on('singleclick', function (evt) {
                                let coord = osm_map.transformCoordinate(evt.coordinate);
                                // Update lat/lon form fields
                                $('input.location_lon').val(coord[0]);
                                $('input.location_lat').val(coord[1]);
                                // Update position of marker
                                osm_map.moveMarker($('input.location_lon').val(), $('input.location_lat').val());
                                
                                // Fetch data from geocoder API
                                $.ajax({
                                    url: '{{ Config::get("geo.reverse_geocoder_url") }}',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        format: 'json',
                                        'accept-language': 'de',
                                        lat: coord[1],
                                        lon: coord[0],
                                        key: '{{ Config::get("geo.api_key") }}',
                                    },
                                    success: function(data) {
                                        //console.log(data);
                                        // Update address form input fields with results from geocoder
                                        $('input.location_country').val(data.address.country);
                                        $('input.location_state').val(data.address.state);
                                        $('input.location_county').val(data.address.county);
                                        $('input.location_city').val(getTownFromAddress(data.address));
                                        $('input.location_street').val(data.address.road);
                                        $('input.location_postcode').val(data.address.postcode);
                                    },
                                    error:function (xhr) {
                                        //console.log(xhr);
                                        // In case of wrong API key, the server responds with HTTP 403, but XHR fails
                                        // because of missing CORS headers
                                        if (xhr.status == 0) {
                                            error_message = '@lang("common.geocoder_api_key_error")';
                                        }
                                        // Render the geocoder error message
                                        $('#alertModalLabel').text('@lang("common.laravel_error")');
                                        $('#alertModalContent').html('<div class="alert alert-danger">' + error_message + '</div>');
                                        $('#alertModal').modal('show');
                                    },
                                });
                            });
                        }
                        
                        function getTownFromAddress(address) {
                            if(address.city) {
                                return address.city;
                            }
                            else {
                                if(address.town) {
                                    return address.town;
                                }
                                else {
                                    if(address.village) {
                                        return address.village;
                                    }
                                    else {
                                        return "?";
                                    }
                                }
                            }
                        }
                        
                        // Autocomplete search for address form input
                        $('input.location_city').autocomplete( {
                            disabled: true,
                            minLength: 5,
                            source: function(request, response) {
                                // Fetch data from geocoder API
                                $.ajax({
                                    url: '{{ Config::get("geo.geocoder_url") }}',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        format: 'json',
                                        'accept-language': 'de',
                                        addressdetails: '1',
                                        q: request.term,
                                        key: '{{ Config::get("geo.api_key") }}',
                                    },
                                    success: function(data) {
                                        var transformed = $.map(data, function (element) {
                                            //console.log(element);
                                            return {
                                                label: element.display_name + ' (' + element.class + ')',
                                                value: element.osm_id,
                                                address: element.address,
                                                latitude: element.lat,
                                                longitude: element.lon,
                                                osm_id: element.osm_id,
                                            };
                                        });
                                        //console.log(transformed);
                                        response(transformed);
                                    },
                                    error:function (xhr) {
                                        //console.log(xhr);
                                        // In case of wrong API key, the server responds with HTTP 403, but XHR fails
                                        // because of missing CORS headers
                                        if (xhr.status == 0) {
                                            error_message = '@lang("common.geocoder_api_key_error")';
                                        }
                                        // Render the geocoder error message
                                        $('#alertModalLabel').text('@lang("common.laravel_error")');
                                        $('#alertModalContent').html('<div class="alert alert-danger">' + error_message + '</div>');
                                        $('#alertModal').modal('show');
                                    },
                                });
                            },
                            select: function (event, ui) {
                                //console.log(ui.item);
                                event.preventDefault();
                                $('input.location_city').autocomplete('disable');
                                
                                // Update address form input fields with results from geocoder
                                $('input.location_country').val(ui.item.address.country);
                                $('input.location_state').val(ui.item.address.state);
                                $('input.location_county').val(ui.item.address.county);
                                $('input.location_city').val(getTownFromAddress(ui.item.address));
                                $('input.location_street').val(ui.item.address.road);
                                $('input.location_postcode').val(ui.item.address.postcode);
                                $('input.location_lat').val(ui.item.latitude);
                                $('input.location_lon').val(ui.item.longitude);
                                
                                // Adjust map center to location of selected search result
                                osm_map.updatePosition($('input.location_lon').val(), $('input.location_lat').val());
                                osm_map.moveMarker($('input.location_lon').val(), $('input.location_lat').val());
                                
                                return false;
                            }
                        });
                        
                        // Send search request to geocoder API
                        $('button.searchAddressBtn').click(function(xhr) {
                            xhr.preventDefault();
                            $('input.location_city').autocomplete('enable');
                            $('input.location_city').autocomplete('search');
                        });
                    </script>
                @endif
                @include('includes.form_input_help')
                </div>
                @break
            
        @endswitch
        
        @endunless
        
    @endforeach
    
    @if(0 && $errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="form-group">
    {{-- Check if we are using a backend route --}}
    @if($options['edit.meta'] || !config('ui.upload_terms_auth'))
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    @else
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal" data-form-id="itemCreateForm">@lang('common.save')</button>
        @include('includes.modal_upload')
    @endif
    </div>
    {{ csrf_field() }}
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

@endif

</div>

{{-- Don't include if we are using a backend route --}}
@unless (Route::currentRouteName() == 'item.create')
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
@endunless
