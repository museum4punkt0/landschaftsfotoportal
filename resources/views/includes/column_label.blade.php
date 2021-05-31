                    <div>
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                    </div>
