<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configration for the frontend menus
    |--------------------------------------------------------------------------
    |
    | TODO
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Maximum levels for sidebar menu
    |--------------------------------------------------------------------------
    */
    'sidebar_max_levels' => 5,

    /*
    |--------------------------------------------------------------------------
    | Odering of menu items depending on menu level
    |--------------------------------------------------------------------------
    |
    | Any column names of the table 'items' are allowed,
    | e.g. 'item_id', 'title', 'created_at', 'item_type_fk'.
    |
    | It supports passing an array of sort operations to sort by multiple
    | attributes, e.g. [['item_typ_fk', 'desc'], ['title', 'asc']].
    | The default is [['title', 'desc']] and could be omitted.
    */
    'sidebar_item_order' => [
        0 => 'item_id',
        3 => [['item_type_fk', 'desc'], ['title', 'asc']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude menu items of given item type depending on menu level
    |--------------------------------------------------------------------------
    |
    | Menu items can be filtered for each menu level separately.
    | e.g. 2 => 188 means: hide items of item type 188 on menu level 2
    |
    | Menu levels start counting from 0, but level 0 cannot be filtered atm.
    | The default value is 0 (no item type) and could be omitted.
    */
    'sidebar_exclude_item_type' => [
        1 => 188,
        2 => 188,
        3 => 188,
        4 => 188,
        5 => 188,
    ],

    /*
    |--------------------------------------------------------------------------
    | Title of the item that contains the content for the frontend home page
    |--------------------------------------------------------------------------
    */
    'home_item_title' => 'Home',

];
