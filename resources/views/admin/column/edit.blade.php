@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.edit')</h2>

<form action="{{ route('column.update', $column->column_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('common.description')</span>
        <input type="text" name="description" class="form-control" 
            value="{{ old('description', $column->description) }}" autofocus
        />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.translated_name')</span>
        <select name="translation" class="form-control" size=1 >
            @foreach($translations as $trans)
                <option value="{{$trans->element_fk}}"
                    @if(old('translation', $column->translation_fk) == $trans->element_fk) selected @endif >
                    {{ $trans->value }}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('translation') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.data_type')</span>
        <select name="data_type" class="form-control" size=1 >
            @foreach($data_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('data_type', $column->data_type_fk) == $type->element_fk) selected @endif >
                    {{ $type->value }}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('data_type') }}</span>
    </div>
    <div class="form-group collapse @if(old('data_type') == $data_type_ids['_list_'] || old('data_type') == $data_type_ids['_multi_list_'] || !old('data_type') && ($column->data_type_fk == $data_type_ids['_list_'] || $column->data_type_fk == $data_type_ids['_multi_list_']))show @endif" id="list_group">
        <span>@lang('lists.list')</span>
        <select name="list" class="form-control" size=1 >
            @foreach($lists as $list)
                <option value="{{$list->list_id}}"
                    @if(old('list', $column->list_fk) == $list->list_id) selected @endif>
                    {{$list->name}} ({{$list->description}})
                </option>
            @endforeach
        </select>
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
    // Triggered when select for 'data_type' changed
    $('.form-control[name=data_type]').change(function(event) {
        if ($(this).val() == {{ $data_type_ids['_list_'] }} || $(this).val() == {{ $data_type_ids['_multi_list_'] }}) {
            $('#list_group').collapse('show');
        }
        else {
            $('#list_group').collapse('hide');
        }
    });
</script>

@endsection
