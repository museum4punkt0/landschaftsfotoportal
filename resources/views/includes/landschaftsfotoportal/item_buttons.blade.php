                <!-- Icons for user interaction -->
                <div>
                    @can('show-admin')
                        <span class="fa-stack fa-2x">
                            <a href="{{ route('item.edit', $item->item_id) }}" title="@lang('common.edit')">
                                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                            </a>
                        </span>
                    @elsecan('update', $item)
                        <span class="fa-stack fa-2x">
                            <a href="{{ route('item.edit.own', $item->item_id) }}" title="@lang('common.edit')">
                                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                            </a>
                        </span>
                    @endcan
                    <span class="fa-stack fa-2x">
                    @guest
                        <a href="#" data-toggle="modal" data-target="#downloadModal" data-href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                    @else
                        @if(Config::get('ui.download_terms_auth'))
                            <a href="#" data-toggle="modal" data-target="#downloadModal" data-href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                        @else
                            <a href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                        @endif
                    @endguest
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas {{ Config::get('ui.icon_download') }} fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                    @guest
                        <a href="#" data-toggle="modal" data-target="#requestLoginModal" title="@lang('cart.add')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas {{ Config::get('ui.icon_cart_add') }} fa-stack-1x fa-inverse"></i>
                    @else
                        @if(!$item->carts->firstWhere('created_by', Auth::id()))
                            <a href="#" class="cartAddBtn" data-href="{{ route('cart.add', $item->item_id) }}" title="@lang('cart.add')">
                                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                <i class="fas {{ Config::get('ui.icon_cart_add') }} fa-stack-1x fa-inverse"></i>
                        @else
                            <a href="#" data-toggle="modal" data-target="#cartRemoveModal" data-href="{{ route('cart.remove', $item->carts->firstWhere('created_by', Auth::id())->cart_id) }}" title="@lang('cart.remove')">
                                <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                <i class="fas {{ Config::get('ui.icon_cart_remove') }} fa-stack-1x fa-inverse"></i>
                        @endif
                    @endguest
                        </a>
                    </span>
                    @if(config('ui.comments'))
                        <span class="fa-stack fa-2x">
                        @guest
                            <a href="#" data-toggle="modal" data-target="#requestLoginModal" title="@lang('comments.new')">
                        @else
                            <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('comment.store', $item->item_id) }}" title="@lang('comments.new')">
                        @endguest
                                <i class="fas fa-circle fa-stack-2x
                                @if(!empty($item->details->firstWhere('column_fk', 22)->value_string))
                                    text-primary
                                @else
                                    sgn-color-2
                                @endif
                                "></i>
                                <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                            </a>
                        </span>
                    @endif
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('item.show.public', $item->item_id) }}" title="@lang('common.permalink')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas {{ Config::get('ui.icon_permalink') }} fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                </div>
