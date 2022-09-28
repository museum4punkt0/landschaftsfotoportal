<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration for the API
    |--------------------------------------------------------------------------
    |
    | TODO
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Filter for item type to be included in API response
    |--------------------------------------------------------------------------
    |
    | The response of the API endpoint 'taxon/fwTaxonId/{taxon}/items' contains
    | items of one single item type defined here.
    |
    */
    
    'items' => [
        'item_type' => env('API_ITEM_TYPE', '_specimen_'),
        'basis_of_record' => env('API_BASIS_OF_RECORD', 'PRESERVED_SPECIMEN'),
        'reference_route' => env('API_ITEM_ROUTE', 'api.item.show.specimen'),
    ],

];
