                    <div class="list-group">
                    @foreach($items->sortBy(['item_type_fk', 'taxon_fk', 'title']) as $item)
                        <a href="{{ route('item.show.public', $item) }}"
                            class="list-group-item list-group-item-action row d-flex"
                        >
                            <div class="col">
                                {{ $item_types->firstWhere('element_fk', $item->item_type_fk)->value }}
                            </div>
                            <div class="col">
                                {{ optional($item->taxon)->full_name }}
                            </div>
                            <div class="col">
                                {{ $item->title }}
                            </div>
                        </a>
                    @endforeach
                    </div>
