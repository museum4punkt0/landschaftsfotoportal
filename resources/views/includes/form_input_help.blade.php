@if($descriptions->firstWhere('element_fk', $cm->column->translation_fk))
    <small id="fieldsHelpBlock-{{ $cm->column->column_id }}" class="form-text text-muted">
        {{ $descriptions->firstWhere('element_fk', $cm->column->translation_fk)->value }}
    </small>
@endif
