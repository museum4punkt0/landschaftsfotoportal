@foreach($sub->sortBy(data_get($order, (isset($loop) ? $loop->depth : 0), 'title')) as $child)
    @if($child->public == 1)
        <li class="nav-item">
            @if($child->item_id == $item->item_id)
                <a class="nav-link active" href="{{ route('item.show.public', $child) }}">
            @else
                <a class="nav-link" href="{{ route('item.show.public', $child) }}">
            @endif
            {{ $child->title }}
            
            {{-- Screen readers can mention the currently active menu item --}}
            @if($child->item_id == $item->item_id)
                <span class="sr-only">(current)</span>
            @endif
            </a>
            @if($loop->depth < Config::get('menu.sidebar_max_levels'))
                @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $child->item_id &&
                    count($child->children->where(
                        'item_type_fk', '<>', data_get($exclude, $loop->depth, -1)
                    )))
                    <ul>
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
