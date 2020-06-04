@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('attributes.edit')</h2>

<form action="{{ route('attribute.update', $attribute) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('common.name')</span>
        <input type="text" name="name" class="form-control" value="{{ old('name', $attribute->name) }}" />
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection