@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('colmaps.new')</h2>

<form action="{{ route('colmap.store') }}" method="POST">
    
    <div class="form-group">
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
                <option value="{{$group->element_id}}"
                    @if(old('column_group') == $group->element_id) selected @endif>
                    @foreach($group->values as $v)
                        @if($v->attribute->name == 'name_'.app()->getLocale())
                            {{$v->value}}
                        @endif
                    @endforeach
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('column_group') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.list')</span>
        <select name="column" class="form-control" size=1 >
            @foreach($columns as $column)
                <option value="{{$column->column_id}}"
                    @if(old('column') == $column->column_id) selected @endif>
                    @foreach($column->translation->values as $t)
                        @if($t->attribute->name == 'name_'.app()->getLocale())
                            {{$t->value}}
                        @endif
                    @endforeach
                    ({{$column->description}})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('column') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('colmaps.config')</span>
        <input type="text" name="config" class="form-control" value="{{old('config')}}" />
        <span class="text-danger">{{ $errors->first('config') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
