@extends('layouts.app')

@section('content')

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
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('items.item_type')</th>
                        <th colspan="1">@lang('common.name')</th>
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
                            @foreach($item->item_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            Typ-ID {{$item->item_type_fk}}
                        </td>
                        <td>
                            {{$item->getTitleColumn()}}
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
    </div>                         
</div>

@endsection
