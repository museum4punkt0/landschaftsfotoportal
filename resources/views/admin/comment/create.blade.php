@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('comments.new')</h2>

<form action="{{ route('item.comment.store', $item->item_id) }}" method="POST">
    
    <div class="form-group">
        <label for="messageInput">@lang('comments.message')</label>
        <input type="text" id="messageInput" name="message" class="form-control"
            value="{{old('message')}}" maxlength="4095" autofocus
        />
        <span class="text-danger">{{ $errors->first('message') }}</span>
    </div>
    <div class="form-group">
        <label for="publicSelect">@lang('common.published')</label>
        <select id="publicSelect" name="public" class="form-control" size=1>
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
