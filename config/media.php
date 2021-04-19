<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media storage
    |--------------------------------------------------------------------------
    |
    | All directories are relative to /storage/app/public/ and must contain a
    | trailing slash.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Directory for storing CSV files during import of lists and items
    |--------------------------------------------------------------------------
    */
    'import_dir' => 'import/',

    /*
    |--------------------------------------------------------------------------
    | Directory for storing image files in full/original size
    |--------------------------------------------------------------------------
    */
    'full_dir' => 'images/full/',

    /*
    |--------------------------------------------------------------------------
    | Directory for storing image files in medium size
    |--------------------------------------------------------------------------
    */
    'medium_dir' => 'images/medium/',
    
    'medium_width' => 1200,
    'medium_height' => 800,

    /*
    |--------------------------------------------------------------------------
    | Directory for storing image files in thumbnail size
    |--------------------------------------------------------------------------
    */
    'preview_dir' => 'images/preview/',

    'preview_width' => 375,
    'preview_height' => 250,

    /*
    |--------------------------------------------------------------------------
    | Parameters for zoomify image viewer
    |--------------------------------------------------------------------------
    */
    'zoomify_url' => 'https://webapp.senckenberg.de/zoomify/index_full.html?',
    'zoomify_zif_image_path' => 'bilder/aq-media/bestikri/images_tiled/2/',
    'zoomify_jpg_image_path' => 'bilder/aq-media/bestikri/images/2/',
    'mapservice_url' => 'https://mapservice.senckenberg.de/sgn-projekt/bestrikri/full-map-template.html?',

];
