@foreach($sub->sortBy('title') as $child)
    <li class="nav-item">
        @if($child->item_id == $item->item_id)
            <a class="nav-link active" href="{{ $child->item_id }}">
        @else
            <a class="nav-link" href="{{ $child->item_id }}">
        @endif
        {{ $child->title }}
        
        {{-- Screen readers can mention the currently active menu item --}}
        @if($it->item_id == $item->item_id)
            <span class="sr-only">(current)</span>
        @endif
        </a>
        @if($loop->depth < Config::get('menu.sidebar_max_levels'))
            @if($loop->depth <= count($path) && $path[$loop->depth - 1] == $child->item_id && count($child->children))
                <ul>
                    @include('includes.item_submenu',['sub' => $child->children, 'path' => $path])
                </ul>
            @endif
        @endif
    </li>
@endforeach
