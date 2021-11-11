@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.new')</h2>

<form action="{{ route('column.store') }}" method="POST">
    
    <div class="form-group">
        <span>@lang('common.description')</span>
        <input type="text" name="description" class="form-control" value="{{old('description')}}" autofocus />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.translated_name')</span>
        <select name="translation" id="translation_select" class="form-control" size=1 >
            @foreach($translations as $trans)
                <option value="{{$trans->element_fk}}"
                    @if(old('translation') == $trans->element_fk) selected @endif>
                    {{ $trans->value }}
                </option>
            @endforeach
            <option value="-1" @if(old('translation') == -1) selected @endif>
                --- @lang('common.new') ---
            </option>
        </select>
        <span class="text-danger">{{ $errors->first('translation') }}</span>
        <input type="hidden" name="lang" value="{{$attribute->attribute_id}}" />
        <input type="text" name="new_translation" id="translation_input" class="form-control" value="{{old('new_translation')}}" />
        <span class="text-danger">{{ $errors->first('new_translation') }}</span>
    </div>
    <div class="form-group">
        <span>@lang('columns.data_type')</span>
        <select name="data_type" class="form-control" size=1 >
            @foreach($data_types as $type)
                <option value="{{$type->element_fk}}"
                    @if(old('data_type') == $type->element_fk) selected @endif>
                    {{ $type->value }}
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('data_type') }}</span>
    </div>
    <div class="form-group collapse @if(old('data_type') == $data_type_ids['_list_'] || old('data_type') == $data_type_ids['_multi_list_'] || !old('data_type'))show @endif" id="list_group">
        <span>@lang('lists.list')</span>
        <select name="list" class="form-control" size=1 >
            @foreach($lists as $list)
                <option value="{{$list->list_id}}"
                    @if(old('list') == $list->list_id) selected @endif>
                    {{$list->name}} ({{$list->description}})
                </option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('list') }}</span>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary">@lang('common.save')</button>
    </div>
    {{ csrf_field() }}
</form>

</div>

<script type="text/javascript">
    var select_element = document.getElementById("translation_select");
    var input_element = document.getElementById("translation_input");
    // Register event for changing/selecting options
    select_element.addEventListener("change", TranslationChanged);
    TranslationChanged();

    function TranslationChanged() {
        var translation = select_element.options[select_element.selectedIndex].value;
        // Toggle visibility of text input depending on selected option
        if (translation == -1) {
            input_element.style.visibility = "visible";
        }
        else {
            input_element.style.visibility = "hidden";
        }
    }
    
    // Triggered when select for 'data_type' changed
    $('.form-control[name=data_type]').change(function(event) {
        if ($(this).val() == {{ $data_type_ids['_list_'] }} || $(this).val() == {{ $data_type_ids['_multi_list_'] }}) {
            $('#list_group').collapse('show');
        }
        else {
            $('#list_group').collapse('hide');
        }
    });
</script>

@endsection
