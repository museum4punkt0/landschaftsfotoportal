@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('items.new')</h2>

<form action="{{ route('item.store') }}" method="POST">
    
    @foreach($colmap as $cm)
        {{--
        tr {{ $cm->column->translation_fk }}, 
        dt {{ $cm->column->data_type_fk }}, 
        {{ $cm->column->data_type->values[0]->value }}, 
        {{ $cm->column->data_type->values[1]->value }}, 
        {{ print_r($data_types->contains('_list_')) }}
        --}}
        
        @switch($cm->column->data_type->attributes->firstWhere('name', 'code')->pivot->value)
            
            {{-- Data_type of form field is list --}}
            @case('_list_')
                {{-- dd($lists->firstWhere('list_id', $cm->column->list_fk)->elements) --}}
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <select name="fields[{{ $cm->column->column_id }}]" class="form-control" size=1 >
                        @foreach($lists->firstWhere('list_id', $cm->column->list_fk)->elements as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id) == $element->element_id) selected @endif>
                                @foreach($element->values as $v)
                                    {{$v->value}}, 
                                @endforeach
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
            {{-- Data_type of form field is integer --}}
            @case('_integer_')
            {{-- Data_type of form field is float --}}
            @case('_float_')
            {{-- Data_type of form field is string --}}
            @case('_string_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" value="{{old('fields.'. $cm->column->column_id)}}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            
        @endswitch
        <br/>
    @endforeach
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

@endsection
