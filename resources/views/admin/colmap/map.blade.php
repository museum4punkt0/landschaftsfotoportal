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
                @lang('colmaps.mapping_for')
                @foreach($item_types->find($item_type)->values as $v)
                    @if($v->attribute->name == 'name_'.app()->getLocale())
                        {{$v->value}}
                    @endif
                @endforeach
                (Item-ID {{ $item_type }})
                </div>
                
                {{--
                <form action="{{ route('colmap.map', $item_type) }}" method="GET">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <span>@lang('colmaps.item_type')</span>
                            <select name="item_type" class="form-control" size=1 >
                                @foreach($item_types as $type)
                                    <option value="{{$type->element_id}}"
                                        @if(old('item_type') == $type->element_id) selected @endif>
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
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary">@lang('common.save')</button>
                        </div>
                        {{ csrf_field() }}
                    </div>
                </form>
                --}}
                
                <form action="{{ route('colmap.map.store') }}" method="POST">
                    <input type="hidden" name="item_type" value="{{ $item_type }}" />
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <span>@lang('colmaps.mapped')</span>
                            <select name="column_mapped[]" class="form-control" size=25 multiple disabled="disabled">
                            @foreach($columns_mapped as $column)
                                <option value="{{$column->column_id}}">
                                    @foreach($column->translation->values as $t)
                                        @if($t->attribute->name == 'name_'.app()->getLocale())
                                            {{$t->value}}
                                        @endif
                                    @endforeach
                                    ({{$column->description}})
                                </option>
                            @endforeach
                            </select>
                            <span>@lang('colmaps.mapped_hint')</span>
                        </div>

                        <div class="form-group col-md-6">
                            <span>@lang('colmaps.unmapped')</span>
                            <select name="column_avail[]" class="form-control" size=25 multiple>
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
                            <span>@lang('colmaps.unmapped_hint')</span>
                            <span class="text-danger">{{ $errors->first('column_avail') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="col-md-6">
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <span>@lang('taxon.list')</span>
                                <select name="taxon" class="form-control" size=1 >
                                    <option value="">@lang('common.all')</option>
                                    @foreach($taxa as $taxon)
                                        @unless($taxon->valid_name)
                                            <option value="{{$taxon->taxon_id}}"
                                                @if(old('taxon') == $taxon->taxon_id) selected @endif>
                                                @for ($i = 0; $i < $taxon->depth; $i++)
                                                    |___
                                                @endfor
                                                {{$taxon->taxon_name}} {{$taxon->taxon_author}} ({{$taxon->native_name}})
                                            </option>
                                        @endunless
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('taxon') }}</span>
                            </div>
                            <div class="form-group">
                                <span>@lang('columns.column_group')</span>
                                <select name="column_group" class="form-control" size=1 >
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

@endsection
