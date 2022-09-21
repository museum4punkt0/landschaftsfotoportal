<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Column Mapping Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to manage column mapping in the backend.
    |
    */

    'config' => 'Configuration (JSON format)',
    'options' => 'Data type related config options',

    // Config options
    'option_required_label' => 'Required',
    'option_required_help' => 'The field must not be empty when creating or editing a record.',
    'option_embed_label' => 'Show mbedded',
    'option_embed_help' => 'All public fields of the related record are shown, otherwise its title only. (Only available in frontend)',
    'option_show_link_label' => 'Link to embedded record',
    'option_show_link_help' => 'Show a link to the related record, if option “Show embedded” is not set. (Only available in frontend)',
    'option_item_type_label' => 'Type of related records',
    'option_item_type_help' => 'The type of record, the related records belong to.',
];
