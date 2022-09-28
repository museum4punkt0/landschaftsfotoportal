<!-- Portfolio Grid, Gallery -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'portfolio',
        'section_heading' => $heading,
        'section_subheading' => $subheading,
    ])
    
        <div class="row">
        @foreach($items as $item)
            {{-- Limit the number of shown entries, used for search results --}}
            @isset($limit)
                @break($loop->iteration > $limit)
            @endisset
            
            @if($item->details->firstWhere('column_fk', $image_module->config['columns']['filename'] ?? 0))
            <div class="col-lg-4 col-sm-6 mb-4">
                <div class="portfolio-item @if($loop->last && $loop->odd) no-display @endif">
                    <!-- Image preview and link -->
                    @include('includes.item_gallery_image', ['item' => $item])

                    <div class="portfolio-caption">
                        <!-- Icons for user interaction -->
                        <div class="my-2" style="font-size: 0.6rem;">
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
                                        @if(!empty($item->details->firstWhere('column_fk', $image_module->config['columns']['missing'] ?? 0)->value_string))
                                            text-primary
                                        @else
                                            sgn-color-2
                                        @endif
                                        "></i>
                                        <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                        <!-- Image caption -->
                        @include('includes.item_gallery_image_caption', ['item' => $item])

                    </div>
                </div>
            </div>
            @endif
        
        @endforeach
        </div>
    
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
