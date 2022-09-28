<label for="fieldsInput-{{ $cm->column->column_id }}" class="{{ $css_class ?? '' }}">
    @if($cm->public != 1)
        <i class="fas {{ Config::get('ui.icon_unpublished', 'fa-eye-slash') }}"
            title="@lang('common.unpublished')"></i>
    @endif

    @if(!empty($input_label))
        {{ $input_label }}
    @else
        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
    @endif

    @can('show-admin')
        ({{ $cm->column->description }}, 
        @lang('columns.data_type'): 
        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
    @endcan
</label>
