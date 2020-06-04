@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('elements.new')</h2>

<form action="{{ route('list.element.store', $list->list_id) }}" method="POST">
    
    <div class="form-group">
        <span>@lang('values.value')</span>
        <input type="text" name="value" class="form-control" value="{{old('value')}}" />
        <span class="text-danger">{{ $errors->first('value') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('lists.attribute')</span>
        <select name="attribute" class="form-control" size=1 >
        @foreach($attributes as $attribute)
            <option value="{{$attribute->attribute_id}}"
            @if(old('attribute') == $attribute->attribute_id) selected @endif >
                {{$attribute->name}}
            </option>
        @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('attribute') }}</span>
    </div>
    @if($list->hierarchical)
        <div class="form-group">
            <span>@lang('lists.parent')</span>
            <select name="parent_fk" class="form-control" size=1 >
                <option value="0">@lang('common.root')</option>
                @foreach($elements as $element)
                    <option value="{{$element->values[0]->element_fk}}"
                    @if(old('parent_fk') == $element->values[0]->element_fk) selected @endif >
                    @for ($i = 0; $i < $element->depth + 1; $i++)
                        |___
                    @endfor
                    @foreach($element->values as $value)
                        {{$value->value}}; 
                    @endforeach
                    </option>
                @endforeach
            </select>
            <span class="text-danger">{{ $errors->first('parent_fk') }}</span>
        </div>
    @else
        <input type="hidden" name="parent_fk" class="form-control" value=0 />
    @endif
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
