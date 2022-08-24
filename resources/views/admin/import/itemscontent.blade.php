@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">@lang('import.header'): @lang('items.header') ({{ $file_name }})</div>
        <div class="card-body">

            <form action="{{ route('import.items.process') }}" method="POST" class="form-horizontal">
                {{ csrf_field() }}
                <input type="hidden" name="item_type" value="{{ $colmaps[0]->item_type_fk }}" />

                <table class="table mx-0">
                    <tr>
                        <th>@lang('import.firstrow')</th>
                        <th>@lang('import.nextrows')</th>
                        <th>@lang('columns.header')</th>
                    </tr>

                    <!-- For each column in CSV -->
                    @foreach($csv_data[0] as $csv_header)
                        <tr>
                            <td>
                                <b>{{ $csv_header }}</b>
                            </td>
                            <td>
                                @foreach($csv_data as $row)
                                    @unless($loop->first)
                                        {{ substr($row[$loop->parent->index], 0, 75) }} [...]<br>
                                    @endunless
                                @endforeach
                            </td>
                            <td>
                                <!-- Select the column into which to import -->
                                <div class="form-group">
                                <select
                                    id="fieldsSelect-{{ $loop->index }}"
                                    name="fields[{{ $loop->index }}]"
                                    aria-describedby="fieldsSelectHelpBlock-{{ $loop->index }}"
                                    class="form-control form-control-sm fields-select"
                                    data-field-id="{{ $loop->index }}"
                                    size=1
                                    @if($loop->first) autofocus @endif
                                >
                                    <option value="0">@lang('common.ignore')</option>
                                    <option value="-1"
                                        @if(old('fields.'.$loop->index) == -1) selected @endif>
                                        * @lang('common.relation'): @lang('import.parent_details')
                                    </option>
                                    <option value="-2"
                                        @if(old('fields.'.$loop->index) == -2) selected @endif>
                                        * @lang('common.relation'): @lang('import.parent_taxon')
                                    </option>
                                    <option value="-3"
                                        @if(old('fields.'.$loop->index) == -3) selected @endif>
                                        * @lang('common.relation'): @lang('import.taxon_name')
                                    </option>
                                    @foreach($colmaps->unique('column_fk') as $colmap)
                                        {{-- Exclude columns with data type 'taxon' --}}
                                        @unless($colmap->column->data_type->attributes
                                            ->firstWhere('name', 'code')->pivot->value == '_taxon_')
                                            <option value="{{ $colmap->column_fk }}"
                                                @if(old('fields.'.$loop->parent->index, Arr::get($selected_attr, $loop->parent->index, 0)) == $colmap->column_fk)
                                                    selected
                                                @endif
                                                data-option-help="{{ $colmap->column->data_type->attributes
                                                    ->firstWhere('name', 'name_'.app()->getLocale())
                                                    ->pivot->value }}"
                                                @if($colmap->column->data_type->attributes
                                                    ->firstWhere('name', 'code')->pivot->value == '_relation_')
                                                    data-option-item-type="{{ $item_types->firstwhere('element_fk', $colmap->getConfigValue('item_type'))->value }}"
                                                @endif
                                            >
                                                {{ $colmap->column->translation->attributes
                                                    ->firstWhere('name', 'name_'.app()->getLocale())
                                                    ->pivot->value }}
                                            </option>
                                        @endunless
                                    @endforeach
                                </select>
                                <small id="fieldsSelectHelpBlock-{{ $loop->index }}" class="form-text text-muted">
                                </small>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    <tr><td colspan=3>
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td></tr>
                </table>

                <div class="form-group">
                <span>
                    @lang('import.attribute_hint')<br/>
                </span>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="headerCheckbox" name="header"
                            class="form-check-input" value=1 @if(old('header')) checked @endif
                        >
                        <label for="headerCheckbox" class="form-check-label">
                            @lang('import.contains_header')
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="uniqueTaxaCheckbox" name="unique_taxa"
                            class="form-check-input" value=1 @if(old('unique_taxa')) checked @endif
                        >
                        <label for="uniqueTaxaCheckbox" class="form-check-label">
                            @lang('import.unique_taxa')
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publicSelect">@lang('common.published')</label>
                    <select id="publicSelect" name="public"
                        aria-describedby="publicSelectHelpBlock" class="form-control" size=1
                    >
                        <option value="1"
                            @if(old('public') == 1) selected @endif>
                            @lang('common.yes')
                        </option>
                        <option value="0"
                            @if(old('public') == 0) selected @endif>
                            @lang('common.no')
                        </option>
                    </select>
                    <small id="publicSelectHelpBlock" class="form-text text-muted">
                        @lang('import.public_hint')<br/>
                    </small>
                    <span class="text-danger">{{ $errors->first('public') }}</span>
                </div>

                {{-- Input with autocomplete for parent item, despite the name of the include --}}
                @include('includes.form_taxon_autocomplete', [
                    'search_url' => route('item.autocomplete'),
                    'div_class' => 'form-group',
                    'name' => 'parent',
                    'input_placeholder' => '',
                    'input_label' => __('lists.parent'),
                    'input_help' =>  __('import.parent_hint') .' '. __('items.autocomplete_help'),
                    'null_label' => __('common.none'),
                    'taxon_name' => old('parent_name', __('common.none')),
                    'taxon_id' => old('parent'),
                ])

                <div class="form-group">
                    <label for="parentItemTypeSelect">@lang('import.parent_item_type')</label>
                    <select id="parentItemTypeSelect" name="parent_item_type"
                        aria-describedby="parentItemTypeSelectHelpBlock" class="form-control" size=1
                    >
                        @foreach($item_types as $type)
                            <option value="{{$type->element_fk}}"
                                @if(old('parent_item_type') == $type->element_fk) selected @endif>
                                {{$type->value}}
                            </option>
                        @endforeach
                    </select>
                    <small id="parentItemTypeSelectHelpBlock" class="form-text text-muted">
                        @lang('import.parent_item_type_hint')<br/>
                    </small>
                    <span class="text-danger">{{ $errors->first('parent_item_type') }}</span>
                </div>
                
                <!-- Form fields for geocoding -->
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="geocoderEnableCheckbox" name="geocoder_enable"
                            class="form-check-input" value=1 @if(old('geocoder_enable')) checked @endif
                        >
                        <label for="geocoderEnableCheckbox" class="form-check-label">
                            @lang('import.geocoder_use')
                        </label>
                    </div>
                </div>
                
                <fieldset id="geocoderFieldset" class="collapse @if(old('geocoder_enable'))show @endif">
                    <legend>@lang('import.geocoder_use')</legend>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="geocoderInteractiveCheckbox" name="geocoder_interactive"
                                class="form-check-input" value=1 @if(old('geocoder_interactive')) checked @endif
                            >
                            <label for="geocoderInteractiveCheckbox" class="form-check-label">
                                @lang('import.geocoder_interactive')
                            </label>
                        </div>
                    </div>
                
                    <div class="form-text">@lang('import.geocoder_hint')</div>
                    
                    <div class="form-group">
                        <label for="geocoderCountry">@lang('common.country')</label>
                        <select id="geocoderCountry" name="geocoder[country]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.country', $geocoder_attr['country']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.country') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderState">@lang('common.state')</label>
                        <select id="geocoderState" name="geocoder[state]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.state', $geocoder_attr['state']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.state') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderCounty">@lang('common.county')</label>
                        <select id="geocoderCounty" name="geocoder[county]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.county', $geocoder_attr['county']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.county') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderPostcode">@lang('common.postcode')</label>
                        <select id="geocoderPostcode" name="geocoder[postcode]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.postcode', $geocoder_attr['postcode']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.postcode') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderCity">@lang('common.city')</label>
                        <select id="geocoderCity" name="geocoder[city]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.city', $geocoder_attr['city']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.city') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderStreet">@lang('common.street')</label>
                        <select id="geocoderStreet" name="geocoder[street]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.street', $geocoder_attr['street']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.street') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderLocality">@lang('common.locality')</label>
                        <select id="geocoderLocality" name="geocoder[locality]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.locality', $geocoder_attr['locality']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.locality') }}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="geocoderDescription">@lang('common.description')</label>
                        <select id="geocoderDescription" name="geocoder[note]" class="form-control" size=1 >
                            @foreach($csv_data[0] as $csv_header)
                                <option value="{{$loop->index}}"
                                    @if(old('geocoder.note', $geocoder_attr['note']??null) == $loop->index) selected @endif>
                                        {{ $csv_header }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('geocoder.note') }}</span>
                    </div>
                </fieldset>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('import.import')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Triggered when checkbox for geocoder changed
    $('#geocoderEnableCheckbox').change(function(event) {
        if ($(this).prop('checked')) {
            $('#geocoderFieldset').collapse('show');
        }
        else {
            $('#geocoderFieldset').collapse('hide');
        }
    });

    // Triggered when select for columns changed
    $('.fields-select').change(function(event) {
        let fieldId = $(this).data('field-id');
        let helpText = $('#fieldsSelect-'+fieldId+' option:selected').data('option-help') || '';
        let itemType = $('#fieldsSelect-'+fieldId+' option:selected').data('option-item-type') || '';
        // Add localized name of item type if selected column has '_relation_' type
        if (itemType) {
            helpText += ': ' + itemType;
        }
        $('#fieldsSelectHelpBlock-'+fieldId).text(helpText);
    });
</script>

@endsection
