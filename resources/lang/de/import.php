<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Common Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for common words and phrases.
    |
    */

    'header' => 'Import',
    'import' => 'importieren',
    'lists_hint' => 'Hiermit kann der Inhalt von Auswahllisten aus CSV-Dateien importiert werden. Die entsprechende Liste muss vorher angelegt werden.',
    'taxa_hint' => 'Hiermit kann eine Taxliste aus einer CSV-Datei importiert werden. Bestehende Taxa bleiben erhalten.',
    'items_hint' => 'Hiermit können Datensätze aus einer CSV-Datei importiert werden.',
    'file_hint' => 'Laden Sie eine gültige CSV-Datei (max. 4 MB) hoch.',
    'upload_error' => 'Fehler beim Hochladen: ',
    'save_error' => 'Speichern der Datei fehlgeschlagen.',
    'firstrow' => 'Inhalt 1. Zeile',
    'nextrows' => 'Inhalt der nächsten Zeilen',
    'element_id' => 'Element ID',
    'parent_id' => 'Parent ID',
    'taxon_name' => 'Name des Taxons',
    'attribute_hint' => 'Wählen Sie für jede Tabellenspalte welche Art von Inhalt sie enthält.',
    'fullname_hint' => 'Wenn "Vollname" nicht ausgewählt wird oder leer ist, wird dieser aus den anderen Namensbestandteilen verkettet.',
    'parent_hint' => 'Wählen Sie den übergeordneten Menü-Eintrag, dies ist unabhängig vom übergeordneten Taxon.',
    'attribute_once' => '":attribute" darf nur für eine Spalte ausgewählt werden!',
    'missing_attributes' => 'Für mindestens eine Spalte muss der Inhalts-Typ (Attribut) ausgewählt werden!',
    'missing_columns' => 'Für mindestens eine Spalte muss der Inhalts-Typ (Anzeigefeld) ausgewählt werden!',
    'missing_id' => 'Es muss eine Spalte mit den Element-IDs ausgewählt werden!',
    'missing_parent' => 'Es muss eine Spalte mit den Parent-IDs ausgewählt werden!',
    'contains_header' => 'CSV enthält in der ersten Zeile Spaltenüberschriften',
    'unique_taxa' => 'nur einzigartige (keine doppelten) Taxa importieren',
    'into_this_list' => 'Die CSV-Datei wird anschließend in die Liste ":name" (:description) importiert werden.',
    'taxon_exists' => 'Der Datensatz für ":full_name" wurde nicht importiert, da das Taxon bereits existiert!',
    'taxon_not_found' => 'Der Datensatz für ":full_name" konnte nicht importiert werden, da das Taxon nicht existiert!',
    'done' => 'Import wurde abgeschlossen.',

];
