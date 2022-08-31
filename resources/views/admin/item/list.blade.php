@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
    @if (true || Auth::check())
        <div class="card-header">@lang('items.header')</div>
        <div class="card-body">
            <div class="row">
                <div class="col align-self-start">
                    <a href="{{route('item.new')}}" class="btn btn-primary">@lang('items.new')</a>
                    <a href="{{route('item.unpublished')}}" class="btn btn-primary">@lang('items.unpublished')</a>

                </div>
                @include('includes.form_autocomplete_search', [
                    'search_url' => route('item.autocomplete'),
                    'div_class' => 'col align-self-end',
                    'input_placeholder' => __('search.search'),
                ])
            </div>

            <div class="table-responsive">
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th colspan="1" id="item_id" class="orderby" title="sortieren" data-bs-column="item_id" data-bs-sort="asc">@lang('common.id')</th>
                            <th colspan="1"></th>
                            <th colspan="1" id="title" class="orderby" title="sortieren" data-bs-column="title" data-bs-sort="asc">@lang('common.name')</th>
                            <th colspan="1">@lang('items.item_type')</th>
                            <th colspan="1">@lang('common.actions')</th>
                        </tr>
                        <tr>
                            <th colspan="1">
                                <input type="text" id="Listfilter-Id" size="3" class="form-control" value="{{$aFilter['id']}}" />
                            </th>
                            <th colspan="1"></th>
                            <th colspan="1">
                                <input type="text" id="Listfilter-title" size="12" class="form-control" style="width:100%;" value="{{$aFilter['title']}}" />
                            </th>
                            <th colspan="1">
                                <select id="Listfilter-Item_type" style="width:100%;" class="form-control" >
                                    <option value="">@lang('common.showall')</option>
                                    @foreach($item_types as $ItemType)
                                        <option value="{{$ItemType->element_fk}}" @if ($ItemType->element_fk == $aFilter['item_type']) selected="selected" @endif>{{$ItemType->value}}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th colspan="3">
                                <form action="{{route('item.index')}}" method="GET" id="frmListFilter">
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
                                            
                                            
                                            $('#Listfilter-Id').keyup(function (e) {
                                                $('#frmId').val($('#Listfilter-Id').val());
                                                if (e.key === 'Enter') {
                                                    $('#frmListFilter').submit();
                                                }
                                            });
                                            $('#Listfilter-title').keyup(function (e) {
                                                $('#frmtitle').val($('#Listfilter-title').val());
                                                if (e.key === 'Enter') {
                                                    $('#frmListFilter').submit();
                                                }
                                            });
                                            $('#Listfilter-Item_type').change(function () {
                                                $('#frmItem_type').val($('#Listfilter-Item_type').find(":selected").val());
                                            });
                                            $('#btnReset').click(function () {
                                                $('#Listfilter-Name').val('');
                                                $('#frmtitle').val('');
                                                $('#Listfilter-Id').val('');
                                                $('#frmId').val('');
                                                $('#frmItem_type').val('');
                                                $('#Listfilter-Item_type').prop('selectedIndex', 0);
                                            });
                                        });
                                    </script>
                                    <input type="hidden" name="id" id="frmId" value="{{$aFilter['id']}}" />
                                    <input type="hidden" name="title" id="frmtitle" value="{{$aFilter['title']}}" />
                                    <input type="hidden" name="item_type" id="frmItem_type" value="{{$aFilter['item_type']}}" />
                                    <button class="btn btn-primary" type="submit">@lang('common.filter')</button>
                                    <button class="btn btn-primary" type="reset" id="btnReset">@lang('common.reset')</button>
                                </form>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                {{$item->item_id}}
                            </td>
                            <td>
                                <div class="container">
                                    <a href="{{route('item.show.public', $item->item_id)}}#details">
                                    @if($item->details->firstWhere(
                                        'column_fk', $image_module->config['columns']['filename'] ?? 0))
                                        <img class="img-fluid thumbnail-table"
                                            src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                                $item->details->firstWhere(
                                                    'column_fk', $image_module->config['columns']['filename'] ?? 0
                                                )->value_string) }}"
                                            alt=""
                                            title="{{ optional($item->details->firstWhere(
                                                'column_fk', $image_module->config['columns']['caption'] ?? 0)
                                                )->value_string }}"
                                        />
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
                                <span class="d-md-table-cell fa-btn">
                                    <span class="fa-stack fa-2x">
                                        <a href="{{ route('item.show', $item) }}" title="@lang('common.show')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_show') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                </span>
                                <span class="d-md-table-cell fa-btn">
                                    <span class="fa-stack fa-2x">
                                        <a href="{{ route('item.edit', $item) }}" title="@lang('common.edit')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                        </a>
                                    </span>
                                </span>
                                <span class="d-md-table-cell fa-btn">
                                    <span class="fa-stack fa-2x">
                                        <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                            data-href="{{ route('item.destroy', $item) }}"
                                            data-message="@lang('items.confirm_delete', ['name' => $item->title])"
                                            data-title="@lang('items.delete')"
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
            {{ $items->links() }}
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
