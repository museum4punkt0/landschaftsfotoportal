@extends('layouts.app')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

<div class="container">
    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('colmaps.header')</div>
            <div class="card-body">
                <div class="row">
                    <div class="col align-self-start">
                        <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
                        <a href="{{route('colmap.map')}}" class="btn btn-primary">@lang('common.batch')</a>
                        @if(count($colmaps))
                            <a href="{{route('colmap.sort')}}" class="btn btn-primary">@lang('common.sort')</a>
                        @endif
                    </div>
                    
                    @include('includes.form_autocomplete_search', [
                        'search_url' => route('colmap.autocomplete'),
                        'div_class' => 'col align-self-end',
                        'input_placeholder' => __('search.search'),
                    ])
                </div>
                
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('columns.list')</th>
                        <th colspan="1">@lang('columns.column_group')</th>
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
                                <b>{{substr($v->attribute->name, 0, -3)}}:</b> {{$v->value}}<br/>
                            @endforeach
                            <a href="{{route('column.edit', $colmap->column_fk)}}">ID {{$colmap->column_fk}}</a>
                        </td>
                        <td>
                            @foreach($colmap->column_group->values as $v)
                                @if($v->attribute->name == 'config')
                                    <b>{{$v->attribute->name, 0}}:</b> {{$v->value}}<br/>
                                @else
                                    <b>{{substr($v->attribute->name, 0, -3)}}:</b> {{$v->value}}<br/>
                                @endif
                            @endforeach
                            <a href="{{route('element.show', $colmap->column_group_fk)}}">ID {{$colmap->column_group_fk}}</a>
                        </td>
                        <td>
                            @if($colmap->taxon_fk)
                                {{$colmap->taxon->taxon_name}}<br/>
                                <a href="{{route('taxon.edit', $colmap->taxon_fk)}}">ID {{$colmap->taxon_fk}}</a>
                            @else
                                @lang('common.all')
                            @endif
                        </td>
                        <td>
                            @foreach($colmap->item_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            <a href="{{route('element.show', $colmap->item_type_fk)}}">ID {{$colmap->item_type_fk}}</a>
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
        <div class="card-footer">
            {{ $colmaps->links() }}
        </div>
    </div>
</div>

@endsection
