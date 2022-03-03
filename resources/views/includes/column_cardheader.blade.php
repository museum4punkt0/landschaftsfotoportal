{{-- Included in resources/views/admin/item/show.blade.php --}}

                <div class="card-header">
                    <h5 class="mb-0">
                        @if($cm->public != 1)
                            <i class="fas {{ Config::get('ui.icon_unpublished', 'fa-eye-slash') }}"
                                title=" @lang('common.unpublished') "></i>
                        @endif
                        
                        {{ optional($translations->firstWhere('element_fk', $cm->column->translation_fk))->value }}
                        ({{ $cm->column->description }})
                    </h5>
                </div>
