@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('comments.edit')</h2>

<form action="{{ route('comment.update', $comment->comment_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('comments.message')</span>
        <input type="text" name="message" class="form-control" value="{{old('message', $comment->message)}}" />
        <span class="text-danger">{{ $errors->first('message') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public', $comment->public) == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public', $comment->public) == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@endsection
