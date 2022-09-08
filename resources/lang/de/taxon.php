<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Taxon Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to manage the taxonomic tree in the backend.
    |
    | There should be lines for each column of 'taxon' database table to print proper
    | validation errors. The names of language lines and database columns must be equal.
    |
    */

    'header' => 'Taxa',
    'list' => 'Taxon',
    'new' => 'Neues Taxon',
    'edit' => 'Taxon bearbeiten',
    'delete' => 'Taxon löschen',
    'confirm_delete' => 'Möchten Sie das Taxon “:name” wirklich löschen?',
    'created' => 'Taxon wurde angelegt.',
    'updated' => 'Taxon wurde bearbeitet.',
    'deleted' => 'Taxon wurde gelöscht.',
    'still_owned_by_it' => 'Taxon kann nicht gelöscht werden, da es in Datensätzen verwendet wird!',
    'still_owned_by_cm' => 'Taxon kann nicht gelöscht werden, da es in Feld-Zurordnungen verwendet wird!',

    'related_items' => 'Verknüpfte Datensätze',
    'parent' => 'Übergeordnetes Taxon',
    'parent_help' => 'Das nächst-höhere übergeordnete, valide Taxon.',
    'anchestors' => 'Übergeordnete Taxa',
    'anchestors_ranks' => 'Anzahl der anzuzeigenden Ränge',
    'taxon_name' => 'Wissenschaftlicher Name',
    'taxon_author' => 'Autor des Namens',
    'taxon_suppl' => 'Namenszusatz',
    'full_name' => 'Vollname inkl. Autor und Zusatz',
    'native_name' => 'Umgangssprachlicher Name',
    'valid_name' => 'Valides Taxon',
    'valid_name_help' => 'Falls es sich um ein Synonym handelt, geben Sie hier das valide Taxon an.',
    'valid' => 'Taxon ist valide',
    'synonyms' => 'Synonyme',
    'rank' => 'Rang',
    'rank_abbr' => 'Rang-Kürzel',
    'gsl_id' => 'GermanSL SPECIES_NR',
    'bfn_namnr' => 'BfN NAMNR',
    'bfn_sipnr' => 'BfN SIPNR',
    
    'autocomplete_help' => 'Geben Sie einen Teil des wissenschaftl. oder umgangssprachl. Namens ein um danach zu suchen. Leer lassen, falls nicht zutreffend.',
    'circular_parent' => 'Das Elternelement kann kein Kindelement dieses Taxons sein.',
];
