@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('colmaps.edit')</h2>

<div class="alert alert-danger">@lang('colmaps.edit_danger')</div>

<form action="{{ route('colmap.update', $colmap->colmap_id) }}" method="POST">
    
    <div class="form-group">
        <label for="columnSelect">@lang('columns.list')</label>
        <select id="columnSelect" name="column" aria-describedby="columnHelpBlock"
            class="form-control" size=1 disabled>
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
        <small id="columnHelpBlock" class="form-text text-muted">
            @lang('colmaps.column_help')
        </small>
        <span class="text-danger">{{ $errors->first('column') }}</span>
    </div>
    <div class="form-group">
        <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
        <select id="itemTypeSelect" name="item_type" aria-describedby="itemTypeHelpBlock"
            class="form-control" size=1 disabled>
            @foreach($item_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('item_type', $colmap->item_type_fk) == $type->element_fk) selected @endif >
                    {{$type->value}}
                </option>
            @endforeach
        </select>
        <small id="itemTypeHelpBlock" class="form-text text-muted">
            @lang('colmaps.item_type_help')
        </small>
        <span class="text-danger">{{ $errors->first('item_type') }}</span>
    </div>
    @include('includes.form_taxon_autocomplete', [
        'search_url' => route('taxon.autocomplete', ['valid' => true]),
        'div_class' => 'form-group',
        'name' => 'taxon',
        'input_placeholder' => '',
        'input_label' => __('colmaps.taxon'),
        'input_help' => __('colmaps.taxon_help') . " " . __('taxon.autocomplete_help'),
        'null_label' => __('common.all'),
        'taxon_name' => old('taxon_name', optional($colmap->taxon)->full_name ?? __('common.all')),
        'taxon_id' => old('taxon', $colmap->taxon_fk),
    ])
    <div class="form-group">
        <label for="columnGroupSelect">@lang('colmaps.column_group')</label>
        <select id="columnGroupSelect" name="column_group" aria-describedby="columnGroupHelpBlock"
            class="form-control" size=1 autofocus>
            @foreach($column_groups as $group)
                <option value="{{$group->element_fk}}"
                    @if(old('column_group', $colmap->column_group_fk) == $group->element_fk) selected @endif>
                    {{$group->value}}
                </option>
            @endforeach
        </select>
        <small id="columnGroupHelpBlock" class="form-text text-muted">
            @lang('colmaps.column_group_help')
        </small>
        <span class="text-danger">{{ $errors->first('column_group') }}</span>
    </div>
    <div class="form-group">
        <label for="columnOrderInput">@lang('colmaps.column_order')</label>
        <input type="text" id="columnOrderInput" name="column_order" aria-describedby="columnOrderHelpBlock"
            class="form-control" 
            value="{{ old('column_order', $colmap->column_order) }}"
        />
        <span class="text-danger">{{ $errors->first('column_order') }}</span>
        <small id="columnOrderHelpBlock" class="form-text text-muted">
            @lang('colmaps.column_order_help')
        </small>
    </div>
    <div class="form-group">
        <label for="publicSelect">@lang('colmaps.public')</label>
        <select id="publicSelect" name="public" aria-describedby="publicHelpBlock"
            class="form-control" size=1 >
            <option value="1"
                @if(old('public', $colmap->public) == 1) selected @endif>
                @lang('common.yes')
            </option>
            <option value="0"
                @if(old('public', $colmap->public) == 0) selected @endif>
                @lang('common.no')
            </option>
        </select>
        <small id="publicHelpBlock" class="form-text text-muted">
            @lang('colmaps.public_help')
        </small>
        <span class="text-danger">{{ $errors->first('public') }}</span>
    </div>
    <div class="form-group">
        <label for="apiAttributeInput">@lang('colmaps.api_attribute')</label>
        <input type="text" id="apiAttributeInput" name="api_attribute" class="form-control" 
            value="{{ old('api_attribute', $colmap->api_attribute) }}" maxlength="255"
        />
        <span class="text-danger">{{ $errors->first('api_attribute') }}</span>
    </div>
    <div class="form-group">
        <label for="configInput">@lang('colmaps.config')</label>
        <input type="text" id="configInput" name="config" class="form-control" 
            value="{{ old('config', $colmap->config) }}" maxlength="4095"
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
