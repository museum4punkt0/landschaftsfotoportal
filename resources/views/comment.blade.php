@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

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

@include('includes.modal_alert')
@include('includes.modal_comment_edit')
@include('includes.modal_comment_delete')

<!-- My comments table -->
<div class="page-section bg-light">
<div class="container">
    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('comments.my_own')</div>
            <div class="card-body">
                <table class="table mt-4">
                <thead>
                    <tr>
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
                            {{$comment->updated_at}}
                        </td>
                        <td>
                            <!-- Icon and button for editing -->
                            <div style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('ajax.comment.update', $comment->comment_id) }}" data-message="{{ $comment->message }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-pencil-alt fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td>
                            <!-- Icons and button for deleting -->
                            <div style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#commentDeleteModal" data-href="{{ route('ajax.comment.destroy', $comment->comment_id) }}" title="@lang('common.delete')">
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas fa-trash fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
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
</div>

@endsection
