{{-- Don't include if we are using a backend route --}}
@unless (Route::currentRouteName() == 'item.edit')
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'edit_form',
        'section_heading' => '',
        'section_subheading' => '',
    ])
@endunless

@include('includes.modal_confirm_delete')
@include('includes.modal_image_large')

<div class="container">
@if ($errors->any())
    <div class="alert alert-danger">
        @lang('common.form_validation_error')
    </div>
@endif
@if (session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif

<h2>@lang('items.edit')</h2>
@if(isset($options['edit.revision']))
    <div class="form-group">
        <label for="comparedRevisionSelect">@lang('revisions.history')</label>
        <select
            id="comparedRevisionSelect"
            name="revisions"
            aria-describedby="compareSelectHelpBlock"
            class="form-control"
            data-current="{{ $item->revision }}"
            size=1
        >
        @foreach($item->item->revisions->sortByDesc('updated_at') as $rev)
            <option value="{{ $rev->revision }}">
                {{ $rev->updated_at }}, @lang('revisions.revision'): 
                @if($rev->revision < 0)@lang('revisions.draft')@endif{{ $rev->revision }},
                {{ $rev->editor->name }}
            </option>
        @endforeach
        </select>
        <small id="compareSelectHelpBlock" class="form-text text-muted">
            @lang('revisions.compare_select_help')
        </small>
    </div>
    <div class="text-muted">@lang('revisions.compare_help')</div>
    <hr>
    <!-- Handle diffs of item revisions -->
    <script type="text/javascript">
        // Triggered when document is ready
        $(document).ready(function () {
            itemDiff.init({{ $item->revision }});
        });
        // Triggered when revision select has changed
        $('#comparedRevisionSelect').change(function () {
            itemDiff.init({{ $item->revision }}, $('#comparedRevisionSelect option:selected').val());
        });
    </script>
@endif

<form id="itemEditForm" action="{{ route($options['route'], $item->original_item_id) }}" method="POST" enctype="multipart/form-data">
    
@if($options['edit.meta'])
    <div class="form-group">
        <label for="menuTitleInput">@lang('items.menu_title')</label>
        <input type="text" id="menuTitleInput" name="menu_title" class="form-control"
            data-column="-101" data-type="menu_title"
            value="{{old('menu_title', $item->menu_title)}}" maxlength="255" autofocus
        >
        <span class="text-danger">{{ $errors->first('menu_title') }}</span>
        @includeWhen(isset($options['edit.revision']), 'includes.form_history_meta', [
            'data_type' => 'menu_title', 'column_id' => -101
        ])
    </div>
    <div class="form-group">
        <label for="pageTitleInput">@lang('items.page_title')</label>
        <input type="text" id="pageTitleInput" name="page_title" class="form-control"
            data-column="-104" data-type="page_title"
            value="{{old('page_title', $item->page_title)}}" maxlength="1024" autofocus
        >
        <span class="text-danger">{{ $errors->first('page_title') }}</span>
        @includeWhen(isset($options['edit.revision']), 'includes.form_history_meta', [
            'data_type' => 'page_title', 'column_id' => -104
        ])
    </div>
    <div class="form-group">
        <label for="publicSelect">@lang('common.published')</label>
        <select id="publicSelect" name="public" class="form-control" data-column="-102" data-type="public" size=1>
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
        @includeWhen(isset($options['edit.revision']), 'includes.form_history_meta', [
            'data_type' => 'public', 'column_id' => -102
        ])
    </div>
    {{-- Input with autocomplete for parent item, despite the name of the include --}}
    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('item.autocomplete'),
        'div_class' => 'form-group',
        'name' => 'parent',
        'input_placeholder' => '',
        'input_label' => __('lists.parent'),
        'input_help' =>  __('items.autocomplete_help'),
        'null_label' => __('common.none'),
        'taxon_name' => old('parent_name', optional($item->parent)->title ?? __('common.none')),
        'taxon_id' => old('parent', $item->parent_fk),
    ])
    @includeWhen(isset($options['edit.revision']), 'includes.form_history_meta', [
        'data_type' => 'parent', 'column_id' => -103
    ])
    @if(config('ui.taxa'))
    <div class="form-group">
        <label for="taxonNameInput">@lang('taxon.list')</label>
        <input
            type="text"
            id="taxonNameInput"
            name="taxon_name"
            class="form-control"
            value="@if($taxon) {{$taxon->full_name}} ({{$taxon->native_name}}) @else @lang('common.none') @endif"
            readonly
        />
        <input type="hidden" name="taxon" value="{{optional($taxon)->taxon_id}}" />
    </div>
    @endif
@endif
    
    @foreach($colmap as $cm)
        {{-- Don't show columns which have auto generated content, e.g. image size/dimensions --}}
        @unless($cm->getConfigValue('editable') === false)
        
        @switch($cm->column->data_type_name)
            
            {{-- Data_type of form field is relation --}}
            @case('_relation_')
                {{-- Input with autocomplete for related item --}}
                @include('includes.form_item_autocomplete', [
                    'search_url' => route('item.autocomplete', ['item_type' => $cm->getConfigValue('relation_item_type')]),
                    'div_class' => 'form-group',
                    'column' => $cm->column->column_id,
                    'name' => 'fields',
                    'input_placeholder' => '',
                    'input_help' =>  __('items.autocomplete_help'),
                    'null_label' => __('common.none'),
                    'item_title' => old('fields_name.' . $cm->column->column_id,
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->related_item->title ?? __('common.none')),
                    'item_id' => old('fields.' . $cm->column->column_id,
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->related_item_fk),
                ])

                @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                    'data_type' => 'relation', 'column_id' => $cm->column->column_id
                ])
                @break

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
                        data-column="{{ $cm->column->column_id }}"
                        data-type="list"
                        size=1
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >
                        <option value="">@lang('common.choose')</option>
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id, 
                                    optional($details->firstWhere('column_fk', $cm->column->column_id))->element_fk) == 
                                     $element->element_id)
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
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'list', 'column_id' => $cm->column->column_id
                    ])
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
                        data-column="{{ $cm->column->column_id }}"
                        data-type="multi_list"
                        size=5
                        multiple
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(collect(old('fields.'. $cm->column->column_id, $details->firstWhere('column_fk', $cm->column->column_id)->elements()->pluck('element_id')->toArray()))->contains($element->element_id))
                                    selected
                                @endif
                            {{--
                            @if(old('fields.'. $cm->column->column_id))
                                @if(collect(old('fields.'. $cm->column->column_id))->contains($element->element_id))
                                    selected
                                @endif
                            @else
                                @if($details->firstWhere('column_fk', $cm->column->column_id)->elements()->get()->contains($element->element_id) == 
                                     $element->element_id)
                                    selected
                                @endif
                            @endif
                            --}}
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
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'multi_list', 'column_id' => $cm->column->column_id
                    ])
                </div>
                @break
            
            {{-- Data_type of form field is boolean --}}
            @case('_boolean_')
                <div class="form-group">
                    <div class="form-check">
                        <input type="hidden" name="fields[{{ $cm->column->column_id }}]" value=0 />
                        <input
                            type="checkbox"
                            id="fieldsInput-{{ $cm->column->column_id }}"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-check-input @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                            data-column="{{ $cm->column->column_id }}"
                            data-type="boolean"
                            value=1
                            @if(old('fields.'. $cm->column->column_id, 
                            optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int)) checked @endif
                            @if($loop->first && !$options['edit.meta']) autofocus @endif
                        />
                        @include('includes.column_label', ['css_class' => 'form-check-label'])
                    </div>
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'int', 'column_id' => $cm->column->column_id
                    ])
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input
                        type="text"
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control {{ $cm->getConfigValue('data_subtype') }} @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        data-column="{{ $cm->column->column_id }}"
                        data-type="integer"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        value="{{ old('fields.'. $cm->column->column_id, 
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->value_int) }}"
                        @if($cm->getConfigValue('editable') == 'readonly' && !$options['edit.meta']) readonly @endif
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'int', 'column_id' => $cm->column->column_id
                    ])
                </div>
                @break
            
            {{-- Data_type of form field is float --}}
            @case('_float_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <input
                        type="text"
                        id="fieldsInput-{{ $cm->column->column_id }}"
                        name="fields[{{ $cm->column->column_id }}]"
                        aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                        class="form-control {{ $cm->getConfigValue('data_subtype') }} @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                        data-column="{{ $cm->column->column_id }}"
                        data-type="float"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        value="{{ old('fields.'. $cm->column->column_id, 
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->value_float) }}"
                        @if($cm->getConfigValue('editable') == 'readonly' && !$options['edit.meta']) readonly @endif
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'float', 'column_id' => $cm->column->column_id
                    ])
                </div>
                @break
            
            {{-- Data_type of form field is string --}}
            @case('_string_')
            {{-- Data_type of form field is (menu) title --}}
            @case('_title_')
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
                            data-column="{{ $cm->column->column_id }}"
                            data-type="textarea"
                            placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                            rows="{{$cm->getConfigValue('textarea')}}"
                            @if($loop->first && !$options['edit.meta']) autofocus @endif
                        >{{
                            old('fields.'. $cm->column->column_id, 
                                optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string)
                        }}</textarea>
                    @else
                        <input
                            type="text"
                            id="fieldsInput-{{ $cm->column->column_id }}"
                            name="fields[{{ $cm->column->column_id }}]"
                            aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                            class="form-control {{ $cm->getConfigValue('data_subtype') }}@if($cm->getConfigValue('search') == 'address') autocomplete @endif @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                            data-column="{{ $cm->column->column_id }}"
                            data-type="string"
                            placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}" 
                            value="{{ old('fields.'. $cm->column->column_id, 
                            optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
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
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'string', 'column_id' => $cm->column->column_id
                    ])
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
                        data-column="{{ $cm->column->column_id }}"
                        data-type="string"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        rows="{{ $cm->getConfigValue('textarea') ?? 5 }}"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    >{!!
                        old('fields.'. $cm->column->column_id, 
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string)
                    !!}</textarea>
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'string', 'column_id' => $cm->column->column_id
                    ])
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('.summernote').summernote({
                            toolbar: [
                                ['style', ['bold', 'italic', 'underline', 'clear']],
                                ['font', ['strikethrough', 'superscript', 'subscript']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol']],
                                ['table', ['table']],
                                ['insert', ['link', 'picture', 'video']],
                                ['view', ['fullscreen', 'codeview', 'help']],
                            ],
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
                        data-column="{{ $cm->column->column_id }}"
                        data-type="string"
                        placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                        value="{{ old('fields.'. $cm->column->column_id, 
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'string', 'column_id' => $cm->column->column_id
                    ])
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
                        data-column="{{ $cm->column->column_id }}"
                        data-type="date"
                        min="{{ $cm->getDateBoundConfig('min') }}"
                        max="{{ $cm->getDateBoundConfig('max') }}"
                        value="{{ old('fields.'. $cm->column->column_id, 
                        optional($details->firstWhere('column_fk', $cm->column->column_id))->value_date) }}"
                        @if($loop->first && !$options['edit.meta']) autofocus @endif
                    />
                    
                    @include('includes.form_input_help')
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'date', 'column_id' => $cm->column->column_id
                    ])
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
                            @if(old('date_type') == 'point' || !old('date_type') && $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from() == $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to())
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
                            @if(old('date_type') == 'period' || !old('date_type') && $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from() != $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to())
                                checked
                            @endif
                        >
                        <label class="form-check-label" for="datePeriodRadio-{{ $cm->column->column_id }}">@lang('common.date_period')</label>
                    </div>
                    <div class="row">
                    <!-- Form field for the date (of beginning) -->
                        <div class="col-12 col-sm-6 date" data-column="{{ $cm->column->column_id }}">
                            <label for="fieldsInput-{{ $cm->column->column_id }}">@lang('common.date_period_start')</label>
                            <input
                                type="date"
                                id="fieldsInput-{{ $cm->column->column_id }}"
                                name="fields[{{ $cm->column->column_id }}][start]"
                                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                                class="form-control @if($errors->has('fields.'.$cm->column->column_id.'.start')) is-invalid @endif"
                                data-column="{{ $cm->column->column_id }}"
                                data-type="daterange"
                                min="{{ $cm->getDateBoundConfig('min') }}"
                                max="{{ $cm->getDateBoundConfig('max') }}"
                                value="{{ old('fields.'. $cm->column->column_id .'.start', 
                                optional($details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from())->toDateString()) }}"
                            />
                            <span class="text-danger">
                                {{ $errors->first('fields.'. $cm->column->column_id .'.start') }}
                            </span>
                        </div>
                    <!-- Form fields for the date (of ending) -->
                    @if(old('date_type') == 'period' || !old('date_type') && $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->from() != $details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to())
                        <div class="col-12 col-sm-6 collapse show date date-period" data-column="{{ $cm->column->column_id }}">
                    @else
                        <div class="col-12 col-sm-6 collapse date date-period" data-column="{{ $cm->column->column_id }}">
                    @endif
                            <label for="fieldsInput-{{ $cm->column->column_id }}-end">@lang('common.date_period_end')</label>
                            <input
                                type="date"
                                id="fieldsInput-{{ $cm->column->column_id }}-end"
                                name="fields[{{ $cm->column->column_id }}][end]"
                                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }} fieldsFeedback-{{ $cm->column->column_id }}"
                                class="form-control @if($errors->has('fields.'.$cm->column->column_id.'.end')) is-invalid @endif"
                                data-column="{{ $cm->column->column_id }}"
                                data-type="daterange"
                                min="{{ $cm->getDateBoundConfig('min') }}"
                                max="{{ $cm->getDateBoundConfig('max') }}"
                                data-msg-invalid="@lang('common.invalid_daterange')"
                                value="{{ old('fields.'. $cm->column->column_id .'.end', 
                                optional($details->firstWhere('column_fk', $cm->column->column_id)->value_daterange->to())->toDateString()) }}"
                            />
                            <span class="text-danger">
                                {{ $errors->first('fields.'. $cm->column->column_id .'.end') }}
                            </span>
                        </div>
                    </div>
                    <span id="fieldsFeedback-{{ $cm->column->column_id }}" class="text-danger"></span>
                    @include('includes.form_input_help')
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'daterange', 'column_id' => $cm->column->column_id
                    ])
                </div>
                <script type="text/javascript">
                    // Triggered when radio changed
                    $('.form-check input[name=date_type][value=point]').click(function(event) {
                        var column = $(this).data('column');
                        //alert('point ' + column);
                        $('.date-period[data-column='+column+']').collapse('hide');
                        // Set end date to the same as start date
                        $('#fieldsInput-'+column+'-end').val($('#fieldsInput-'+column).val());
                        // Remove invalid decoration of input field
                        $('#fieldsFeedback-'+column).text('');
                        $('#fieldsInput-'+column+'-end').removeClass('is-invalid');
                    });
                    $('.form-check input[name=date_type][value=period]').click(function(event) {
                        var column = $(this).data('column');
                        //alert('period ' + column);
                        $('.date-period').filter(function () {
                            return $(this).data("column") === column;
                        }).collapse('show');
                    });
                    // Triggered when date was edited
                    $('.date [type=date]').change(function(event) {
                        var column = $(this).data('column');
                        //alert('date changed ' + column + ': ' + $(this).val());
                        // Check period, otherwise set end date to start date
                        if ($('#datePeriodRadio-'+column).prop('checked')) {
                            checkValidDateRange(column);
                        }
                        else {
                            $('#fieldsInput-'+column+'-end').val($('#fieldsInput-'+column).val());
                        }
                    });
                    $('form').submit(function(event) {
                        var errors = 0;
                        $('.date-period').each(function () {
                            //console.log($(this).data('column'));
                            errors += checkValidDateRange($(this).data('column'));
                        });
                        if(errors) {
                            // Prevent submitting the form
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                    // Check for end date to be later than start date
                    function checkValidDateRange(column) {
                        var start = new Date($('#fieldsInput-'+column).val());
                        var end = new Date($('#fieldsInput-'+column+'-end').val());
                        if ((end.getTime() - start.getTime()) < 0) {
                            var error_message = $('#fieldsInput-'+column+'-end').data('msg-invalid');
                            // Decorate input as invalid
                            $('#fieldsFeedback-'+column).text(error_message);
                            $('#fieldsInput-'+column+'-end').addClass('is-invalid');
                            $('#fieldsInput-'+column+'-end').focus();
                            return 1;
                        }
                        else {
                            // Remove invalid decoration of input field
                            $('#fieldsFeedback-'+column).text('');
                            $('#fieldsInput-'+column+'-end').removeClass('is-invalid');
                            return 0;
                        }
                    }
                </script>
                @break
            
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    @include('includes.column_label')
                    
                    <div class="form-row">
                        <div class="col">
                        @if($cm->getConfigValue('image_show') == 'preview' || $cm->getConfigValue('image_show') == 'filename')
                            @if(Storage::exists('public/'. Config::get('media.preview_dir') .
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                                {{ $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}
                                <br/>
                                <a href="#" data-toggle="modal" data-target="#imageLargeModal"
                                    data-img-source="{{ asset('storage/'. Config::get('media.medium_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}">
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                />
                                </a>
                            @else
                                @lang('columns.image_not_available')
                            @endif

                            {{-- 2nd image for compared revision --}}
                            @if(isset($options['edit.revision']))
                            </div>
                            <div class="col">
                                <span id="comparedRevisionImageFilename-{{ $cm->column->column_id }}">
                                    {{ $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}
                                </span>
                                <br/>
                                <a href="#" data-toggle="modal" data-target="#imageLargeModal"
                                    id="comparedRevisionImageLink-{{ $cm->column->column_id }}"
                                    data-img-source="{{ asset('storage/'. Config::get('media.medium_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                    data-path="{{ asset('storage/'. Config::get('media.medium_dir')) }}/"
                                >
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                    id="comparedRevisionImage-{{ $cm->column->column_id }}"
                                    data-path="{{ asset('storage/'. Config::get('media.preview_dir')) }}/"
                                />
                                </a>
                            @endif
                        @endif
                        </div>
                        <div class="col">
                            <input
                                type="hidden"
                                id="fieldsInput-{{ $cm->column->column_id }}-filename"
                                name="fields[{{ $cm->column->column_id }}][filename]"
                                value="{{
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string }}"
                            />
                            <input
                                type="file"
                                id="fieldsInput-{{ $cm->column->column_id }}"
                                name="fields[{{ $cm->column->column_id }}][file]"
                                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                                class="form-control-file @if($errors->has('fields.'.$cm->column->column_id.'.file')) is-invalid @endif"
                                data-column="{{ $cm->column->column_id }}"
                                data-type="image"
                                accept=".jpg, .jpeg"
                                @if($loop->first && !$options['edit.meta']) autofocus @endif
                            />
                            @include('includes.form_input_help')
                            <span class="form-text text-muted">@lang('items.file_max_size', ['max' => config('media.image_max_size', 2048)]) @lang('columns.image_hint')</span>
                        </div>
                    </div>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id .'.file') }}</span>
                    @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                        'data_type' => 'string', 'column_id' => $cm->column->column_id
                    ])
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
                            <input
                                type="url"
                                id="fieldsInput-{{ $cm->column->column_id }}"
                                name="fields[{{ $cm->column->column_id }}]"
                                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                                class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                                data-column="{{ $cm->column->column_id }}"
                                data-type="string"
                                placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                                value="{{ old('fields.'. $cm->column->column_id, 
                                optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
                                @if($loop->first && !$options['edit.meta']) autofocus @endif
                            />
                            <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                            @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                                'data_type' => 'string', 'column_id' => $cm->column->column_id
                            ])
                            
                            <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                                src="{{ old('fields.'. $cm->column->column_id, 
                                optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
                            >
                        @endif
                        @if($cm->getConfigValue('map_iframe') == 'service')
                            <input
                                type="text"
                                name="fields-info[{{ $cm->column->column_id }}]"
                                class="form-control"
                                value="{{ Config::get('media.mapservice_url') . 'artid=' }}"
                                readonly
                            />
                            <input
                                type="text"
                                id="fieldsInput-{{ $cm->column->column_id }}"
                                name="fields[{{ $cm->column->column_id }}]"
                                aria-describedby="fieldsHelpBlock-{{ $cm->column->column_id }}"
                                class="form-control @if($errors->has('fields.'.$cm->column->column_id)) is-invalid @endif"
                                data-column="{{ $cm->column->column_id }}"
                                data-type="string"
                                placeholder="{{ optional($placeholders->firstWhere('element_fk', $cm->column->translation_fk))->value }}"
                                value="{{ old('fields.'. $cm->column->column_id,
                                    optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
                                @if($loop->first && !$options['edit.meta']) autofocus @endif
                            />
                            <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                            @includeWhen(isset($options['edit.revision']), 'includes.form_history_detail', [
                                'data_type' => 'string', 'column_id' => $cm->column->column_id
                            ])
                            
                            <iframe width="100%" height="670px" scrolling="no" marginheight="0" marginwidth="0" frameborder="0"
                                src="{{ Config::get('media.mapservice_url') }}artid={{ old('fields.'. $cm->column->column_id, 
                                optional($details->firstWhere('column_fk', $cm->column->column_id))->value_string) }}"
                            >
                        @endif
                        <p>@lang('items.no_iframe')</p>
                        </iframe>
                    @else
                        <span>detail column {{$cm->column->column_id}} for map not found</span>
                    @endif
                @endif
                @if($cm->getConfigValue('map') == 'inline')
                    <div id="map" class="map"
                        data-colmap="{{ $cm->colmap_id }}"
                        data-item="{{ $item->item_id }}"
                        data-map-config="{{ route('map.config', ['colmap' => $cm->colmap_id]) }}"
                        data-forward-geocoder-url="{{ Config::get('geo.geocoder_url') }}"
                        data-reverse-geocoder-url="{{ Config::get('geo.reverse_geocoder_url') }}"
                        data-api-key="{{ Config::get('geo.api_key') }}"
                        data-image-path="{{ asset('storage/images/') }}"
                        @if($item instanceof \App\ItemRevision)
                            data-revision="{{ $item->item_id }}"
                        @endif
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
        <button type="submit" aria-describedby="saveRevisionBtnHelpBlock" class="btn btn-primary">
            @lang('common.save')
        </button>
        @if(Config::get('ui.revisions'))
            {{-- Show button for deleting this draft revision --}}
            @if($item->revision < 0)
                <button type="button" class="btn btn-danger" data-toggle="modal"
                    data-target="#confirmDeleteModal"
                    data-href="{{ route('revision.destroy.draft', $item) }}"
                    data-message="@lang('revisions.confirm_delete')"
                    data-title="@lang('revisions.delete_draft')"
                    title="@lang('revisions.delete_draft')"
                >
                    @lang('revisions.delete_draft')
                </button>
                <small id="saveRevisionBtnHelpBlock" class="form-text text-muted">@lang('revisions.save_delete_drafts_help')</small>
            @else
                <small id="saveRevisionBtnHelpBlock" class="form-text text-muted">@lang('revisions.save_revision_help')</small>
            @endif
        @endif
    @else
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal" data-form-id="itemEditForm">@lang('common.save')</button>
        @if(Config::get('ui.revisions'))
            @if($item->revision < 0)
                {{-- Show button for deleting this draft revision --}}
                <button type="button" class="btn btn-danger" data-toggle="modal"
                    data-target="#confirmDeleteModal"
                    data-href="{{ route('item.destroy.draft', $item->original_item_id) }}"
                    data-message="@lang('revisions.confirm_delete')"
                    data-title="@lang('revisions.delete_draft')"
                    title="@lang('revisions.delete_draft')"
                >
                    @lang('revisions.delete_draft')
                </button>
            @endif
        @endif
        @include('includes.modal_upload')
    @endif
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

</div>

{{-- Don't include if we are using a backend route --}}
@unless (Route::currentRouteName() == 'item.edit')
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
@endunless
