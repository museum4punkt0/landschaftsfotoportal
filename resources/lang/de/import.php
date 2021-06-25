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
    'parent_taxon' => 'Parent Taxon',
    'parent_details' => 'Parent Detail',
    'parent_item_type' => 'Parent Datensatztyp',
    'taxon_name' => 'Vollname des Taxons',
    'attribute_hint' => 'Wählen Sie für jede Tabellenspalte welche Art von Inhalt sie enthält.',
    'fullname_hint' => 'Wenn "Vollname" nicht ausgewählt wird oder leer ist, wird dieser aus den anderen Namensbestandteilen verkettet.',
    'parent_hint' => 'Wählen Sie optional einen übergeordneten Menü-Eintrag für alle zu importierenden Datensätze. Dies ist unabhängig vom übergeordneten Taxon.',
    'parent_item_type_hint' => 'Wählen Sie andernfalls den Datensatztyp der Elternelemente. Dies ist unabhängig vom übergeordneten Taxon.',
    'geocoder_hint' => 'Wählen Sie die passenden Spalten der CSV-Datei aus, welche die Ortsangaben beeinhalten.',
    
    'attribute_once' => '":attribute" darf nur für eine Spalte ausgewählt werden!',
    'missing_attributes' => 'Für mindestens eine Spalte muss der Inhalts-Typ (Attribut) ausgewählt werden!',
    'missing_columns' => 'Für mindestens eine Spalte muss der Inhalts-Typ (Anzeigefeld) ausgewählt werden!',
    'missing_id' => 'Es muss eine Spalte mit den Element-IDs ausgewählt werden!',
    'missing_parent' => 'Es muss eine Spalte mit den Parent-IDs ausgewählt werden!',
    
    'column_separator' => 'Trennzeichen für Tabellenspalten',
    'element_separator' => 'Trennzeichen für Elemente von Mehrfachauswahllisten',
    'contains_header' => 'CSV enthält in der ersten Zeile Spaltenüberschriften',
    'unique_taxa' => 'nur einzigartige (keine doppelten) Taxa importieren',
    'geocoder_use' => 'Geocoder-Dienst benutzen',
    'geocoder_interactive' => 'Interaktiver Geocoder: bei mehrdeutigen Ergebnissen nachfragen',
    'into_this_list' => 'Die CSV-Datei wird anschließend in die Liste ":name" (:description) importiert werden.',
    
    'items_count_import' => ':count Datensätze werden insgesamt importiert.',
    
    'read_csv' => 'CSV-Datei :file wird eingelesen.',
    'select_location' => 'Wählen Sie den Eintrag, welcher am besten den Originaldaten entspricht.',
    'item_imported' => 'Datensatz wurde mit ID :id importiert.',
    'element_mismatch' => 'Auswahlliste enthält kein Element namens ":element"!',
    'taxon_exists' => 'Der Datensatz für ":full_name" wurde nicht importiert, da das Taxon bereits existiert!',
    'taxon_not_found' => 'Der Datensatz für ":full_name" konnte nicht importiert werden, da das Taxon nicht existiert!',
    'done' => 'Import wurde abgeschlossen.',

];
