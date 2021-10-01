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
            <div class="card-header">@lang('columns.header')</div>
            <div class="card-body">
                <div class="row">
                    <div class="col align-self-start">
                        <a href="{{route('column.create')}}" class="btn btn-primary">@lang('columns.new')</a>
                    </div>
                    
                    @include('includes.form_autocomplete_search', [
                        'search_url' => route('column.autocomplete'),
                        'div_class' => 'col align-self-end',
                        'input_placeholder' => __('search.search'),
                    ])
                </div>
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1" id="column_id" class="orderby" title="sortieren" data-bs-column="column_id" data-bs-sort="asc">@lang('common.id')</th>
                        <th colspan="1" id="description" class="orderby" title="sortieren" data-bs-column="description" data-bs-sort="asc">@lang('common.description')</th>
                        <th colspan="1">@lang('common.name')</th>
                        <th colspan="1">@lang('columns.data_type')</th>
                        <th colspan="1">@lang('lists.list')</th>
                        <th colspan="2">@lang('common.actions')</th>
                    </tr>
                    <tr>
                         <th colspan="1">
                            <input type="text" id="column_id" size="3" class="form-control Listfilter" value="{{$aFilter['column_id']}}" />
                        </th>
                        <th colspan="1">
                            <input type="text" id="description" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['description']}}" />
                        </th>
                        <th colspan="1">
                            <!--<input type="text" id="name" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['name']}}" />-->
                        </th>
                        <th colspan="1">
                            <select id="data_type_fk" class="form-control Listfilter">
                                <option value="">@lang('common.choose')</option>
                                @foreach($data_types as $data_type)
                                    <option value="{{$data_type->element_fk}}"
                                        @if ($aFilter['data_type_fk'] == $data_type->element_fk) selected="selected" @endif>{{$data_type->value}}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                        <th colspan="1">
                            <select id="list_fk" class="form-control Listfilter" style="width: 100%;">
                                <option value="">@lang('common.choose')</option>
                                @foreach($lists as $list)
                                    <option value="{{$list->list_id}}"
                                        @if ($aFilter['list_fk'] == $list->list_id) selected="selected" @endif>{{$list->name}}
                                    </option>
                                @endforeach
                            </select>
                        </th>
                        <th colspan="3">
                            <form action="{{route('column.index')}}" method="GET" id="frmListFilter">
                                <script type="text/javascript">
                                    function param(name) {
                                        return (location.search.split(name + '=')[1] || '').split('&')[0];
                                    }

                                    $(document).ready(function () {
                                        $('.orderby').css('cursor','pointer');
                                        $('.orderby').each(function(e){
                                            var column = $(this).attr('data-bs-column');
                                            $(this).bind('click', function(){
                                                if(!$('#hidden_orderby').length){
                                                    $('#frmListFilter').append('<input type="hidden" name="orderby" class="hiddenlistfilter" id="hidden_orderby" value="' + column + '" />');
                                                }else{
                                                     $('#hidden_orderby').val(column);
                                                }
                                                if(param('sort') !== '' && column === param('orderby') ){
                                                    if( $('#hidden_sort').val() === 'desc' ){
                                                        $('#hidden_sort').val('asc');
                                                    }else{
                                                        $('#hidden_sort').val('desc');
                                                    }
                                                }
                                                $('#frmListFilter').submit();
                                            });
                                            
                                        });
                                        
                                        if(param('orderby') !== ''){
                                            $('#frmListFilter').append('<input type="hidden" name="orderby" class="hiddenlistfilter" id="hidden_orderby" value="' + param('orderby') + '" />');
                                            if( param('sort') === 'desc' || param('sort') === '' ){
                                                $('#' + param('orderby')).append('&nbsp;<i class="fas fa-angle-down"></i>');
                                                $('#frmListFilter').append('<input type="hidden" name="sort" class="hiddenlistfilter" id="hidden_sort" value="desc" />');
                                            }else{
                                                $('#' + param('orderby')).append('&nbsp;<i class="fas fa-angle-up"></i>');
                                                $('#frmListFilter').append('<input type="hidden" name="sort" class="hiddenlistfilter" id="hidden_sort" value="asc" />');
                                            }
                                        }
                                        
                                        $('#limit').change(function (e){
                                            if(!$('#hidden_limit').length){
                                                $('#frmListFilter').append('<input type="hidden" name="limit" class="hiddenlistfilter" id="hidden_limit" value="' + this.value + '" />');
                                            }else{
                                                $('#hidden_limit').val(this.value);
                                            }
                                            $('#frmListFilter').submit();

                                        });
                                        if(param('limit') > 0 ){
                                            $('#frmListFilter').append('<input type="hidden" name="limit" class="hiddenlistfilter" id="hidden_limit" value="' + param('limit') + '" />');
                                        }
                                        
                                        $('#limit > option').each(function(){
                                            if(this.value === param('limit')){
                                                $(this).attr('selected', 1);
                                            }
                                        });
                                        
                                        //Suche
                                        var aFields = [
                                            {name: 'column_id', type: 'string', element: 'text'},
                                            //{name: 'name', type: 'string', element: 'text'},
                                            {name: 'description', type: 'string', element: 'text'},
                                            {name: 'data_type_fk', type: 'string', element: 'select'},
                                            {name: 'list_fk', type: 'string', element: 'select'},
                                        ];
                                        for (field in aFields) {
                                            switch (aFields[field].element) {
                                                case 'select':
                                                    //$('#Listfilter-Item_type').change(function () {
                                                        //$('#frmItem_type').val($('#Listfilter-Item_type').find(":selected").val());
                                                    //});
                                                    $('#frmListFilter').append('<input type="hidden" name="' + aFields[field].name + '" class="hiddenlistfilter" id="hidden_' + aFields[field].name + '" value="' + param(aFields[field].name) + '" />');
                                                    break;
                                                case 'text':
                                                    $('#frmListFilter').append('<input type="hidden" name="' + aFields[field].name + '" class="hiddenlistfilter" id="hidden_' + aFields[field].name + '" value="' + param(aFields[field].name) + '" />');
                                                    
                                                    break;
                                            }
                                        }
                                        
                                        //Übernahme der Werte in die Versteckten Felder
                                        //transfer values from input-fields in the hidden ones
                                        $('.Listfilter').keyup(function (e) {
                                            $('#hidden_' + this.id).val(this.value);
                                            if (e.key === 'Enter') {
                                                $('#frmListFilter').submit();
                                            }
                                        });
                                        
                                        $('.Listfilter').change(function (e) {
                                            $('#hidden_' + this.id).val(this.value);
                                        });


                                        $('#btnReset').click(function () {
                                            for (field in aFields) {
                                                $('#hidden_' + aFields[field].name).val('');
                                                $('.Listfilter').val('');
                                            }
                                        });
                                    });
                                </script>
                                <!--<input type="hidden" name="limit" class="hiddenlistfilter" id="hidden_limit" value="' + e.value + '" />-->
                                <button class="btn btn-primary" type="submit">@lang('common.filter')</button>
                                <button class="btn btn-primary" type="reset" id="btnReset">@lang('common.reset')</button>
                            </form>
                        </th>
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
                                <b>{{substr($v->attribute->name, 0, -3)}}:</b> {{$v->value}}<br/>
                            @endforeach
                            <a href="{{route('element.show', $column->translation_fk)}}">ID {{$column->translation_fk}}</a>
                        </td>
                        <td>
                            @foreach($column->data_type->values as $v)
                                {{$v->value}}<br/>
                            @endforeach
                            <a href="{{route('element.show', $column->data_type_fk)}}">ID {{$column->data_type_fk}}</a>
                        </td>
                        <td>
                            @if($column->list_fk)
                                {{$column->list->name}} ({{$column->list->description}})<br/>
                                <a href="{{route('list.show', $column->list_fk)}}">ID {{$column->list_fk}}</a>
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
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            {{ $columns->links() }}
            @lang('common.rowsperpage')<div style="width: 100px;">
                <select id="limit" class="form-control">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>

@endsection
