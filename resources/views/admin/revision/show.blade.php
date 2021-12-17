@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    <h2>@lang('revisions.list')</h2>
    <div class="my-4">
        <a href="{{route('item.show.public', $item->item_fk)}}" class="btn btn-primary">
            @lang('revisions.show_frontend')
        </a>
    </div>

    @include('includes.item_show_cards')

    @if(env('APP_DEBUG'))
        [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
    @endif

</div>

@endsection
