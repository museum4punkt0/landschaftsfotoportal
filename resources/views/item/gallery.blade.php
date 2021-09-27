@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Portfolio Grid, Gallery with latest items-->
    @include('includes.item_gallery', [
        'items' => $items['latest'],
        'heading' => __('landschaftsfotoportal.gallery_latest_heading'),
        'subheading' => __('landschaftsfotoportal.gallery_latest_subheading')
    ])
    <!-- Portfolio Grid, Gallery with random items-->
    @include('includes.item_gallery', [
        'items' => $items['random'],
        'heading' => __('landschaftsfotoportal.gallery_random_heading'),
        'subheading' => __('landschaftsfotoportal.gallery_random_subheading')
    ])
    <!-- Portfolio Grid, Gallery with incomplete items-->
    @include('includes.item_gallery', [
        'items' => $items['incomplete'],
        'heading' => __('landschaftsfotoportal.gallery_incomplete_heading'),
        'subheading' => __('landschaftsfotoportal.gallery_incomplete_subheading')
    ])
    
    @include('includes.modal_login_request')
    @include('includes.modal_download')
    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')
    
@endsection
