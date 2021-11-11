@extends('layouts.app')

@section('content')

<div class="container">
<h1>@lang('elements.edit')</h1>

<form action="{{ route('element.update', $element->element_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent_fk" class="form-control" size=1 autofocus>
            <option value="" @if(old('parent_fk', $element->parent_fk) == 0) selected @endif>
                @lang('common.root')
            </option>
            @foreach($elements as $el)
                <option value="{{$el->values[0]->element_fk}}"
                    @if(old('parent_fk', $element->parent_fk) == $el->values[0]->element_fk) selected @endif >
                    @for ($i = 0; $i < $el->depth + 1; $i++)
                        |___
                    @endfor
                    @foreach($el->values as $value)
                        {{$value->value}}; 
                    @endforeach
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent_fk') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
    @method('PATCH')
</form>

</div>

@stop
