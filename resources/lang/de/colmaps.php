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

    'header' => 'Feld-Zuordnungen',
    'list' => 'Feld-Zuordnung',
    'new' => 'Neue Feld-Zuordnung',
    'edit' => 'Feld-Zuordnung bearbeiten',
    'delete' => 'Feld-Zuordnung löschen',
    'confirm_delete' => 'Möchten Sie die Feld-Zuordnung “:name” wirklich löschen?',
    'created' => 'Feld-Zuordnung wurde angelegt.',
    'created_num' => ':count Feld-Zuordnungen wurden angelegt.',
    'updated' => 'Feld-Zuordnung wurde bearbeitet.',
    'deleted' => 'Feld-Zuordnung wurde gelöscht.',
    
    'published' => 'Das Anzeigefeld ist nun öffentlich sichtbar.',
    'unpublished' => 'Das Anzeigefeld ist nun nicht mehr öffentlich sichtbar.',

    'config' => 'Konfiguration (JSON-Format)',
    'api_attribute' => 'Attribute-Name bei Ausgabe über die API',
    'item_type' => 'Datensatztyp',
    'item_type_help' => 'Legt fest, für welchen Datensatztyp dieses Anzeigefeld verwendet wird.',
    'column_help' => 'Das Anzeigefeld, dessen Verwendung konfiguriert werden soll.',
    'column_group' => 'Gliederungspunkt',
    'column_group_help' => 'Die Zwischenüberschrift unter der das Anzeigefeld gruppiert und angezeigt wird.',
    'taxon' => 'Verwendung bei Taxon',
    'taxon_help' => 'Sie können ein Anzeigefeld auf die Verwendung bei einem bestimmten Taxon und deres untergeordneten Taxa einschränken. Dies ist z.B. sinnvoll, um Anzeigefelder für gattungsspezifische Merkmale zu definieren.',
    'public' => 'Öffentliche Sichtbarkeit',
    'public_help' => 'Für jedes Anzeigefeld ist wählbar, ob es im Frontend öffentlich sichtbar sein soll.',
    'column_order' => 'Reihenfolge der Anzeigefelder',
    'column_order_help' => 'Dieses Feld hat informativen Charakter. Sie sollten die Reihenfolge mittels "Feld-Zuordnungen" -> "Sortieren" ändern!',
    'sort_end' => 'Am Ende einsortieren',
    'sort_end_help' => 'Das Anzeigefeld wird automatisch nach allen bisher vorhandenen Anzeigefeldern für diesen Datensatztyp einsortiert. Andernfalls erscheint es undefiniert am Anfang.',
    'sort_for' => 'Sortierung von Anzeigefeldern für: ',
    'sort_hint' => 'Die Sortierung der Anzeigefelder kann mittels Drag&Drop vorgenommen werden.',
    'mapping_for' => 'Zuordnung von Anzeigefeldern für: ',
    'mapped' => 'Zugeordnete Anzeigefelder',
    'mapped_hint' => 'Diese Anzeigefelder sind bereits mit dem ausgewählten Typ von Datensätzen verknüpft.',
    'unmapped' => 'Verfügbare unzugeordnete Anzeigefelder',
    'unmapped_hint' => 'Diese Anzeigefelder können dem ausgewählten Typ von Datensätzen zugewiesen werden. Mehrfachauswahl ist mit der Taste [Strg] möglich.',
    'details_added' => 'Fehlende Anzeigefelder wurden zu :count Datensätzen hinzugefügt.',
    'edit_danger' => 'Sie bearbeiten Feldzuordnungen. Dies wird Ihre Daten unbrauchbar machen!',
    'none_available' => 'Diesem Datensatztyp wurden noch keine Anzeigefelder zugeordnet.',
    'no_item_type' => 'Es wurde noch kein Datensatztyp angelegt!',
];
