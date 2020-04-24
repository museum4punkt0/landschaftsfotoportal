@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('values.new')</h2>

<form action="{{ route('element.value.store', $element->element_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('values.value')</span>
        <input type="text" name="value" class="form-control" value="{{old('value')}}" />
        <span class="text-danger">{{ $errors->first('value') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.attribute')</span>
        <select name="attribute" class="form-control" size=1 >
        @foreach($attributes as $attribute)
            <option value="{{$attribute->attribute_id}}">{{$attribute->name}}</option>
        @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('attribute') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
