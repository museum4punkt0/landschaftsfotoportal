@extends('layouts.app')

@section('content')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('comments.header')</div>
            <div class="card-body">
                <a href="{{route('comment.unpublished')}}" class="btn btn-primary">@lang('comments.unpublished')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1"></th>
                        <th colspan="1">@lang('comments.message')</th>
                        <th colspan="1">@lang('common.published')</th>
                        <th colspan="1">@lang('common.created')</th>
                        <th colspan="1">@lang('common.updated')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($comments as $comment)
                    <tr>
                        <td>
                            {{$comment->comment_id}}
                        </td>
                        <td>
                            <div class="container">
                            <a href="{{route('item.show.public', $comment->item->item_id)}}#details">
                            @if($comment->item->details->firstWhere(
                                'column_fk', $image_module->config['columns']['filename'] ?? 0))
                                <img class="img-fluid thumbnail-table"
                                    src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                        $comment->item->details->firstWhere(
                                            'column_fk', $image_module->config['columns']['filename'] ?? 0
                                        )->value_string) }}"
                                    alt=""
                                    title="{{ optional($comment->item->details->firstWhere(
                                        'column_fk', $image_module->config['columns']['caption'] ?? 0)
                                        )->value_string }}"
                                />
                            @endif
                            </a>
                            </div>
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
                            {{$comment->creator->name}} (@lang('users.group_'. $comment->editor->group->name)),<br/>
                            {{$comment->created_at}}
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
