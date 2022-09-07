@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.edit')</h2>

<form action="{{ route('column.update', $column->column_id) }}" method="POST">
    
    <div class="form-group">
        <label for="descriptionInput">@lang('common.description')</label>
        <input type="text" id="descriptionInput" name="description" class="form-control"
            value="{{ old('description', $column->description) }}" maxlength="255" autofocus
        />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <label for="translationSelect">@lang('columns.translated_name')</label>
        <select id="translationSelect" name="translation" aria-describedby="translationHelpBlock"
            class="form-control" size=1>
            @foreach($translations as $trans)
                <option value="{{$trans->element_fk}}"
                    @if(old('translation', $column->translation_fk) == $trans->element_fk) selected @endif >
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
            class="form-control" size=1 >
            @foreach($data_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('data_type', $column->data_type_fk) == $type->element_fk) selected @endif >
                    {{ $type->value }}
                </option>
            @endforeach
        </select>
        <small id="dataTypeHelpBlock" class="form-text text-muted">
            @lang('columns.data_type_help')
        </small>
        <span class="text-danger">{{ $errors->first('data_type') }}</span>
    </div>
    <div class="form-group collapse @if(old('data_type') == $data_type_ids['_list_'] || old('data_type') == $data_type_ids['_multi_list_'] || !old('data_type') && ($column->data_type_fk == $data_type_ids['_list_'] || $column->data_type_fk == $data_type_ids['_multi_list_']))show @endif" id="listSelectGroup">
        <label for="listSelect">@lang('lists.list')</label>
        <select id="listSelect" name="list" aria-describedby="listHelpBlock" class="form-control" size=1>
            @foreach($lists as $list)
                <option value="{{$list->list_id}}"
                    @if(old('list', $column->list_fk) == $list->list_id) selected @endif>
                    {{$list->name}} ({{$list->description}})
                </option>
            @endforeach
        </select>
        <small id="listHelpBlock" class="form-text text-muted">
            @lang('columns.list_help')
        </small>
        <span class="text-danger">{{ $errors->first('list') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
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
</script>

@endsection
