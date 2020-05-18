@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.edit')</h2>

<form action="{{ route('column.update', $column->column_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('common.description')</span>
        <input type="text" name="description" class="form-control" value="{{$column->description}}" />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.translated_name')</span>
        <select name="translation" class="form-control" size=1 >
            @foreach($translations as $trans)
                <option value="{{$trans->element_id}}"
                    @if($column->translation_fk == $trans->element_id) selected @endif>
                    @foreach($trans->values as $v)
                        {{$v->value}}, 
                    @endforeach
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('translation') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.data_type')</span>
        <select name="data_type" class="form-control" size=1 >
            @foreach($data_types as $type)
                <option value="{{$type->element_id}}"
                    @if($column->data_type_fk == $type->element_id) selected @endif>
                    @foreach($type->values as $v)
                        {{$v->value}}, 
                    @endforeach
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('data_type') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.list')</span>
        <select name="list" class="form-control" size=1 >
            <option value="">@lang('common.ignore')</option>
            @foreach($lists as $list)
                <option value="{{$list->list_id}}"
                    @if($column->list_fk == $list->list_id) selected @endif>
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

@endsection
