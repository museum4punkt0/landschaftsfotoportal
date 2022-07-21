@extends('layouts.app')

@section('content')

<div class="container">
    @include('includes.alert_session_div')

    <div class="card">
        @if (true || Auth::check())
            <div class="card-header">@lang('colmaps.header')</div>
            <div class="card-body">
                <a href="{{route('colmap.create')}}" class="btn btn-primary">@lang('colmaps.new')</a>
                <a href="{{route('colmap.sort')}}" class="btn btn-primary">@lang('common.sort')</a>
                <hr>
                <div class="card-title">
                @lang('colmaps.mapping_for')
                @foreach($item_types->find($item_type)->values as $v)
                    @if($v->attribute->name == 'name_'.app()->getLocale())
                        {{$v->value}}
                    @endif
                @endforeach
                (Item-ID {{ $item_type }})
                </div>
                
                <form action="{{ route('colmap.map', $item_type) }}" method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
                            <select
                                id="itemTypeSelect"
                                name="item_type"
                                class="form-control"
                                size=1
                                autofocus
                            >
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
                
                <form action="{{ route('colmap.map.store') }}" method="POST">
                    <input type="hidden" name="item_type" value="{{ $item_type }}" />
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="columnMappedSelect">@lang('colmaps.mapped')</label>
                            <select
                                id="columnMappedSelect"
                                name="column_mapped[]"
                                aria-describedby="columnMappedHelpBlock"
                                class="form-control"
                                size=25
                                multiple
                                disabled="disabled"
                            >
                            @foreach($columns_mapped as $column)
                                <option value="{{$column->column_id}}">
                                    @foreach($column->translation->values as $t)
                                        @if($t->attribute->name == 'name_'.app()->getLocale())
                                            {{$t->value}}
                                        @endif
                                    @endforeach
                                    ({{$column->description}})
                                    @if($column->column_mapping->firstWhere('colmap_id', $column->colmap_id)->taxon)
                                        [{{ $column->column_mapping->firstWhere(
                                            'colmap_id', $column->colmap_id)->taxon->full_name }}]
                                    @endif
                                </option>
                            @endforeach
                            </select>
                            <small id="columnMappedHelpBlock" class="form-text text-muted">
                                @lang('colmaps.mapped_hint')
                            </small>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="columnAvailSelect">@lang('colmaps.unmapped')</label>
                            <select
                                id="columnAvailSelect"
                                name="column_avail[]"
                                aria-describedby="columnAvailHelpBlock"
                                class="form-control"
                                size=25
                                multiple
                            >
                            @foreach($columns_avail as $column)
                                <option value="{{$column->column_id}}"
                                    @if(is_array(old('column_avail')) &&
                                        false !== array_search($column->column_id, old('column_avail')))
                                        selected
                                    @endif
                                >
                                    @foreach($column->translation->values as $t)
                                        @if($t->attribute->name == 'name_'.app()->getLocale())
                                            {{$t->value}}
                                        @endif
                                    @endforeach
                                    ({{$column->description}})
                                </option>
                            @endforeach
                            </select>
                            <small id="columnAvailHelpBlock" class="form-text text-muted">
                                @lang('colmaps.unmapped_hint')
                            </small>
                            <span class="text-danger">{{ $errors->first('column_avail') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                        </div>
                        
                        <div class="col-md-6">
                            @include('includes.form_taxon_autocomplete', [
                                'search_url' => route('taxon.autocomplete', ['valid' => true]),
                                'div_class' => 'form-group',
                                'name' => 'taxon',
                                'input_placeholder' => '',
                                'input_label' => __('taxon.list'),
                                'null_label' => __('common.all'),
                                'taxon_name' => old('taxon_name', __('common.all')),
                                'taxon_id' => old('taxon'),
                            ])
                            <div class="form-group">
                                <label for="columnGroupSelect">@lang('columns.column_group')</label>
                                <select
                                    id="columnGroupSelect"
                                    name="column_group"
                                    class="form-control"
                                    size=1
                                >
                                    @foreach($column_groups as $group)
                                        <option value="{{$group->element_fk}}"
                                            @if(old('column_group') == $group->element_fk) selected @endif>
                                            {{$group->value}}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('column_group') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="publicSelect">@lang('common.published')</label>
                                <select id="publicSelect" name="public" class="form-control" size=1>
                                    <option value="1"
                                        @if(old('public') == 1) selected @endif>
                                        @lang('common.yes')
                                    </option>
                                    <option value="0"
                                        @if(old('public') == 0) selected @endif>
                                        @lang('common.no')
                                    </option>
                                </select>
                                <span class="text-danger">{{ $errors->first('public') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="configInput">@lang('colmaps.config')</label>
                                <input
                                    type="text"
                                    id="configInput"
                                    name="config"
                                    class="form-control"
                                    value="{{old('config')}}"
                                />
                                <span class="text-danger">{{ $errors->first('config') }}</span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">@lang('common.save')</button>
                            </div>
                            {{ csrf_field() }}
                        </div>
                    </div>
                </form>
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
    var elem = document.getElementById("itemTypeSelect");
    elem.addEventListener("change", itemTypeChanged);

    function itemTypeChanged() {
        var item_type = document.getElementById("itemTypeSelect").options[document.getElementById("itemTypeSelect").selectedIndex].value;
        window.location.href = "{{ route('colmap.map') }}/" + item_type;
    }
</script>

@endsection
