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
            <div class="card-header">@lang('attributes.header')</div>
            <div class="card-body">
                <a href="{{route('attribute.create')}}" class="btn btn-primary">@lang('attributes.new')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="2">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($attributes as $attr)
                    <tr>
                        <td>
                            {{$attr->attribute_id}}
                        </td>
                        <td>
                            {{$attr->name}}
                        </td>
                        <td>
                            <form action="{{route('attribute.edit', $attr)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('attribute.destroy', $attr)}}" method="POST">
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
            {{ $attributes->links() }}
        </div>
    </div>
</div>

@endsection
