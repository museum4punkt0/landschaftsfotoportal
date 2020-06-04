@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('items.edit')</h2>

<form action="{{ route('item.update', $item->item_id) }}" method="POST" enctype="multipart/form-data">
    
    @foreach($colmap as $cm)
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
                        @foreach($lists[$cm->column->list_fk] as $element)
                            <option value="{{$element->element_id}}"
                                @if(old('fields.'. $cm->column->column_id, 
                                    $details->firstWhere('column_fk', $cm->column->column_id)->element_fk) == 
                                     $element->element_id)
                                        selected
                                @endif
                            >
                                @for ($i = 0; $i < $element->depth; $i++)
                                    |___
                                @endfor
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
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_int) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is float --}}
            @case('_float_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_float) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is date --}}
            @case('_date_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="date" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_date) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
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
                    <input type="text" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is URL --}}
            @case('_url_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <input type="url" name="fields[{{ $cm->column->column_id }}]" class="form-control" 
                        value="{{ old('fields.'. $cm->column->column_id, 
                        $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}" />
                    <span class="text-danger">{{ $errors->first('fields.'. $cm->column->column_id) }}</span>
                </div>
                @break
            {{-- Data_type of form field is image --}}
            @case('_image_')
                <div class="form-group">
                    <span>
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $cm->column->data_type->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }})
                    </span>
                    <div class="form-row">
                        <div class="col">
                            <input type="file" class="form-control-file" name="fields[{{ $cm->column->column_id }}]" />
                            <span class="form-text text-muted">@lang('column.image_hint')</span>
                        </div>
                        <div class="col">
                            @if(Storage::exists('public/images/'.
                                $details->firstWhere('column_fk', $cm->column->column_id)->value_string))
                                <img src="{{ asset('storage/images/'.
                                    $details->firstWhere('column_fk', $cm->column->column_id)->value_string) }}"
                                    width=100
                                />
                            @else
                                @lang('columns.image_not_available')
                            @endif
                        </div>
                    </div>
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
    @method('PATCH')
</form>

</div>

@endsection
