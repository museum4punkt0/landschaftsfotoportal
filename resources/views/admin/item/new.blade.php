@extends('layouts.app')

@section('content')

<div class="container">
@if (request('msg'))
    <div class="alert alert-info">
    @if (request('msg') == 'new_related')
        @lang('items.new_related_info')
    @endif
    </div>
@endif

<h2>@lang('items.new')</h2>

<form action="{{ route('item.create') }}" method="GET">
    
    <div class="form-group">
        <label for="itemTypeSelect">@lang('colmaps.item_type')</label>
        <select id="itemTypeSelect" name="item_type" class="form-control" size=1 autofocus>
            @foreach($item_types as $type)
                <option value="{{$type->element_id}}"
                    @if(old('item_type', request('item_type')) == $type->element_id) selected @endif>
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
        'null_label' => __('common.none'),
        'taxon_name' => old('taxon_name', __('common.none')),
        'taxon_id' => old('taxon'),
    ])
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.next')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
