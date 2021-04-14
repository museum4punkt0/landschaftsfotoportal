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
    | Settings for the slippy map
    |--------------------------------------------------------------------------
    |
    | Coordinates and zoom for map initialisation
    |
    */
    
    'map_lat' => 51.1,
    'map_lon' => 14.5,
    'map_zoom' => 10,

];
