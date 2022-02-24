@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('elements.new_batch')</h2>

<form action="{{ route('list.element.store_batch', $list->list_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('elements.multivalues')<br>{{$example_value}}</span>
        <textarea name="multivalues" class="form-control" id="multivalue" rows="10"></textarea>
        <span class="text-danger">{{ $errors->first('multivalues') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
