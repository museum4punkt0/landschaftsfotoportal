@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('comments.new')</h2>

<form action="{{ route('item.comment.store', $item->item_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('comments.message')</span>
        <input type="text" name="message" class="form-control" value="{{old('message')}}" />
        <span class="text-danger">{{ $errors->first('message') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="0"
                @if(old('public') == 0) selected @endif>
                @lang('comments.state_unpublished')
            </option>
            <option value="-1"
                @if(old('public') == -1) selected @endif>
                @lang('comments.state_locked')
            </option>
            <option value="1"
                @if(old('public') == 1) selected @endif>
                @lang('comments.state_published')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
