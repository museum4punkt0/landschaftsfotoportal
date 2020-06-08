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
            <div class="card-header">@lang('colmaps.header')</div>
            <div class="card-body">
                <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('columns.list')</th>
                        <th colspan="1">@lang('taxon.list')</th>
                        <th colspan="1">@lang('colmaps.item_type')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($colmaps as $colmap)
                    <tr>
                        <td>
                            {{$colmap->colmap_id}}
                        </td>
                        <td>
                            {{$colmap->column->description}}
                        </td>
                        <td>
                            @foreach($colmap->column->translation->values as $v)
                                {{substr($v->attribute->name, -2)}}: {{$v->value}}<br/>
                            @endforeach
                            ID {{$colmap->column_fk}}<br/>
                        </td>
                        <td>
                            @if($colmap->taxon_fk)
                                {{$colmap->taxon->taxon_name}}<br/>
                                ID {{$colmap->taxon_fk}}
                            @else
                                @lang('common.all')
                            @endif
                        </td>
                        <td>
                            @foreach($colmap->item_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            ID {{$colmap->item_type_fk}}
                        </td>
                        <td>
                            <form action="{{route('colmap.destroy', $colmap)}}" method="POST">
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
