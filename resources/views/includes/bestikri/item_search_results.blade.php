                    <div class="list-group">
                        <div class="list-group-item list-group-item-action row d-flex">
                            <div class="col">
                                <strong>@lang('items.item_type')</strong>
                            </div>
                            <div class="col">
                                <strong>@lang('taxon.taxon_name')</strong>
                            </div>
                            <div class="col">
                                <strong>@lang('items.title')</strong>
                            </div>
                            <div class="col">
                                <strong>@lang('common.info')</strong>
                            </div>
                        </div>
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
                            <div class="col">
                                {{ optional($item->details->firstWhere('column_fk', 14))->value_string }}
                            </div>
                        </a>
                    @endforeach
                    </div>
