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
                        <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item->item_id)}}#details">
                            <div class="portfolio-hover">
                                <div class="portfolio-hover-content text-center">
                                    <i class="portfolio-caption-heading">
                                    {{ Str::limit($item->item->details->firstWhere('column_fk', 23)->value_string,
                                        config('ui.galery_caption_length'), ' (...)') }}
                                    </i>
                                </div>
                            </div>
                            <div class="img-preview-square" style="background-image: url('{{ str_replace(['(',')'],['\(','\)'],asset('storage/' . Config::get('media.preview_dir') . $item->item->details->firstWhere('column_fk', 13)->value_string)) }}');">&nbsp;</div>
                        </a>
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
                            <div class="portfolio-caption-heading">
                            @if(!empty($item->item->details->firstWhere('column_fk', 22)->value_string))
                                {{ $item->item->details->firstWhere('column_fk', 22)->value_string }},
                            @endif
                            @if(!empty($item->item->details->firstWhere('column_fk', 20)->value_string))
                                {{ $item->item->details->firstWhere('column_fk', 20)->value_string }},
                            @endif
                                {{ $item->item->details->firstWhere('column_fk', 19)->value_string }}
                            </div>
                            <div class="portfolio-caption-subheading text-muted">
                                {{ $item->item->details->firstWhere('column_fk', 5)->value_string }}
                            </div>
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
