                <div class="card-header">
                    <h5 class="mb-0">
                        
                        {{ $cm->column->translation->attributes->
                            firstWhere('name', 'name_'.app()->getLocale())->pivot->value }} 
                        ({{ $cm->column->description }})
                    </h5>
                </div>
