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
            <div class="card-header">@lang('lists.header')</div>
            <div class="card-body">
                <a href="{{route('list.create')}}" class="btn btn-primary">@lang('lists.new')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('lists.hierarchical')</th>
                        <th colspan="3">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($lists as $list)
                    <tr>
                        <td>
                            {{$list->list_id}}
                        </td>
                        <td>
                            {{$list->name}}
                        </td>
                        <td>
                            {{$list->description}}
                        </td>
                        <td>
                            @if($list->hierarchical) @lang('common.yes') @else @lang('common.no') @endif
                        </td>
                        <td>
                            <form action="{{route('list.show', $list->list_id)}}" method="GET">
                                <button class="btn btn-primary" type="submit">@lang('common.show')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('list.edit', $list->list_id)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('list.destroy', $list->list_id)}}" method="POST">
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
