@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Portfolio Grid, Gallery -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Neue Bilder</h2>
                <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
            </div>
            <div class="row">
            @foreach($items as $item)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="portfolio-item">
                        <a class="portfolio-link d-flex justify-content-center" href="{{route('item.show.public', $item->item_id)}}#details">
                            <div class="portfolio-hover">
                                <div class="portfolio-hover-content text-center">
                                    <i class="portfolio-caption-heading">
                                    {{ $item->details->firstWhere('column_fk', 23)->value_string }}
                                    </i>
                                </div>
                            </div>
                            <img class="img-fluid" src="{{ asset('storage/'. Config::get('media.preview_dir') .
                                    $item->details->firstWhere('column_fk', 13)->value_string .'.jpg') }}" alt="" />
                        </a>
                        <div class="portfolio-caption">
                            <div class="portfolio-caption-heading">
                                {{ $item->details->firstWhere('column_fk', 22)->value_string }},
                                {{ $item->details->firstWhere('column_fk', 20)->value_string }},
                                {{ $item->details->firstWhere('column_fk', 19)->value_string }}
                            </div>
                            <div class="portfolio-caption-subheading text-muted">
                                {{ $item->details->firstWhere('column_fk', 5)->value_string }}
                            </div>
                            <!-- Icons for user interaction -->
                            <div class="my-2" style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                @guest
                                    <a href="#" data-toggle="modal" data-target="#downloadModal" title="@lang('common.download')">
                                @else
                                    <a href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                                @endguest
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-download fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                @guest
                                    <a href="#" data-toggle="modal" data-target="#requestLoginModal" title="@lang('cart.add')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas fa-images fa-stack-1x fa-inverse"></i>
                                @else
                                    @if(!$item->carts->firstWhere('created_by', Auth::id()))
                                        <a href="#" id="cartAddBtn" data-href="{{ route('cart.add', $item->item_id) }}" title="@lang('cart.add')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas fa-images fa-stack-1x fa-inverse"></i>
                                    @else
                                        <a href="#" data-toggle="modal" data-target="#cartRemoveModal" data-href="{{ route('cart.remove', $item->carts->firstWhere('created_by', Auth::id())->cart_id) }}" title="@lang('cart.remove')">
                                            <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                            <i class="fas fa-trash fa-stack-1x fa-inverse"></i>
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
                                        <i class="fas fa-comment fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>
    
    @include('includes.modal_login_request')
    @include('includes.modal_download')
    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')
    
@endsection
