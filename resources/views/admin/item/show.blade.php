@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    <h2>@lang('items.list')</h2>
    <div class="my-4">
        <a href="{{route('item.show.public', $item->item_id)}}" class="btn btn-primary">
        @lang('items.show_frontend')
        </a>
        <a href="{{route('item.edit', $item->item_id)}}" class="btn btn-primary">
        @lang('common.edit')
        </a>
        @unless(config('ui.revisions'))
            @unless($item->public)
                <a href="{{route('item.publish', $item->item_id)}}" class="btn btn-primary">
                @lang('common.publish')
                </a>
            @endunless
        @endunless
    </div>

    @include('includes.item_show_cards')

    @if(env('APP_DEBUG'))
        [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
    @endif

</div>

@endsection
