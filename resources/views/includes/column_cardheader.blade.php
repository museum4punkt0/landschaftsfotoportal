                <div class="card-header">
                    <h5 class="mb-0">
                        @if($cm->public != 1)
                            <i class="fas {{ Config::get('ui.icon_unpublished', 'fa-eye-slash') }}"
                                title=" @lang('common.unpublished') "></i>
                        @endif
                        
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
