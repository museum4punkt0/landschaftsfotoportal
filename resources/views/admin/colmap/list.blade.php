@extends('layouts.app')

@section('content')

@include('includes.modal_confirm_delete')
@include('includes.modal_alert')

<div class="container">
    @include('includes.alert_session_div')

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
                
                <div class="table-responsive">
                <table class="table mt-4">
                <thead>
                    <tr>
                        <th colspan="1" >@lang('common.id')</th>
                        <th colspan="1">@lang('common.description')</th>
                        <th colspan="1">@lang('columns.list')</th>
                        <th colspan="1">@lang('colmaps.column_group')</th>
                        <th colspan="1">@lang('taxon.list')</th>
                        <th colspan="1">@lang('colmaps.item_type')</th>
                        <th colspan="1">@lang('common.actions')</th>
                    </tr>
                    <tr>
                        <th colspan="1">
                            <input type="text" id="colmap_id" size="3" class="form-control Listfilter" value="{{$aFilter['colmap_id']}}" />
                        </th>
                        <th colspan="1">
                            <input type="text" id="description" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['description']}}" />
                        </th>
                        <th colspan="1">
                        </th>
                        <th colspan="1">
                            <select id="column_group_fk" style="width:100%;" class="form-control Listfilter" >
                                <option value="">@lang('common.showall')</option>
                                @foreach($column_groups as $ColumnGroup)
                                    <option value="{{$ColumnGroup->element_fk}}" @if ($ColumnGroup->element_fk == $aFilter['column_group_fk']) selected="selected" @endif>
                                        {{$ColumnGroup->value}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th colspan="1">
                            <select id="taxon_fk" style="width:100%;" class="form-control Listfilter" >
                                <option value="">@lang('common.showall')</option>
                                @foreach($taxa as $taxon)
                                    <option value="{{$taxon->taxon_id}}" @if ($taxon->taxon_id == $aFilter['taxon_fk']) selected="selected" @endif>
                                        {{$taxon->full_name}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th colspan="1">
                            <select id="item_type_fk" style="width:100%;" class="form-control Listfilter" >
                                <option value="">@lang('common.showall')</option>
                                @foreach($item_types as $ItemType)
                                    <option value="{{$ItemType->element_fk}}" @if ($ItemType->element_fk == $aFilter['item_type_fk']) selected="selected" @endif>
                                        {{$ItemType->value}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th colspan="1">
                            <form action="{{route(@Route::currentRouteName())}}" method="GET" id="frmListFilter">
                                <script type="text/javascript">
                                    function param(name) {
                                        return (location.search.split(name + '=')[1] || '').split('&')[0];
                                    }

                                    $(document).ready(function () {
                                        $('.Listfilter').tooltip();
                                        $('#limit').change(function (e){
                                            if(!$('#hidden_limit').length){
                                                $('#frmListFilter').append('<input type="hidden" name="limit" class="hiddenlistfilter" id="hidden_limit" value="' + this.value + '" />');
                                            }else{
                                                $('#hidden_limit').val(this.value);
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
                                            {name: 'colmap_id', type: 'string', element: 'text'},
                                            {name: 'description', type: 'string', element: 'text'},
                                            {name: 'item_type_fk', type: 'string', element: 'text'},
                                            {name: 'taxon_fk', type: 'string', element: 'text'}, //$colmap->taxon->taxon_name
                                            {name: 'column_fk', type: 'string', element: 'text'}, //$colmap->column_group->values as $v    $v->attribute->name
                                            {name: 'column_group_fk', type: 'string', element: 'text'}, //$colmap->column_group->values as $v    $v->attribute->name
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
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" class="publicToggleLink"
                                        data-url="{{ route('colmap.publish', $colmap) }}"
                                        data-colmap-id="{{$colmap->colmap_id}}"
                                        title="@lang('common.toggle_public')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        @if($colmap->public)
                                            <i class="fas {{ Config::get('ui.icon_published') }} fa-stack-1x fa-inverse"></i>
                                        @else
                                            <i class="fas {{ Config::get('ui.icon_unpublished') }} fa-stack-1x fa-inverse"></i>
                                        @endif
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('colmap.edit', $colmap) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </span>
                            <span class="d-md-table-cell fa-btn">
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('colmap.destroy', $colmap) }}"
                                        data-message="@lang('colmaps.confirm_delete', ['name' => $colmap->column->description])"
                                        data-title="@lang('colmaps.delete')"
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
            {{ $colmaps->links() }}
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

<script type="text/javascript">
    // Triggered when public visibility button is clicked
    $('.publicToggleLink').click(function(event) {
        event.preventDefault();
        let faIcon = $(this).children('i.fa-inverse');
        
        $.ajax({
            type:'GET',
            url:$(this).data('url'),
            success:function (data) {
                // Show alert model with status message
                $('#alertModalLabel').text('@lang("common.toggle_public")');
                $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                $('#alertModal').modal('show');
                // Change fa-icon for public visibility
                if (data.public) {
                    faIcon.removeClass('fa-eye-slash');
                    faIcon.addClass('fa-eye');
                }
                else {
                    faIcon.removeClass('fa-eye');
                    faIcon.addClass('fa-eye-slash');
                }
                // Close modal dialog
                window.setTimeout(function () {
                    $('#alertModal').modal('hide');
                }, 5000);
            },
            error:function (xhr) {
                // Render the Laravel error message
                $('#alertModalLabel').text('@lang("common.laravel_error")');
                $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                $('#alertModal').modal('show');
            },
        });
    });
</script>

@endsection
