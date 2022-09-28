<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Items Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to manage items in the backend.
    |
    */

    'header' => 'Datensätze',
    'list' => 'Datensatz',
    'my_own' => 'Meine eigenen Datensätze',
    'unpublished' => 'Unveröffentlichte Datensätze',
    'new' => 'Neuer Datensatz',
    'edit' => 'Datensatz bearbeiten',
    'delete' => 'Datensatz löschen',
    'confirm_delete' => 'Möchten Sie den Datensatz “:name” wirklich löschen?',
    'created' => 'Datensatz wurde angelegt.',
    'updated' => 'Datensatz wurde bearbeitet.',
    'deleted' => 'Datensatz wurde gelöscht.',
    
    'item_type' => 'Datensatztyp',
    'related_taxon' => 'Verknüpftes Taxon',
    'related_item' => 'Verknüpfter Datensatz',
    'new_related_info' => 'Nach dem Speichern des neu angelegten, verknüpften Datensatzes kann dieser Browser-Tab/Fenster geschlossen werden. Anschließend bearbeiten Sie den ursprünglichen Datensatzes weiter ohne Neuladen.',
    'no_title_column' => 'Kein Anzeigefeld als Name definiert.',
    'no_iframe' => 'Ihr Browser kann leider keine eingebetteten Frames anzeigen.',
    'no_position_for_map' => 'Es liegen keine genauen Ortsangaben für die Kartendarstellung vor.',
    'no_detail_for_column' => 'Kein Eintrag für Anzeigespalte #:column in der Datenbank vorhanden!',
    'show_frontend' => 'Im Frontend anzeigen',
    'autocomplete_help' => 'Geben Sie den Anfang des Namens (Menü-Titel) ein. Leer lassen, falls nicht zutreffend.',
    'title' => 'Datensatzname',
    'menu_hierarchy' => 'Menü-Hierarchie',
    'menu_title' => 'Name des Datensatzes (im Menü angezeigt)',
    'page_title' => 'Titel des Datensatzes (als Seitenüberschrift angezeigt)',

    'add_titles' => 'Datensatz-Namen aktualisieren',
    'add_titles_hint' => 'Achtung, dies kann lange dauern! Falls am Ende nur eine leere Seite angezeigt wird, bitte im Browser die Seite neu laden: [Strg]+[R].',
    'titles_item_type_help' => 'Datensatztyp für den die Namen aller Datensätze generiert oder aktualisiert werden sollen.',
    'name_schema' => 'Namensschema',
    'name_schema_0' => 'Von Anzeigefeld übernehmen, welches im Datensatztyp als Datensatz-Name konfiguriert wurde',
    'name_schema_1' => 'Von Taxon: Wiss. Name + Autor + Zusatz (z.B. Crataegus subsphaerica Gand. s. l.)',
    'name_schema_2' => 'Von Taxon: Wiss. Name (z.B. Crataegus subsphaerica)',
    'name_schema_3' => 'Von Taxon: Wiss. Name + Zusatz (z.B. Crataegus subsphaerica s. l.)',
    'name_schema_4' => 'Von Taxon: Wiss. Name, gekürzt + Zusatz (z.B. C. subsphaerica s. l.)',
    'name_schema_5' => 'Von Taxon: Wiss. Name, gekürzt + Autor + Zusatz (z.B. C. subsphaerica Gand. s. l.)',
    'name_schema_help' => 'Schema nach dem die Namen der ausgewählten Datensätze erzeugt werden sollen.',
    'titles_update' => 'Alle aktualisieren',
    'titles_update_help' => 'Falls ausgewählt, werden die Namen aller Datensätze aktualisiert bzw. neu erzeugt. Andernfalls werden nur Datensätze ohne Namen bearbeitet.',
    'titles_added' => 'Für :count Datensätze wurde ein Titel übernommen.',

    'remove_orphans' => 'Details bereinigen',
    'orphans_removed' => ':count verwaiste Details wurden bereinigt.',
    'published' => ':count Datensätze wurden veröffentlicht.',
    'publish_not_available' => 'Sie können Datensätze nicht direkt veröffentlichen. Nutzen Sie stattdessen die Moderations-Werkzeuge.',
    'editing_draft' => 'Sie bearbeiten einen noch nicht veröffentlichten Entwurf.',

    'removed_after_deleting_drafts' => 'Datensatz gelöscht, nachdem alle Entwürfe entfernt wurden.',
    'added_missing_detail' => 'Fehlendes Detail gespeichert.',
    'image_preview' => 'Bild-Vorschau',
    'image_dimensions' => 'Bildgröße',
    'image_size' => 'Dateigröße',
    'no_column_for_image_width' => 'Keine Anzeigespalte für Bildbreite konfiguriert!',
    'no_column_for_image_height' => 'Keine Anzeigespalte für Bildhöhe konfiguriert!',
    'no_column_for_image_size' => 'Keine Anzeigespalte für Bildgröße konfiguriert!',
    'no_config_for_colmap' => 'Fehlende Konfiguration für Feld-Zuordnung #:colmap: ',
    'file_not_found' => 'Datei ":file" nicht vorhanden!',
    'file_max_size' => 'Die Datei darf maximal :max Kilobytes groß sein.',
    'resized_image_created' => 'Bild wurde in geänderter Größe gespeichert: ',
    'file_ext_fixed' => 'Für :count Bild-Datensätze wurde die Endung des Dateinamens korrigiert.',
    'no_home_page' => 'Es wurde keine Seite als Homepage konfiguriert.',
];
