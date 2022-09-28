@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('lists.header')</div>
            <div class="card-body">
                @if(Route::currentRouteName() == 'list.internal')
                    <div class="alert alert-warning">@lang('lists.internal_warning')</div>
                @endif
                
                <a href="{{route('list.create')}}" class="btn btn-primary">@lang('lists.new')</a>
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1" id="list_id" class="orderby" title="sortieren" data-bs-column="list_id" data-bs-sort="asc">@lang('common.id')</th>
                        <th colspan="1" id="name" class="orderby" title="sortieren" data-bs-column="name" data-bs-sort="asc">@lang('common.name')</th>
                        <th colspan="1" id="description" class="orderby" title="sortieren" data-bs-column="description" data-bs-sort="asc">@lang('common.description')</th>
                        <th colspan="1">@lang('lists.hierarchical')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                    <tr>
                         <th colspan="1">
                            <input type="text" id="list_id" size="3" class="form-control Listfilter" value="{{$aFilter['list_id']}}" />
                        </th>
                        <th colspan="1">
                            <input type="text" id="name" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['name']}}" />
                        </th>
                        <th colspan="1">
                            <input type="text" id="description" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['description']}}" />
                        </th>
                        <th colspan="1">
                            <select id="hierarchical" class="form-control Listfilter">
                                <option value="">@lang('common.choose')</option>
                                <option value="1"@if ($aFilter['hierarchical'] == '1') selected="selected" @endif>@lang('common.yes')</option>
                                <option value="0"@if ($aFilter['hierarchical'] == '0') selected="selected" @endif>@lang('common.no')</option>
                            </select>
                        </th>
                        <th colspan="3">
                             
                            <form action="@if(Route::currentRouteName() == 'list.internal'){{route('list.internal')}}@else{{route('list.index')}}@endif" method="GET" id="frmListFilter">
                                <script type="text/javascript">
                                    function param(name) {
                                        return (location.search.split(name + '=')[1] || '').split('&')[0];
                                    }

                                    $(document).ready(function () {
                                        //Sortierung
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
                                            {name: 'list_id', type: 'string', element: 'text'},
                                            {name: 'name', type: 'string', element: 'text'},
                                            {name: 'description', type: 'string', element: 'text'},
                                            {name: 'hierarchical', type: 'boolean', element: 'select'},
                                        ];
                                        for (field in aFields) {
                                            switch (aFields[field].element) {
                                                case 'select':
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
                                <button class="btn btn-primary" type="submit">@lang('common.filter')</button>
                                <button class="btn btn-primary" type="reset" id="btnReset">@lang('common.reset')</button>
                            </form>
                        </th>
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
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('list.show', $list) }}" title="@lang('common.show')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_show') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('list.edit', $list) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('list.destroy', $list) }}"
                                        data-message="@lang('lists.confirm_delete', ['name' => $list->name])"
                                        data-title="@lang('lists.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
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
            {{ $lists->links() }}
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
