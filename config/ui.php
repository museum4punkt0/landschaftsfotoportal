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
    'icon_cart_remove' => 'fa-bookmark',
    'icon_comment' => 'fa-comment',
    'icon_upload' => 'fa-upload',
    'icon_download' => 'fa-download',
    'icon_permalink' => 'fa-link',
    'icon_external_link' => 'fa-external-link-alt',
    'icon_published' => 'fa-eye',
    'icon_unpublished' => 'fa-eye-slash',
    'icon_description' => 'fa-info',
    'icon_items_own' => 'fa-images',
    'icon_email_address' => 'fa-at',
    'icon_show' => 'fa-tv',
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
    | Number of taxon anchestors
    |--------------------------------------------------------------------------
    |
    | How many anchestors of a taxon are displayed in admin backend
    |
    */
    
    'taxon_anchestors' => 5,
    
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
    | Max. length of caption on gallery items
    |--------------------------------------------------------------------------
    |
    | How many characters is the maximum length of a caption displayed on a
    | gallery image.
    |
    */
    
    'galery_caption_length' => 160,
    
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

    /*
    |--------------------------------------------------------------------------
    | Enable buttons for publishing comments in backend
    |--------------------------------------------------------------------------
    |
    | This affects 'Publish' per comment and 'Publish all'.
    | However, changing its state is always possible by editing the comment.
    |
    */

    'publish_comment' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable comments for items
    |--------------------------------------------------------------------------
    |
    | Disabling removes all the comment features at all.
    |
    */

    'comments' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable taxa management
    |--------------------------------------------------------------------------
    |
    | Disabling removes all the taxa features at all.
    |
    */

    'taxa' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable revisions (versions) for items
    |--------------------------------------------------------------------------
    |
    | Whenever an item is created or updated a revision is saved to database.
    |
    */

    'revisions' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable HTML for page titles of items
    |--------------------------------------------------------------------------
    |
    | This allows HTML tags in titles of content pages for items.
    |
    */

    'html_page_title' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable registration for users
    |--------------------------------------------------------------------------
    |
    | Allows users to register for an account.
    |
    */

    'user_registration' => (bool) env('USER_REGISTRATION', false),
];
