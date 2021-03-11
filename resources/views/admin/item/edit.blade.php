@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('items.edit')</h2>

<form action="{{ route('item.update', $item->item_id) }}" method="POST" enctype="multipart/form-data">
    
    <div class="form-group">
        <span>@lang('items.menu_title')</span>
        <input type="text" name="title" class="form-control" value="{{old('title', $item->title)}}" />
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public', $item->public) == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public', $item->public) == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent" class="form-control" size=1 >
            <option value="">@lang('common.root')</option>
            @foreach($items as $it)
                <option value="{{$it->item_id}}"
                    @if(old('parent', $item->parent_fk) == $it->item_id) selected @endif>
                    @for ($i = 0; $i < $it->depth + 1; $i++)
                        |___
                    @endfor
                    {{ $it->title }}
                    ({{ $it->item_type_fk }})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('taxon.list')</span>
        <select name="taxon" id="taxon_select" class="form-control" size=1 readonly>
            <option value="">@lang('common.none')</option>
            @foreach($taxa as $taxon)
                @unless($taxon->valid_name)
                    <option value="{{$taxon->taxon_id}}"
                        @if(old('taxon', $item->taxon_fk) == $taxon->taxon_id) selected @endif>
                        @for ($i = 0; $i < $taxon->depth; $i++)
                            |___
                        @endfor
                        {{$taxon->taxon_name}} {{$taxon->taxon_author}} ({{$taxon->native_name}})
                    </option>
                @endunless
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('taxon') }}</span>
    </div>
    <script type="text/javascript">
        var elem = document.getElementById("taxon_select");
        elem.addEventListener("change", TaxonChanged);

        function TaxonChanged() {
            var tax = document.getElementById("taxon_select").selectedIndex;
            alert('Changing the Taxon is not allowed!');
            //window.location.reload(true);
        }
    </script>
    
    @foreach($colmap as $cm)
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id, 
                                    $details->firstWhere('column_fk', $cm->column->column_id)->element_fk) == 
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
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is image pixel per inch --}}
            @case('_image_ppi_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_int) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is float --}}
            @case('_float_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_float) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is date range --}}
            @case('_date_range_')
                <div class="form-group">
                    <div>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </div>
                    <!-- Radio buttons to switch the type of date -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="date_type" id="datePointRadio-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" value="point" checked>
                        <label class="form-check-label" for="datePointRadio-{{ $cm->column->column_id }}">@lang('common.date_point')</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="date_type" id="datePeriodRadio-{{ $cm->column->column_id }}" data-column="{{ $cm->column->column_id }}" value="period">
                        <label class="form-check-label" for="datePeriodRadio-{{ $cm->column->column_id }}">@lang('common.date_period')</label>
                    </div>
                    <!-- Form field for the date (point in time) -->
                    <div class="collapse show date-point" data-column="{{ $cm->column->column_id }}">
                        <input type="date" name="fields[{{ $cm->column->column_id }}]" data-column="{{ $cm->column->column_id }}" class="form-control" 
                            value="{{ old('fields.'. $cm->column->column_id, 
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
                        <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    </div>
                    <!-- Form fields for the date (period of time) -->
                    <div class="collapse date-period" data-column="{{ $cm->column->column_id }}">
                        @include('includes.form_date_range')
                        <input class="btn btn-primary" type="button" value="@lang('common.save')" onClick="checkDateRange({{ $cm->column->column_id }});">
                    </div>
                    <!-- Hidden form fields for time range passed to laravel controller -->
                    <input type="text" name="fields[{{ $cm->column->column_id }}][start]" data-column="{{ $cm->column->column_id }}" class="form-control date-period-start" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
                    <input type="text" name="fields[{{ $cm->column->column_id }}][end]" data-column="{{ $cm->column->column_id }}" class="form-control date-period-end" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
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
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="date" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
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
            {{-- Data_type of form field is redirect --}}
            @case('_redirect_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    @if($details->firstWhere('column_fk', $cm->column->column_id))
                        <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                            value="{{ old('fields.'. $cm->column->column_id, 
                            $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    @else
                        <span>detail column {{$cm->column->column_id}} for map not found</span>
                    @endif
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is html --}}
            @case('_html_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <textarea name="fields[{{ $cm->column->column_id }}]" class="form-control summernote" 
                        rows=5>{!! old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) !!}</textarea>
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
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <input type="url" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
                    <div class="form-row">
                        <div class="col">
                            <input type="file" class="form-control-file" name="fields[{{ $cm->column->column_id }}]" />
                            <span class="form-text text-muted">@lang('column.image_hint')</span>
                        </div>
                        <div class="col">
                        @if($cm->getConfigValue('image_show') == 'preview')
                            @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                    width=100
                                />
                            @else
                                @lang('columns.image_not_available')
                            @endif
                        @endif
                        </div>
                    </div>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is map --}}
            @case('_map_')
                <div class="form-group">
                    <span>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </span>
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
                        // Init and display the map
                        osm_map.display(lon, lat, zoom);
                        
                        //osm_map.updateSize();
                    </script>
                @endif
                </div>
                @break
            
        @endswitch
        
    @endforeach
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

</div>

@endsection
