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
            <div class="card-header">@lang('items.header')</div>
            <div class="card-body">
                <a href="{{route('item.new')}}" class="btn btn-primary">@lang('items.new')</a>
                <a href="{{route('item.unpublished')}}" class="btn btn-primary">@lang('items.unpublished')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1"></th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('items.item_type')</th>
                        <th colspan="3">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            {{$item->item_id}}
                        </td>
                        <td>
                            <div class="portfolio-item">
                            <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item_id)}}#details">
                            @if($item->details->firstWhere('column_fk', 13))
                                <img src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->details->firstWhere('column_fk', 13)->value_string) }}" height=100 alt="" title="{{ $item->details->firstWhere('column_fk', 23)->value_string }}"/>
                            @endif
                            </a>
                            </div>
                        </td>
                        <td>
                            <a href="{{route('item.show.public', $item->item_id)}}"
                                title="@lang('items.show_frontend')">
                                {{$item->title}}
                            </a>
                        </td>
                        <td>
                            @foreach($item->item_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            Typ-ID {{$item->item_type_fk}}
                        </td>
                        <td>
                            <form action="{{route('item.show', $item->item_id)}}" method="GET">
                                <button class="btn btn-primary" type="submit">@lang('common.show')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('item.edit', $item)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('item.destroy', $item)}}" method="POST">
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
            {{ $items->links() }}
        </div>
    </div>
</div>

@endsection
