@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('lists.new')</h2>

<form action="{{ route('list.store') }}" method="POST">

    <div class="form-group">
        <span>@lang('common.name')</span>
        <input type="text" name="name" class="form-control" value="{{old('name')}}" />
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.description')</span>
        <input type="text" name="description" class="form-control" value="{{old('description')}}" />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="hierarchical" class="form-check-input" value=1 
                @if(old('hierarchical')) checked @endif />
            <span>@lang('lists.hierarchical')</span>
            <span class="text-danger">{{ $errors->first('hierarchical') }}</span>
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="internal" class="form-check-input" value=1 
                @if(old('internal')) checked @endif />
            <span>@lang('lists.internal_list')</span>
            <span class="text-danger">{{ $errors->first('internal') }}</span>
        </div>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
