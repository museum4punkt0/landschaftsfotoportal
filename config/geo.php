<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration for the geocoder and map related things
    |--------------------------------------------------------------------------
    |
    | TODO
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Settings for the external geocoder service
    |--------------------------------------------------------------------------
    |
    | URLs without query parameters
    |
    */
    
    'geocoder_url' => 'http://open.mapquestapi.com/nominatim/v1/search.php?',
    'reverse_geocoder_url' => 'http://open.mapquestapi.com/nominatim/v1/reverse.php?',
    'api_key' => 'FIR28iSHzTDis7Nj8GWEewkfT9gajo1j',

    /*
    |--------------------------------------------------------------------------
    | Usage of browser's geolocation service
    |--------------------------------------------------------------------------
    |
    | Try to use the built-in geolocation service if available and allowed by
    | the user. If this fails for any reaon, the config options 'map_lat' and
    | 'map_lon' will be used instead.
    |
    */
    
    'use_geolocation' => true,

    /*
    |--------------------------------------------------------------------------
    | Settings for the slippy map
    |--------------------------------------------------------------------------
    |
    | Coordinates and zoom for map initialisation
    | Used only if config option 'use_geolocation' is not set or false
    |
    */
    
    'map_lat' => 51.1,
    'map_lon' => 14.5,
    'map_zoom' => 10,

];
