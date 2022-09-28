@extends('layouts.app')

@section('content')

<div class="container">
    @include('includes.alert_session_div')

    <h2>@lang('items.add_titles')</h2>
    <div class="alert alert-info">@lang('items.add_titles_hint')</div>

    <form action="{{ route('titles.store') }}" method="POST">
        <div class="form-group">
            <label for="itemTypeSelect">@lang('items.item_type')</label>
            <select id="itemTypeSelect" name="item_type" aria-describedby="itemTypeHelpBlock"
                class="form-control" size=1 autofocus>
                <option value="0"
                    @if(old('item_type') === 0) selected @endif >
                    @lang('common.all')
                </option>
                @foreach($item_types as $type)
                    <option value="{{$type->element_fk}}"
                        @if(old('item_type') == $type->element_fk) selected @endif >
                        {{$type->value}}
                    </option>
                @endforeach
            </select>
            <small id="itemTypeHelpBlock" class="form-text text-muted">
                @lang('items.titles_item_type_help')
            </small>
            <span class="text-danger">{{ $errors->first('item_type') }}</span>
        </div>

        <div class="form-group">
            <label for="taxonSchemaSelect">@lang('items.name_schema')</label>
            <select id="taxonSchemaSelect" name="taxon_schema" aria-describedby="taxonSchemaHelpBlock"
                class="form-control" size=1 autofocus>
            @for ($i = 0; $i <= 5; $i++)
                <option value="{{ $i }}" @if(old('taxon_schema') === $i) selected @endif >
                    @lang('items.name_schema_' . $i)
                </option>
            @endfor
            </select>
            <small id="taxonSchemaHelpBlock" class="form-text text-muted">
                @lang('items.name_schema_help')
            </small>
            <span class="text-danger">{{ $errors->first('taxon_schema') }}</span>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="updateCheckbox" name="update" aria-describedby="updateHelpBlock"
                    class="form-check-input" value=1
                >
                <label for="updateCheckbox" class="form-check-label">
                    @lang('items.titles_update')
                </label>
            </div>
            <small id="updateHelpBlock" class="form-text text-muted">
                @lang('items.titles_update_help')
            </small>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">@lang('common.next')</button>
        </div>
        {{ csrf_field() }}
    </form>

</div>

@endsection
