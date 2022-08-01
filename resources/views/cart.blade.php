@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')
    
    <!-- My Cart Gallery -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'portfolio',
        'section_heading' => __(config('ui.frontend_layout') . '.my_cart_heading'),
        'section_subheading' => __(config('ui.frontend_layout') . '.my_cart_subheading'),
    ])

            <div class="row">
            @foreach($cart as $item)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="portfolio-item">
                        <!-- Image preview and link -->
                        @include('includes.item_gallery_image', ['item' => $item->item])

                        <div class="portfolio-caption">
                            <!-- Icons for user interaction -->
                            <div class="my-2" style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('item.download', $item->item->item_id) }}" title="@lang('common.download')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_download') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#cartRemoveModal" data-href="{{ route('cart.remove', $item->cart_id) }}" title="@lang('cart.remove')">
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_cart_remove') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('comment.store', $item->item->item_id) }}" title="@lang('comments.new')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                            <!-- Image caption -->
                            @include('includes.item_gallery_image_caption', ['item' => $item->item])

                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            
            <!-- Pagination -->
            <div>
                {{ $cart->links() }}
            </div>

    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')

@endsection
