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
            <div class="card-header">@lang('columns.header')</div>
            <div class="card-body">
                <div class="row">
                    <div class="col align-self-start">
                        <a href="{{route('column.create')}}" class="btn btn-primary">@lang('columns.new')</a>
                    </div>
                    <div class="col align-self-end">
                        <!-- For defining autocomplete search -->
                        <input type="text" id='auto_search' class="form-control autocomplete" placeholder="@lang('search.search')" />
                    </div>
                </div>
                
                <!-- Script using jQuery UI autocomplete widget -->
                <script type="text/javascript">
                    // CSRF Token
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    
                    $(document).ready(function() {
                        $('#auto_search').autocomplete( {
                            minLength: 3,
                            source: function(request, response) {
                                // Fetch data
                                $.ajax({
                                    url:"{{route('column.autocomplete')}}",
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        _token: CSRF_TOKEN,
                                        search: request.term
                                    },
                                    success: function(data) {
                                        response(data);
                                    }
                                });
                            },
                            select: function (event, ui) {
                                // Set selection
                                $('#auto_search').val(ui.item.label); // display the selected text
                                location.href = ui.item.edit_url;
                                return false;
                            }
                        });
                    });
                </script>
                
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1">@lang('common.id')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('columns.data_type')</th>
                        <th colspan="1">@lang('lists.list')</th>
                        <th colspan="2">@lang('common.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($columns as $column)
                    <tr>
                        <td>
                            {{$column->column_id}}
                        </td>
                        <td>
                            {{$column->description}}
                        </td>
                        <td>
                            @foreach($column->translation->values as $v)
                                {{substr($v->attribute->name, -2)}}: {{$v->value}}<br/>
                            @endforeach
                            ID {{$column->translation_fk}}<br/>
                        </td>
                        <td>
                            @foreach($column->data_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            ID {{$column->data_type_fk}}
                        </td>
                        <td>
                            @if($column->list_fk)
                                {{$column->list->name}} ({{$column->list->description}})<br/>
                                ID {{$column->list_fk}}
                            @endif
                        </td>
                        <td>
                            <form action="{{route('column.edit', $column)}}" method="GET">
                                {{ csrf_field() }}
                                <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{route('column.destroy', $column)}}" method="POST">
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
            {{ $columns->links() }}
        </div>
    </div>
</div>

@endsection
