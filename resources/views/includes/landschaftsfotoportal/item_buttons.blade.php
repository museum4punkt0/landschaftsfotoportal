                <!-- Icons for user interaction -->
                <div>
                    <span class="fa-stack fa-2x">
                    @guest
                        <a href="#" data-toggle="modal" data-target="#downloadModal" data-href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                    @else
                        <a href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
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
                                <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                <i class="fas {{ Config::get('ui.icon_cart_remove') }} fa-stack-1x fa-inverse"></i>
                        @endif
                    @endguest
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                    @guest
                        <a href="#" data-toggle="modal" data-target="#requestLoginModal" title="@lang('comments.new')">
                    @else
                        <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('comment.store', $item->item_id) }}" title="@lang('comments.new')">
                    @endguest
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                    <span class="fa-stack fa-2x">
                        <a href="{{ route('item.show.public', $item->item_id) }}" title="@lang('common.permalink')">
                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                            <i class="fas {{ Config::get('ui.icon_permalink') }} fa-stack-1x fa-inverse"></i>
                        </a>
                    </span>
                </div>
