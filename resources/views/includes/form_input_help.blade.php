@if($descriptions->firstWhere('element_fk', $cm->column->translation_fk))
    <small id="fieldsHelpBlock-{{ $cm->column->column_id }}" class="form-text text-muted">
    @if(!empty($input_help))
        {{ $input_help }}
    @else
        {{ $descriptions->firstWhere('element_fk', $cm->column->translation_fk)->value }}
    @endif
    </small>
@endif
