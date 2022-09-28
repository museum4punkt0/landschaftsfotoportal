<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Columns Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to manage columns in the backend.
    |
    */

    'header' => 'Anzeigefelder',
    'list' => 'Anzeigefeld',
    'new' => 'Neues Anzeigefeld',
    'edit' => 'Anzeigefeld bearbeiten',
    'delete' => 'Anzeigefeld löschen',
    'confirm_delete' => 'Möchten Sie das Anzeigefeld “:name” wirklich löschen?',
    'created' => 'Anzeigefeld wurde angelegt.',
    'updated' => 'Anzeigefeld wurde bearbeitet.',
    'deleted' => 'Anzeigefeld wurde gelöscht.',

    'description_help' => 'Die Beschreibung dient zur Unterscheidung und Suche im Backend und kann beliebig benannt werden. Falls gleichzeitig eine neue Feldzuordnung angelegt wird, kann dieses Feld leer bleiben und wird dann automitisch nach folgendem Schema ausgefüllt: "Name des Gliederungspunktes -> Name des Anzeigefeldes".',
    'data_type' => 'Datentyp',
    'data_type_help' => 'Dies legt fest, welchen Inhalt das Anzeigefeld enthalten und anzeigen kann. Die datentyp-spezifische Konfiguration kann mit Hilfe der Feld-Zuordnung erfolgen.',
    'list_help' => 'Die Auswahlliste, welche als Dropdown den Inhalt das Anzeigefeld vorgibt.',
    'translated_name' => 'Name in der momentan ausgewählten Sprache',
    'translated_name_help' => 'Wählen Sie einen Namen, mit dem das Anzeigefeld im Frontend und in Bearbeitungsformularen beschriftet werden soll, aus der Liste aus. Alternativ den letzten Eintrag namens “:new”, um einen neuen Namen anzulegen.',
    'new_translation' => 'Neuen Namen anlegen',
    'add_colmap' => 'Eine neue Zuordnung für dieses Anzeigefeld anlegen',
    'add_colmap_help' => 'Ohne Zuordnung zu einem Datensatztyp wird dieses Anzeigefeld nicht angezeigt werden können. Sie können die Zuordnung aber auch später noch festlegen, z.B. mittels Stapelverarbeitung.',
    'image_hint' => 'Beim Hochladen einer Bilddatei wird die bisherige überschrieben!',
    'image_not_available' => 'Keine Bilddatei vorhanden',
    'still_owned_by' => 'Anzeigefeld kann nicht gelöscht werden, da es (in Feld-Zuordnungen) verwendet wird!',
    'no_colmap' => 'Für dieses Anzeigefeld gibt es noch keine Feld-Zuordnung!',
];
