@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- My own items -->
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_header', [
        'section_id' => 'portfolio',
        'section_heading' => __(config('ui.frontend_layout') . '.my_items_heading'),
        'section_subheading' => __(config('ui.frontend_layout') . '.my_items_subheading'),
    ])
    
            <div class="container my-5">
                <a href="{{route('item.create.own', ['item_type'=>$item_type])}}" class="btn btn-primary">
                    @lang(config('ui.frontend_layout') . '.new_item')
                </a>
            </div>
            
            <div class="row">
            @foreach($items as $item)
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="portfolio-item">
                        <!-- Image preview and link -->
                        @include('includes.item_gallery_image', ['item' => $item])

                        <div class="portfolio-caption">
                            <!-- Icons for user interaction -->
                            <div class="my-2" style="font-size: 0.6rem;">
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('item.edit.own', $item->item_id) }}" title="@lang('common.edit')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_edit') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="{{ route('item.download', $item->item_id) }}" title="@lang('common.download')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_download') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#confirmDeleteModal"
                                        data-href="{{ route('item.destroy.own', $item->item_id) }}"
                                        data-message="@lang('items.confirm_delete', ['name' => $item->title])"
                                        data-title="@lang('items.delete')"
                                        title="@lang('common.delete')"
                                    >
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_delete') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                @if(!$item->carts->firstWhere('created_by', Auth::id()))
                                    <a href="#" class="cartAddBtn" data-href="{{ route('cart.add', $item->item_id) }}" title="@lang('cart.add')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_cart_add') }} fa-stack-1x fa-inverse"></i>
                                @else
                                    <a href="#" data-toggle="modal" data-target="#cartRemoveModal" data-href="{{ route('cart.remove', $item->carts->firstWhere('created_by', Auth::id())->cart_id) }}" title="@lang('cart.remove')">
                                        <i class="fas fa-circle fa-stack-2x text-danger"></i>
                                        <i class="fas {{ Config::get('ui.icon_cart_remove') }} fa-stack-1x fa-inverse"></i>
                                @endif
                                    </a>
                                </span>
                                <span class="fa-stack fa-2x">
                                    <a href="#" data-toggle="modal" data-target="#commentModal" data-href="{{ route('comment.store', $item->item_id) }}" title="@lang('comments.new')">
                                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                                        <i class="fas {{ Config::get('ui.icon_comment') }} fa-stack-1x fa-inverse"></i>
                                    </a>
                                </span>
                            </div>
                            <!-- Image caption -->
                            @include('includes.item_gallery_image_caption', ['item' => $item])

                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            <!-- Pagination -->
            <div>
                {{ $items->links() }}
            </div>
    
    @includeIf('includes.' . Config::get('ui.frontend_layout') . '.section_footer')
    
    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')
    @include('includes.modal_confirm_delete')
    
@endsection
