@extends('layouts.app')

@section('content')

<div class="container">
<h1>@lang('lists.edit')</h1>

<form action="{{ route('list.update', $list->list_id) }}" method="POST">

    <div class="form-group">
        <span>@lang('common.name')</span>
        <input type="text" name="name" class="form-control" value="{{$list->name }}" />
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.description')</span>
        <input type="text" name="description" class="form-control" value="{{$list->description }}" />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.hierarchical')</span>
        <input type="hidden" name="hierarchical" value=0 />
        <input type="checkbox" name="hierarchical" class="form-control" value=1 @if($list->hierarchical) checked @endif />
        <span class="text-danger">{{ $errors->first('hierarchical') }}</span>
    </div>

    <div class="form-group">
        <button type="submit" name="update" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@stop
