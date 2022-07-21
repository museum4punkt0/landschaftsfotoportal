@extends('layouts.app')

@section('content')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('comments.header')</div>
            <div class="card-body">
                @lang('items.list'): <a href="{{route('item.show', $item->item_id)}}">{{ $item->title }}</a>
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
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
                            {{$comment->message}}
                        </td>
                        <td>
                            @switch($comment->public)
                                @case(1)
                                    @lang('comments.state_published')
                                    @break
                                @case(0)
                                    @lang('comments.state_unpublished')
                                    @break
                                @case(-1)
                                    @lang('comments.state_locked')
                                    @break
                            @endswitch
                        </td>
                        <td>
                            {{$comment->editor->name}} (@lang('users.group_'. $comment->editor->group->name)),<br/>
                            {{$comment->updated_at}}
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
