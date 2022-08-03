@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('modules.new')</h2>

<form action="{{ route('module.create') }}" method="GET">
    
    <div class="form-group">
        <label for="moduleSelect">@lang('modules.type')</label>
        <select
            id="moduleSelect"
            name="module"
            aria-describedby="moduleHelpBlock"
            class="form-control"
            size=1
            autofocus
        >
            @foreach($modules as $module)
                <option value="{{$module->module_id}}"
                    @if(old('module') == $module->module_id) selected @endif>
                    "{{ $module->name }}" - 
                    {{ $module->config['name'][app()->getLocale()] ?? $module->name }}
                    ({{ $module->config['description'][app()->getLocale()] ?? $module->description }})
                </option>
            @endforeach
        </select>
        <small id="moduleHelpBlock" class="form-text text-muted">
            @lang('modules.type_help')
        </small>
        <span class="text-danger">{{ $errors->first('module') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.next')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
