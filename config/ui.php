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
    | ressources/views/layouts/frontend_{FRONTEND_TEMPLATE_NAME}.blade.php
    |
    */
    
    'frontend_layout' => 'landschaftsfotoportal',

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

];
