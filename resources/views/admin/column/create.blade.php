@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.new')</h2>

<form action="{{ route('column.store') }}" method="POST">
    
    <div class="form-group">
        <label for="descriptionInput">@lang('common.description')</label>
        <input type="text" id="descriptionInput" name="description" aria-describedby="descriptionHelpBlock"
            class="form-control" value="{{old('description')}}" maxlength="255" autofocus
        />
        <small id="descriptionHelpBlock" class="form-text text-muted">
            @lang('columns.description_help')
        </small>
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <label for="translationSelect">@lang('columns.translated_name')</label>
        <select id="translationSelect" name="translation" aria-describedby="translationHelpBlock"
            class="form-control" size=1>
            @foreach($translations as $trans)
                <option value="{{$trans->element_fk}}"
                    @if(old('translation') == $trans->element_fk) selected @endif>
                    {{ $trans->value }}
                </option>
            @endforeach
            <option value="-1" @if(old('translation') == -1) selected @endif>
                --- @lang('common.new') ---
            </option>
        </select>
        <small id="translationHelpBlock" class="form-text text-muted">
            @lang('columns.translated_name_help', ['new' => __('common.new')])
        </small>
        <span class="text-danger">{{ $errors->first('translation') }}</span>
    </div>
    <div class="form-group collapse @if(old('translation') == -1)show @endif" id="translationInputGroup">
        <label for="translationInput">@lang('columns.new_translation')</label>
        <input type="hidden" name="lang" value="{{$attribute->attribute_id}}" />
        <input
            type="text"
            id="translationInput"
            name="new_translation"
            class="form-control"
            value="{{old('new_translation')}}"
        />
        <span class="text-danger">{{ $errors->first('new_translation') }}</span>
    </div>

    <div class="form-group">
        <label for="dataTypeSelect">@lang('columns.data_type')</label>
        <select id="dataTypeSelect" name="data_type" aria-describedby="dataTypeHelpBlock"
            class="form-control" size=1>
            @foreach($data_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('data_type') == $type->element_fk) selected @endif>
                    {{ $type->value }}
                </option>
            @endforeach
        </select>
        <small id="dataTypeHelpBlock" class="form-text text-muted">
            @lang('columns.data_type_help')
        </small>
        <span class="text-danger">{{ $errors->first('data_type') }}</span>
    </div>

    <div class="form-group collapse @if(old('data_type') == $data_type_ids['_list_'] || old('data_type') == $data_type_ids['_multi_list_'] || !old('data_type'))show @endif" id="listSelectGroup">
        <label for="listSelect">@lang('lists.list')</label>
        <select id="listSelect" name="list" aria-describedby="listHelpBlock" class="form-control" size=1>
            @foreach($lists as $list)
                <option value="{{$list->list_id}}"
                    @if(old('list') == $list->list_id) selected @endif>
                    {{$list->name}} ({{$list->description}})
                </option>
            @endforeach
        </select>
        <small id="listHelpBlock" class="form-text text-muted">
            @lang('columns.list_help')
        </small>
        <span class="text-danger">{{ $errors->first('list') }}</span>
    </div>
    
    <!-- Form fields for optional creating column mapping -->
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" id="colmapEnableCheckbox" name="colmap_enable" aria-describedby="colmapEnableHelpBlock"
                class="form-check-input" value=1 @if(old('colmap_enable')) checked @endif
            >
            <label for="colmapEnableCheckbox" class="form-check-label">
                @lang('columns.add_colmap')
            </label>
        </div>
        <small id="colmapEnableHelpBlock" class="form-text text-muted">
            @lang('columns.add_colmap_help')
        </small>
    </div>
    
    <fieldset id="colmapFieldset" class="collapse @if(old('colmap_enable'))show @endif">
        <legend>@lang('colmaps.new')</legend>

        @include('includes.colmap_create_fields')

    </fieldset>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

<script type="text/javascript">
    // Triggered when select for 'translation' changed
    $('#translationSelect').change(function(event) {
        if ($(this).val() == -1) {
            $('#translationInputGroup').collapse('show');
        }
        else {
            $('#translationInputGroup').collapse('hide');
        }
    });

    // Triggered when select for 'data_type' changed
    $('#dataTypeSelect').change(function(event) {
        if ($(this).val() == {{ $data_type_ids['_list_'] }} || $(this).val() == {{ $data_type_ids['_multi_list_'] }}) {
            $('#listSelectGroup').collapse('show');
        }
        else {
            $('#listSelectGroup').collapse('hide');
        }
    });

    // Triggered when checkbox for creating column mapping changed
    $('#colmapEnableCheckbox').change(function(event) {
        if ($(this).prop('checked')) {
            $('#colmapFieldset').collapse('show');
        }
        else {
            $('#colmapFieldset').collapse('hide');
        }
    });

</script>

@endsection
