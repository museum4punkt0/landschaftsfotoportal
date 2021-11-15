@extends('layouts.app')

@section('content')

<div class="container">
<h2>@lang('columns.new')</h2>

<form action="{{ route('column.store') }}" method="POST">
    
    <div class="form-group">
        <label for="descriptionInput">@lang('common.description')</label>
        <input type="text" id="descriptionInput" name="description" class="form-control"
            value="{{old('description')}}" autofocus
        />
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="form-group">
        <label for="translationSelect">@lang('columns.translated_name')</label>
        <select id="translationSelect" name="translation" class="form-control" size=1>
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
        <input type="text" id="translationInput" name="new_translation" class="form-control" value="{{old('new_translation')}}" />
        <span class="text-danger">{{ $errors->first('new_translation') }}</span>
    </div>
    <div class="form-group">
        <label for="dataTypeSelect">@lang('columns.data_type')</label>
        <select id="dataTypeSelect" name="data_type" class="form-control" size=1>
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
        <label for="listSelect">@lang('lists.list')</label>
        <select id="listSelect" name="list" class="form-control" size=1>
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
    var select_element = document.getElementById("translationSelect");
    var input_element = document.getElementById("translationInput");
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
