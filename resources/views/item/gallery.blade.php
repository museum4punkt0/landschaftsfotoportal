@extends('layouts.frontend_' . Config::get('ui.frontend_layout'))

@section('content')

    <!-- Portfolio Grid, Gallery with latest items-->
    @include('includes.item_gallery', [
        'items' => $items['latest'],
        'heading' => 'Neueste Bilder',
        'subheading' => 'Lorem ipsum dolor sit amet consectetur.'
    ])
    <!-- Portfolio Grid, Gallery with random items-->
    @include('includes.item_gallery', [
        'items' => $items['random'],
        'heading' => 'Zufällige Bilder',
        'subheading' => 'Lorem ipsum dolor sit amet consectetur.'
    ])
    
    @include('includes.modal_login_request')
    @include('includes.modal_download')
    @include('includes.modal_alert')
    @include('includes.modal_cart_remove')
    @include('includes.modal_comment_add')
    
@endsection
