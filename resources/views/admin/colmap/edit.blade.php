@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('colmaps.edit')</h2>

<div class="alert alert-danger">@lang('colmaps.edit_danger')</div>

<form action="{{ route('colmap.update', $colmap->colmap_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('colmaps.item_type')</span>
        <select name="item_type" class="form-control" size=1 disabled>
            @foreach($item_types as $type)
                <option value="{{$type->element_id}}"
                    @if(old('item_type', $colmap->item_type_fk) == $type->element_id) selected @endif >
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
                        @if(old('taxon', $colmap->taxon_fk) == $taxon->taxon_id) selected @endif>
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
                    @if(old('column_group', $colmap->column_group_fk) == $group->element_fk) selected @endif>
                    {{$group->value}}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('column_group') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.list')</span>
        <select name="column" class="form-control" size=1 disabled>
            @foreach($columns as $column)
                <option value="{{$column->column_id}}"
                    @if(old('column', $colmap->column_fk) == $column->column_id) selected @endif >
                    {{$column->description}}
                    /
                    @foreach($column->translation->values as $t)
                        @if($t->attribute->name == 'name_'.app()->getLocale())
                            {{$t->value}}
                        @endif
                    @endforeach
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('column') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.ordering')</span>
        <input type="text" name="column_order" class="form-control" 
            value="{{ old('column_order', $colmap->column_order) }}"
        />
        <span class="text-danger">{{ $errors->first('column_order') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('common.published')</span>
        <select name="public" class="form-control" size=1 >
            <option value="1"
                @if(old('public', $colmap->public) == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public', $colmap->public) == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('colmaps.config')</span>
        <input type="text" name="config" class="form-control" 
            value="{{ old('config', $colmap->config) }}"
        />
        <span class="text-danger">{{ $errors->first('config') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

</div>

@endsection
