{{-- Included in resources/views/includes/item_edit.blade.php --}}
            <div class="row">
                <div class="col-12 col-lg-1"><label>@lang('revisions.history'):</label></div>
                <div class="col">
                    <select class="form-control" size=5>
                        @foreach($item->item->revisions->sortByDesc('updated_at') as $irev)
                            <option value="{{ $irev->revision }}">
                                @switch($data_type)
                                    @case('title')
                                        {{ $irev->title }}
                                        @break
                                    @case('public')
                                        {{ $irev->public }}
                                        @break
                                    @case('parent')
                                        {{ optional($irev->original_parent)->title ?? __('common.none') }}
                                        @if($irev->parent_fk) (ID {{ $irev->parent_fk }}) @endif
                                        @break
                                @endswitch
                                ({{ $irev->updated_at }}, @lang('revisions.revision'): 
                                @if($irev->revision < 0)@lang('revisions.draft')@endif{{ $irev->revision }},
                                {{ $irev->editor->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
