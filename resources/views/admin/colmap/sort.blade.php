@extends('layouts.app')

@section('content')

@include('includes.modal_alert')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('colmaps.header')</div>
            <div class="card-body">
                <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
                <a href="{{route('colmap.map')}}" class="btn btn-primary">@lang('common.batch')</a>
                <hr>
                <div class="card-title">
                @lang('colmaps.sort_for')
                @foreach($item_types->find($item_type)->values as $v)
                    @if($v->attribute->name == 'name_'.app()->getLocale())
                        {{$v->value}}
                    @endif
                @endforeach
                (Item-ID {{ $item_type }})
                </div>
                
                <form action="{{ route('colmap.sort', $item_type) }}" method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <select id="itemTypeSelect" name="item_type" class="form-control"
                                data-url="{{ route('colmap.sort') }}" size=1 autofocus>
                                @foreach($item_types as $type)
                                    <option value="{{$type->element_id}}"
                                        @if(old('item_type', $item_type) == $type->element_id) selected @endif>
                                        @foreach($type->values as $v)
                                            @if($v->attribute->name == 'name_'.app()->getLocale())
                                                {{$v->value}}
                                            @endif
                                        @endforeach
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{ $errors->first('item_type') }}</span>
                        </div>
                        {{ csrf_field() }}
                    </div>
                </form>
                
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <ul class="sort-list list-group" data-url="{{ route('colmap.sort.store') }}">
                            @foreach ($columns_mapped as $column)
                            <li class="list-group-item" data-id="{{$column->colmap_id}}">
                                <span class="handle"></span>
                                @foreach($column->translation->values as $t)
                                    @if($t->attribute->name == 'name_'.app()->getLocale())
                                        {{$t->value}}
                                    @endif
                                @endforeach
                                ({{$column->description}}), ID {{$column->column_id}}
                                    @if($column->column_mapping->firstWhere('colmap_id', $column->colmap_id)->taxon)
                                        [{{ $column->column_mapping->firstWhere(
                                            'colmap_id', $column->colmap_id)->taxon->full_name }}]
                                    @endif
                            </li>
                            @endforeach
                        </ul>
                        <hr>
                        <span>@lang('colmaps.sort_hint')</span>
                    </div>
                </div>
                <style>
                    .list-group-item {
                        display: flex;
                        align-items: center;
                    }
                    .highlight {
                        background: #f7e7d3;
                        min-height: 30px;
                        list-style-type: none;
                    }
                    .handle {
                        min-width: 18px;
                        background: #607D8B;
                        height: 15px;
                        display: inline-block;
                        cursor: move;
                        margin-right: 10px;
                    }
                </style>
            </div>
        @else
            <div class="card-body">
                <h3>You need to log in. <a href="{{url()->current()}}/login">Click here to login</a></h3>
            </div>
        @endif
        <div class="card-footer">
            
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#itemTypeSelect').change(function(event) {
            window.location.href =  $(this).data('url') + '/' + $(this).val();
        });

        function updateToDatabase(url, idString) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method:'POST',
                data:{ids:idString},
                success:function(data) {
                    // Show alert model with status message
                    $('#alertModalLabel').text('@lang("common.update_success")');
                    $('#alertModalContent').html('<div class="alert alert-success">' + data.success + '</div>');
                    $('#alertModal').modal('show');
                    // Close modal dialog
                    window.setTimeout(function () {
                        $('#alertModal').modal('hide');
                    }, 1500);
                },
                 error:function (xhr) {
                    // Render the Laravel error message
                    $('#alertModalLabel').text('@lang("common.laravel_error")');
                    $('#alertModalContent').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                    $('#alertModal').modal('show');
                },
           });
        }

        $('.sort-list').sortable({
            handle: '.handle',
            placeholder: 'highlight',
            axis: "y",
            update: function (e, ui) {
                var url = $('.sort-list').data('url');
                var sortData = $('.sort-list').sortable('toArray', { attribute: 'data-id' });
                updateToDatabase(url, sortData.join(','));
            },
        });
    });
</script>
@endsection
