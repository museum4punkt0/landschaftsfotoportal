@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('modules.new')</h2>

<form action="{{ route('module.store') }}" method="POST">

    <div class="form-group">
        <label for="moduleNameInput">@lang('modules.type')</label>
        <input
            type="text"
            id="moduleNameInput"
            name="module_name"
            class="form-control"
            value="{{ $template->config['name'][app()->getLocale()] ?? $template->name }} ({{ $template->config['description'][app()->getLocale()] ?? $template->description }})"
            readonly
        />
        <input type="hidden" name="module" value="{{$template->module_id}}" />
        <span class="text-danger">{{ $errors->first('module') }}</span>
    </div>
    <div class="form-group">
        <label for="nameInput">@lang('common.name')</label>
        <input type="text" id="nameInput" name="name" aria-describedby="nameHelpBlock" class="form-control" 
            value="{{ old('name', $template->name) }}" maxlength="255" autofocus
        />
        <small id="nameHelpBlock" class="form-text text-muted">
            @lang('modules.name_help')
        </small>
        <span class="text-danger">{{ $errors->first('name') }}</span>
    </div>
    <div class="form-group">
        <label for="descriptionInput">@lang('common.description')</label>
        <input type="text" id="descriptionInput" name="description" class="form-control" 
            value="{{ old('description', $template->config['description'][app()->getLocale()] ?? $template->description) }}" maxlength="255"
        />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <label for="positionInput">@lang('modules.position')</label>
        <input type="text" id="positionInput" name="position" aria-describedby="positionHelpBlock" class="form-control" 
            value="{{ old('position', $template->config['default_position']??'') }}" maxlength="255"
        />
        <small id="positionHelpBlock" class="form-text text-muted">
            @lang('modules.position_help')
        </small>
        <span class="text-danger">{{ $errors->first('position') }}</span>
    </div>
    <div class="form-group">
        <label for="itemInput">@lang('modules.item_fk')</label>
        <input type="text" id="itemInput" name="item" aria-describedby="itemHelpBlock" class="form-control" 
            value="{{ old('item', $template->item_fk) }}" maxlength="255"
        />
        <small id="itemHelpBlock" class="form-text text-muted">
            @lang('modules.item_fk_help')
        </small>
        <span class="text-danger">{{ $errors->first('item') }}</span>
    </div>

    <fieldset>
        <legend>@lang('modules.options')</legend>
        @foreach($template->config['available_options'] as $name => $option)
            <div class="form-group">
            @switch($option['data_type'])
                @case('string')
                    <label for="{{$name}}Select">{{ $option['name'][app()->getLocale()] ?? $name }}</label>
                    <input
                        type="text"
                        id="{{$name}}Select"
                        name="option[{{$name}}]"
                        aria-describedby="{{$name}}HelpBlock"
                        class="form-control"
                        value="{{ old('option.'.$name, $option['default'] ?? '') }}"
                        maxlength="255"
                    />
                    <small id="{{$name}}HelpBlock" class="form-text text-muted">
                        {{ $option['help'][app()->getLocale()] ?? '' }}
                    </small>
                    <span class="text-danger">{{ $errors->first('option.'.$name) }}</span>
                    @break
                @case('column')
                    <label for="{{$name}}Select">
                        @lang('columns.list'): {{ $option['name'][app()->getLocale()] ?? $name }}
                    </label>
                    <select
                        id="{{$name}}Select"
                        name="column[{{$name}}]"
                        aria-describedby="{{$name}}HelpBlock"
                        class="form-control"
                        size=1
                    >
                        <option value="">@lang('common.ignore')</option>
                        @foreach($columns as $column)
                            <option value="{{$column->column_id}}"
                                @if(old('column.'.$name) == $column->column_id) selected @endif
                            >
                                {{ $column->description }}
                            </option>
                        @endforeach
                    </select>
                    <small id="{{$name}}HelpBlock" class="form-text text-muted">
                        {{ $option['help'][app()->getLocale()] ?? '' }}
                    </small>
                    <span class="text-danger">{{ $errors->first('column.'.$name) }}</span>
                    @break
            @endswitch
            </div>
        @endforeach
    </fieldset>

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
