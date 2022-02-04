{{-- Included in resources/views/includes/item_edit.blade.php --}}
                    <div class="row">
                        <div class="col-12 col-lg-1"><label>@lang('revisions.history'):</label></div>
                        <div class="col">
                            <select class="form-control revision-detail-select" size=5
                                data-column="{{$column_id}}"
                            >
                            @if($details->firstWhere('column_fk', $cm->column->column_id))
                                @foreach($details->firstWhere('column_fk', $cm->column->column_id)->detail->revisions->sortByDesc('updated_at') as $drev)
                                    <option value="{{ $drev->item->revision }}" data-content="
                                        @switch($data_type)
                                            @case('list')
                                                @if($drev->element)
                                                    {{ $drev->element->attributes->firstWhere(
                                                        'name', 'name_' . app()->getLocale()
                                                    )->pivot->value }}
                                                @endif
                                                @break
                                            @case('multi_list')
                                                @foreach($drev->elements as $element)
                                                    {{ $element->attributes->firstWhere(
                                                        'name', 'name_' . app()->getLocale()
                                                    )->pivot->value }}; 
                                                @endforeach
                                                @break
                                            @case('int')
                                                {{ $drev->value_int }}
                                                @break
                                            @case('float')
                                                {{ $drev->value_float }}
                                                @break
                                            @case('string')
                                                {{ $drev->value_string }}
                                                @break
                                            @case('date')
                                                {{ $drev->value_date }}
                                                @break
                                            @case('daterange')
                                                {{ $drev->value_daterange->from()->format('Y-m-d') }} - 
                                                {{ $drev->value_daterange->to()->format('Y-m-d') }}
                                                @break
                                        @endswitch
                                    " data-meta="
                                        ({{ $drev->item->updated_at }}, @lang('revisions.revision'): 
                                        @if($drev->item->revision < 0)@lang('revisions.draft')@endif{{ $drev->item->revision }},
                                        {{ $drev->item->editor->name }})
                                    ">
                                    </option>
                                @endforeach
                            @endif
                            </select>
                        </div>
                    </div>
