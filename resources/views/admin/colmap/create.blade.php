@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('colmaps.new')</h2>

<form action="{{ route('colmap.store') }}" method="POST">

    <div class="form-group">
        <label for="columnSelect">@lang('columns.list')</label>
        <select id="columnSelect" name="column" class="form-control" size=1 autofocus>
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

    @include('includes.colmap_create_fields')

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
