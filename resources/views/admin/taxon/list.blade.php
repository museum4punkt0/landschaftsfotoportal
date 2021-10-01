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
            <div class="card-header">@lang('taxon.header')</div>
            <div class="card-body">
                <div class="row">
                    <div class="col align-self-start">
                        <a href="{{route('taxon.create')}}" class="btn btn-primary">@lang('taxon.new')</a>
                    </div>

                    @include('includes.form_autocomplete_search', [
                    'search_url' => route('taxon.autocomplete'),
                    'div_class' => 'col align-self-end',
                    'input_placeholder' => __('search.search'),
                    ])
                </div>

                <div class="table-responsive">
                    <table class="table mt-4">
                        <thead>
                            <tr>
                                <th colspan="1" id="taxon_id" class="orderby" title="sortieren" data-bs-column="taxon_id" data-bs-sort="asc">@lang('common.id')</th>
                                <th colspan="1" id="taxon_name" class="orderby" title="sortieren" data-bs-column="taxon_name" data-bs-sort="asc">@lang('taxon.taxon_name')</th>
                                <th colspan="1" id="native_name" class="orderby" title="sortieren" data-bs-column="native_name" data-bs-sort="asc">@lang('taxon.native_name')</th>
                                <th colspan="1">@lang('taxon.valid_name')</th>
                                <th colspan="1">@lang('taxon.rank')</th>
                                <th colspan="1">@lang('taxon.gsl_id')<br/>
                                    @lang('taxon.bfn_namnr')<br/>
                                    @lang('taxon.bfn_sipnr')
                                </th>
                                <th colspan="2">@lang('common.actions')</th>
                            </tr>
                            <tr>
                                <th colspan="1">
                                    <input type="text" id="taxon_id" size="3" class="form-control Listfilter" value="{{$aFilter['taxon_id']}}" />
                                </th>
                                <th colspan="1">
                                    <input type="text" id="taxon_name" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['taxon_name']}}" />
                                </th>
                                <th colspan="1">
                                    <input type="text" id="native_name" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['native_name']}}" />
                                </th>
                                <th colspan="1">
                                    <select id="valid_name"class="form-control Listfilter" style="width:100%;">
                                        <option value="">@lang('common.choose')</option>
                                        <option value="1" @if ($aFilter['valid_name'] == "1") selected="selected" @endif>
                                            @lang('common.yes')
                                        </option>
                                        <option value="-1" @if ($aFilter['valid_name'] == "-1") selected="selected" @endif>
                                            @lang('common.no')
                                        </option>    
                                    </select>
                                </th>
                                <th colspan="1">
                                    <input type="text" id="rank_abbr" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['rank_abbr']}}" />
                                </th>
                                <th colspan="1">
                                    <a class="btn btn-primary" data-toggle="collapse" href="#triple" role="button" aria-expanded="false" aria-controls="collapseExample">show/hide</a>
                                    <div id="triple" class="collapse">
                                        <input type="text" id="gsl_id" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['gsl_id']}}" />
                                        <input type="text" id="bfn_namnr" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['bfn_namnr']}}" />
                                        <input type="text" id="bfn_sipnr" size="12" class="form-control Listfilter" style="width:100%;" value="{{$aFilter['bfn_sipnr']}}" />
                                    </div>

                                </th>
                                <th colspan="6">
                                    <form action="{{route('taxon.index')}}" method="GET" id="frmListFilter">
                                        <script type="text/javascript">
                                            function param(name) {
                                                return (location.search.split(name + '=')[1] || '').split('&')[0];
                                            }

                                            $(document).ready(function () {
                                                //$('#triple').hide();
                                                $('#tripletrigger').click(function(){
                                                    $('#triple').toggle();
                                                });

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
                                                        $('#hidden_limit').val( this.value );
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

                                                var aFields = [
                                                    {name: 'taxon_id', type: 'string', element: 'text'},
                                                    {name: 'taxon_name', type: 'string', element: 'text'},
                                                    {name: 'native_name', type: 'string', element: 'text'},
                                                    {name: 'valid_name', type: 'string', element: 'text'},
                                                    {name: 'rank_abbr', type: 'string', element: 'text'},
                                                    {name: 'gsl_id', type: 'text', element: 'text'},
                                                    {name: 'bfn_namnr', type: 'text', element: 'text'},
                                                    {name: 'bfn_sipnr', type: 'text', element: 'text'},
                                                ];
                                                for (field in aFields) {
                                                    switch (aFields[field].element) {
                                                        case 'select':
                                                        //$('#Listfilter-Item_type').change(function () {
                                                            //$('#frmItem_type').val($('#Listfilter-Item_type').find(":selected").val());
                                                            //});
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
                                                $('#valid_name').change(function () {
                                                    $('#hidden_valid_name').val($('#valid_name').find(":selected").val());
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
                            @foreach($taxa as $taxon)
                            <tr>
                                <td>
                                    {{$taxon->taxon_id}}
                                </td>
                                <td>
                                    @for ($i = 0; $i < $taxon->depth; $i++)
                                    |___
                                    @endfor
                                    {{$taxon->taxon_name}} {{$taxon->taxon_author}} {{$taxon->taxon_suppl}}
                                </td>
                                <td>
                                    {{$taxon->native_name}}
                                </td>
                                <td>
                                    @if($taxon->valid_name)
                                    ID {{$taxon->valid_name}}
                                    @else
                                    @lang('common.yes')
                                    @endif
                                </td>
                                <td>
                                    {{$taxon->rank_abbr}}
                                </td>
                                <td>
                                    {{$taxon->gsl_id}}<br/>{{$taxon->bfn_namnr}}<br/>{{$taxon->bfn_sipnr}}
                                </td>
                                <td>
                                    <form action="{{route('taxon.edit', $taxon)}}" method="GET">
                                        {{ csrf_field() }}
                                        <button class="btn btn-primary" type="submit">@lang('common.edit')</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{route('taxon.destroy', $taxon)}}" method="POST">
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
            {{ $taxa->links() }}
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
