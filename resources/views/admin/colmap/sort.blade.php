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
                            <select name="item_type" id="item_type_select" class="form-control" size=1 >
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
                        <ul class="sort_menu list-group">
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
    var elem = document.getElementById("item_type_select");
    elem.addEventListener("change", ItemTypeChanged);

    function ItemTypeChanged() {
        var item_type = document.getElementById("item_type_select").options[document.getElementById("item_type_select").selectedIndex].value;
        window.location.href = "{{ route('colmap.sort') }}/" + item_type;
    }
    
    $(document).ready(function() {

        function updateToDatabase(idString) {
            $.ajaxSetup({ headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}});
            $.ajax({
                url:'{{ route('colmap.sort.store') }}',
                method:'POST',
                data:{ids:idString},
                success:function() {
                    alert('@lang('common.update_success')')
                }
            })
        }

        var target = $('.sort_menu');
        target.sortable({
            handle: '.handle',
            placeholder: 'highlight',
            axis: "y",
            update: function (e, ui) {
                var sortData = target.sortable('toArray', { attribute: 'data-id'})
                updateToDatabase(sortData.join(','))
            }
        })
    })
</script>
@endsection
