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
        'item_type' => '_specimen_',
        'basis_of_record' => 'PRESERVED_SPECIMEN',
    ],

];
