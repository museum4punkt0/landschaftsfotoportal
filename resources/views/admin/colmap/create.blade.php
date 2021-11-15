@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('colmaps.new')</h2>

<form action="{{ route('colmap.store') }}" method="POST">
    
    <div class="form-group">
        <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
        <select id="itemTypeSelect" name="item_type" class="form-control" size=1 autofocus>
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
        <select id="columnGroupSelect" name="column_group" class="form-control" size=1 >
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
        <label for="columnSelect">@lang('columns.list')</label>
        <select id="columnSelect" name="column" class="form-control" size=1 >
            @foreach($columns as $column)
                <option value="{{$column->column_id}}"
                    @if(old('column') == $column->column_id) selected @endif>
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
        <label for="publicSelect">@lang('common.published')</label>
        <select id="publicSelect" name="public" class="form-control" size=1 >
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
        <input type="text" id="configInput" name="config" class="form-control" value="{{old('config')}}" />
        <span class="text-danger">{{ $errors->first('config') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

@if(env('APP_DEBUG'))
    [Rendering time: {{ round(microtime(true) - LARAVEL_START, 3) }} seconds]
@endif

</div>

@endsection
