@extends('layouts.app')

@section('content')

<div class="container">
<h1>@lang('elements.edit')</h1>

<form action="{{ route('element.update', $element->element_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('lists.parent')</span>
        <select name="parent_fk" class="form-control" size=1 >
        @if(0)
            <option value="0" @if($element->parent_fk == 0) selected @endif>
                @lang('common.root')
            </option>
        @endif
        @foreach($elements as $el)
            @if($element->element_id != $el->values[0]->element_fk)
            <option value="{{$el->values[0]->element_fk}}"
                @if($element->parent_fk == $el->values[0]->element_fk) selected @endif>
                @foreach($el->values as $value)
                    {{$value->value}}; 
                @endforeach
            </option>
            @endif
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
