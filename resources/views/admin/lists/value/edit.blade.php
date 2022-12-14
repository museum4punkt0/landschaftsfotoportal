@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('values.edit')</h2>

<form action="{{ route('value.update', $value->value_id) }}" method="POST">
    
    <div class="form-group">
        <label for="valueInput">@lang('values.value')</label>
        <input type="text" id="valueInput" name="value" class="form-control"
            value="{{$value->value}}" maxlength="4095" autofocus
        >
        <span class="text-danger">{{ $errors->first('value') }}</span>
    </div>
    <div class="form-group">
        <label for="attributeSelect">@lang('lists.attribute')</label>
        <select id="attributeSelect" name="attribute" class="form-control" size=1>
        @foreach($attributes as $attribute)
            <option value="{{$attribute->attribute_id}}"
                @if(old('attribute', $value->attribute_fk) == $attribute->attribute_id) selected @endif >
                {{$attribute->name}}
            </option>
        @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('attribute') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
