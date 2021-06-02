                    <div>
                        @if($cm->public != 1)
                            <i class="fas {{ Config::get('ui.icon_unpublished', 'fa-eye-slash') }}"
                                title=" @lang('common.unpublished') "></i>
                        @endif
                        
                        {{ $translations->firstWhere('element_fk', $cm->column->translation_fk)->value }}
                        ({{ $cm->column->description }}, 
                        @lang('columns.data_type'): 
                        {{ $data_types->firstWhere('element_fk', $cm->column->data_type_fk)->value }})
                        
                        @if($descriptions->firstWhere('element_fk', $cm->column->translation_fk))
                            <i class="fas {{ Config::get('ui.icon_description', 'fa-info') }}"
                                title="{{ $descriptions->firstWhere('element_fk', $cm->column->translation_fk)->value }}"></i>
                        @endif
                    </div>
