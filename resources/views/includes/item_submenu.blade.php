@foreach($sub->sortBy(data_get($order, (isset($loop) ? $loop->depth : 0), 'title')) as $child)
    @if($child->public == 1)
        <li class="nav-item">
            <div class="nav-item-row d-flex">
            @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $child->item_id)
                <a class="nav-link active mr-auto" href="{{ route('item.show.public', $child) }}"
                    data-item-id="{{ $child->item_id }}">
            @else
                <a class="nav-link mr-auto" href="{{ route('item.show.public', $child) }}"
                    data-item-id="{{ $child->item_id }}">
            @endif

            {{ $child->title }}
            
            {{-- Screen readers can mention the currently active menu item --}}
            @if($child->item_id == $item->item_id)
                <span class="sr-only">(current)</span>
            @endif
            </a>

            <a href="#collapseMI{{ $child->item_id }}"
                @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $child->item_id)
                    class="nav-collapse-icon active"
                    aria-expanded="true"
                @else
                    class="nav-collapse-icon collapsed"
                    aria-expanded="false"
                @endif
                data-item-id="{{ $child->item_id }}"
                data-level="{{ (isset($loop) ? $loop->depth : 0) }}"
                data-toggle="collapse"
                role="button"
                aria-controls="collapseMI{{ $child->item_id }}"
            >
                <i class="fa mr-3" aria-hidden="true"></i>
            </a>
            </div>

            @if($loop->depth < Config::get('menu.sidebar_max_levels'))
                @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $child->item_id &&
                    count($child->children->where(
                        'item_type_fk', '<>', data_get($exclude, $loop->depth, -1)
                    )))
                    <ul class="collapse show" id="collapseMI{{ $child->item_id }}">
                        @include('includes.item_submenu', [
                            'sub' => $child->children->where(
                                'item_type_fk', '<>', data_get($exclude, $loop->depth, -1)
                            ),
                            'path' => $path
                        ])
                    </ul>
                @endif
            @endif
        </li>
    @endif
@endforeach
