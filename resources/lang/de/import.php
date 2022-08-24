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
    'import' => 'Importieren',
    'lists_hint' => 'Hiermit kann der Inhalt von Auswahllisten aus CSV-Dateien importiert werden. Die entsprechende Liste muss vorher angelegt werden.',
    'taxa_hint' => 'Hiermit kann eine Taxliste aus einer CSV-Datei importiert werden. Bestehende Taxa bleiben erhalten.',
    'items_hint' => 'Hiermit können Datensätze aus einer CSV-Datei importiert werden.',
    'file' => 'Zu importierende Datei',
    'file_hint' => 'Laden Sie eine gültige CSV-Datei (max. 4 MB) hoch.',
    'upload_error' => 'Fehler beim Hochladen: ',
    'save_error' => 'Speichern der Datei fehlgeschlagen.',
    'firstrow' => 'Inhalt 1. Zeile',
    'nextrows' => 'Inhalt der nächsten Zeilen',
    'element_id' => 'Element ID',
    'parent_id' => 'Parent ID',
    'parent_taxon' => 'Elternelement mittels Taxon',
    'parent_details' => 'Elternelement mittels Detail',
    'parent_item_type' => 'Datensatztyp des Elternelements',
    'taxon_name' => 'Taxon mittels Vollname',
    'related_item' => 'anderer Datensatz mittels Detail',
    'attribute_hint' => 'Wählen Sie für jede Spalte der CSV-Datei aus, welchem Anzeigefeld ihr Inhalt zugeordnet werden soll. Ignorierte Spalten werden nicht importiert.',
    'fullname_hint' => 'Wenn "Vollname" nicht ausgewählt wird oder leer ist, wird dieser aus den anderen Namensbestandteilen verkettet.',
    'parent_hint' => 'Wählen Sie optional einen gemeinsamen übergeordneten Menü-Eintrag für alle zu importierenden Datensätze. Falls für eine Spalte der CSV-Datei eine "Beziehung: Elternelement" ausgewählt wurde, so hat jenes Vorrang. Dies ist unabhängig vom übergeordneten Taxon.',
    'parent_item_type_hint' => 'Falls für eine Spalte der CSV-Datei eine "Beziehung: Elternelement" ausgewählt wurde, können Sie den Datensatztyp der Elternelemente hiermit einschränken. Dies ist unabhängig vom übergeordneten Taxon.',
    'public_hint' => 'Wählen Sie, ob alle importierten Datensätze auch veröffentlicht werden sollen.',
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
    'geocoder_cache_selected' => 'Auswahl für diesen Ort merken',
    'into_this_list' => 'Die CSV-Datei wird anschließend in die Liste ":name" (:description) importiert werden.',
    
    'items_count_import' => ':count Datensätze werden insgesamt importiert.',
    'skip_lines' => 'Anzahl der zu überspingenden Datensätze (0: keine, beginne am Anfang)',
    
    'read_csv' => 'CSV-Datei :file wird eingelesen.',
    'csv_line' => 'CSV-Zeile :line: ',
    'select_location' => 'Wählen Sie den Eintrag, welcher am besten den Originaldaten entspricht.',
    'item_imported' => 'Datensatz wurde mit ID :id importiert.',
    'element_mismatch' => 'Auswahlliste ":list" enthält kein Element namens ":element"!',
    'taxon_exists' => 'Der Datensatz für ":full_name" wurde nicht importiert, da das Taxon bereits existiert!',
    'taxon_not_found' => 'Der Datensatz für ":full_name" konnte nicht importiert werden, da das Taxon nicht existiert!',
    'parent_taxon_not_found' => 'Dem Datensatz konnte kein individueller Elterndatensatz zugewiesen werden, da keiner mit dem Taxon ":full_name" existiert!',
    'parent_detail_not_found' => 'Dem Datensatz konnte kein individueller Elterndatensatz zugewiesen werden, da keiner mit dem Detail ":detail" existiert!',
    'related_item_not_found' => 'Dem Datensatz konnte kein verknüpfter Datensatz zugewiesen werden, da keiner mit dem Detail ":detail" existiert!',
    'related_item_found' => 'Verknüpfung zu Datensatz ID :id mit Detail ":detail" hergestellt.',
    'done' => 'Import wurde abgeschlossen.',

];
