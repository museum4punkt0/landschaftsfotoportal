@extends('layouts.app')

@section('content')

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('comments.unpublished')</div>
            <div class="card-body">
                <table class="table mt-4">
                <a href="{{route('comment.publish')}}" class="btn btn-primary">@lang('common.publish_all')</a>
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1"></th>
                        <th colspan="1">@lang('comments.message')</th>
                        <th colspan="1">@lang('common.published')</th>
                        <th colspan="1">@lang('common.updated')</th>
                        <th colspan="2">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($comments as $comment)
                    <tr>
                        <td>
                            {{$comment->comment_id}}
                        </td>
                        <td>
                            <div class="portfolio-item">
                            <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $comment->item->item_id)}}#details">
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $comment->item->details->firstWhere('column_fk', 13)->value_string .'.jpg') }}" height=100 alt="" title="{{ $comment->item->details->firstWhere('column_fk', 23)->value_string }}"/>
                            </a>
                            </div>
                        </td>
                        <td>
                            {{$comment->message}}
                        </td>
                        <td>
                            @if($comment->public)
                                @lang('common.yes')
                            @else
                                @lang('common.no')
                            @endif
                        </td>
                        <td>
                            {{$comment->editor->name}} (@lang('users.group_'. $comment->editor->group->name)),<br/>
                            {{$comment->updated_at}}
                        </td>
                        <td>
                            <a href="{{route('comment.publish', $comment->comment_id)}}" class="btn btn-primary">
                            @lang('common.publish')
                            </a>
                        </td>
                        <td>
                            <form action="{{route('comment.edit', $comment)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('comment.destroy', $comment)}}" method="POST">
                                {{ csrf_field() }}
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">@lang('common.delete')</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            {{ $comments->links() }}
        </div>
    </div>
</div>

@endsection
