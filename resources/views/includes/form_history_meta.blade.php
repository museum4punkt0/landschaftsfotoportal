{{-- Included in resources/views/includes/item_edit.blade.php --}}
            <div class="row">
                <div class="col-12 col-lg-1"><label>@lang('revisions.history'):</label></div>
                <div class="col">
                    <select class="form-control revision-detail-select" size=5
                        data-column="{{$column_id}}" 
                    >
                        @foreach($item->item->revisions->sortByDesc('updated_at') as $irev)
                            <option value="{{ $irev->revision }}"  data-content="
                                @switch($data_type)
                                    @case('menu_title')
                                        {{ $irev->menu_title }}
                                        @break
                                    @case('page_title')
                                        {{ $irev->page_title }}
                                        @break
                                    @case('public')
                                        {{ $irev->public }}
                                        @break
                                    @case('parent')
                                        {{ optional($irev->original_parent)->title ?? __('common.none') }}
                                        @if($irev->parent_fk) (ID {{ $irev->parent_fk }}) @endif
                                        @break
                                @endswitch
                            " data-meta="
                                ({{ $irev->updated_at }}, @lang('revisions.revision'): 
                                @if($irev->revision < 0)@lang('revisions.draft')@endif{{ $irev->revision }},
                                {{ $irev->editor->name }})
                            ">
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
