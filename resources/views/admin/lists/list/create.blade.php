@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('lists.new')</h2>

<form action="{{ route('list.store') }}" method="POST">

    <div class="form-group">
        <label for="nameInput">@lang('common.name')</label>
        <input type="text" id="nameInput" name="name" class="form-control" value="{{old('name')}}" autofocus>
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <label for="descriptionInput">@lang('common.description')</label>
        <input type="text" id="descriptionInput" name="description" class="form-control"
            value="{{old('description')}}"
        >
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" id="hierarchicalCheckbox" name="hierarchical"
                class="form-check-input" value=1 @if(old('hierarchical')) checked @endif
            >
            <label for="hierarchicalCheckbox">@lang('lists.hierarchical')</label>
            <span class="text-danger">{{ $errors->first('hierarchical') }}</span>
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" id="internalCheckbox" name="internal"
                class="form-check-input" value=1 @if(old('internal')) checked @endif
            >
            <label for="internalCheckbox">@lang('lists.internal_list')</label>
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
