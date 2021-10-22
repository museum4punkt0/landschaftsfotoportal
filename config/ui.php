<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration for the user interface
    |--------------------------------------------------------------------------
    |
    | TODO
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Name of the layout for this app instance
    |--------------------------------------------------------------------------
    |
    | The corresponding layout file should be located at:
    | ressources/views/layouts/frontend_{FRONTEND_LAYOUT_NAME}.blade.php
    |
    */
    
    'frontend_layout' => env('APP_LAYOUT', 'landschaftsfotoportal'),

    /*
    |--------------------------------------------------------------------------
    | Name of some icons, mainly used for buttons
    |--------------------------------------------------------------------------
    |
    | You can choose any Font Awesome icon, always prefixed with 'fa-'
    | please see https://fontawesome.com/icons
    |
    */
    
    'icon_cart_add' => 'fa-bookmark',
    'icon_cart_remove' => 'fa-trash',
    'icon_comment' => 'fa-comment',
    'icon_download' => 'fa-download',
    'icon_permalink' => 'fa-link',
    'icon_published' => 'fa-eye',
    'icon_unpublished' => 'fa-eye-slash',
    'icon_description' => 'fa-info',
    'icon_items_own' => 'fa-images',
    'icon_email_address' => 'fa-at',
    'icon_edit' => 'fa-pencil-alt',
    'icon_delete' => 'fa-trash',

    /*
    |--------------------------------------------------------------------------
    | Number of search results for autocomplete
    |--------------------------------------------------------------------------
    |
    | How many result rows are returned for an autocomplete search
    |
    */
    
    'autocomplete_results' => 5,
    
    /*
    |--------------------------------------------------------------------------
    | Number of search results
    |--------------------------------------------------------------------------
    |
    | How many result rows are displayed
    |
    */
    
    'search_results' => 30,
    
    /*
    |--------------------------------------------------------------------------
    | Number of items in cart or own items
    |--------------------------------------------------------------------------
    |
    | How many thumbnails are displayed in a special gallery in user's profile
    |
    */
    
    'cart_items' => 12,
    
    /*
    |--------------------------------------------------------------------------
    | Number of items in gallery
    |--------------------------------------------------------------------------
    |
    | How many thumbnails are displayed in a single gallery on home page
    |
    */
    
    'gallery_items' => 3,
    
    /*
    |--------------------------------------------------------------------------
    | Number of items in timeline
    |--------------------------------------------------------------------------
    |
    | How many random thumbnails are displayed per decade
    |
    */
    
    'timeline_items' => 6,
    
    /*
    |--------------------------------------------------------------------------
    | Number of items to be imported per batch
    |--------------------------------------------------------------------------
    |
    | How many rows of a CSV file are imported in a single AJAX request.
    | Lower numbers provide better feedback to the user, higher numbers might
    | be a little faster. (default should be approx. 10)
    |
    */
    
    'import_batch_size' => 10,
    
    /*
    |--------------------------------------------------------------------------
    | Boundaries for date ranges / time spans
    |--------------------------------------------------------------------------
    |
    | Set the first and last year for form inputs representing a date range.
    | To make the upper boundary the current year, set 'end_year' to 'null',
    | without any quotes!
    | The boundary values could be overwritten for for any call of the included
    | blade view, please see explanation in
    | resources/views/includes/form_date_range.blade.php
    |
    */
    
    'start_year' => 1930,
    'end_year' => null,
    
    /*
    |--------------------------------------------------------------------------
    | Accepting terms is required for downloading
    |--------------------------------------------------------------------------
    |
    | A authenticated user must accept terms before downloading an item or image.
    |
    */
    
    'download_terms_auth' => true,
    
    /*
    |--------------------------------------------------------------------------
    | Accepting terms is required for uploading
    |--------------------------------------------------------------------------
    |
    | A authenticated user must accept terms before uploading an image / creating an item.
    |
    */
    
    'upload_terms_auth' => true,

];
