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
    'options' => 'Datentyp-spezifische Konfiguration',
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

    // Config options
    'option_show_title_label' => 'Name des Anzeigefeldes anzeigen',
    'option_show_title_help' => 'Bestimmt das Aussehen des Anzeigefeld-Namens im Frontend.',
    'option_show_title_true' => 'ja',
    'option_show_title_false' => 'nein',
    'option_show_title_hide' => 'verstecken (Platz wird für Anzeige des Inhalts verwendet)',
    'option_required_label' => 'Pflichtfeld',
    'option_required_help' => 'Falls gesetzt, darf das Feld beim Anlegen oder Editieren eines Datensatztes nicht leer bleiben.',
    'option_editable_label' => 'Bearbeiten erlaubt',
    'option_editable_help' => 'Schreibschutz ist nützlich für Felder, dessen Inhalt automatisch generiert werden soll, gilt jedoch nicht für Administratoren! Vollständiges Deaktivieren gilt für alle Nutzergruppen.',
    'option_editable_true' => 'ja (Feld kann bearbeitet werden)',
    'option_editable_false' => 'nein (Feld wird nicht in Bearbeitungsformularen angezeigt)',
    'option_editable_readonly' => 'schreibgeschützt (Feld wird in Bearbeitungsformularen nur lesbar angezeigt)',
    'option_taxon_show_label' => 'Schema zur Anzeige des Taxon-Namens',
    'option_taxon_show_help' => 'Legt fest, welche Information zum Taxon angezeigt wird.',
    'option_taxon_show_false' => 'kein Name',
    'option_taxon_show_full_name' => 'Wissenschaftl. Name',
    'option_taxon_show_native_name' => 'Vernakularname',
    'option_taxon_show_synonyms' => 'Liste der Synonyme',
    'option_taxon_parent_label' => 'Anzeige eines übergeordneten Taxons',
    'option_taxon_parent_help' => 'Rangkürzel eines übergeordneten Taxons (z.B. ORD, FAM, GAT), sonst leer lassen.',
    'option_embed_label' => 'Eingebettet anzeigen',
    'option_embed_help' => 'Alle öffentlichen Anzeigefelder des verknüpften Datensatzes werden angezeigt, andernfalls nur dessen Titel. (Nur im Frontend)',
    'option_show_link_label' => 'Link zum verknüpften Datensatz',
    'option_show_link_help' => 'Zeigt einen Link zum verknüpften Datensatzes, falls die Option “Eingebettet anzeigen” nicht akiviert ist. (Nur im Frontend)',
    'option_relation_item_type_label' => 'Verknüpfter Datensatztyp',
    'option_relation_item_type_help' => 'Der Datensatztyp, welchem die verknüpften Datensätze angehören.',
    'option_scale_factor_label' => 'Skalierungsfaktor',
    'option_scale_factor_help' => 'Der Faktor mit dem der anzuzeigende numerische Wert multipliziert wird. Leer lassen für Ausgabe des Originalwertes.',
    'option_precision_label' => 'Nachkommastellen',
    'option_precision_help' => 'Anzahl der anzuzeigenden Nachkommastellen. Negative Werte bewirken Rundung auf positive Zehnerpotenzen. Nur wirksam, falls der Skalierungsfaktor gesetzt ist. Leer lassen für Ausgabe des Originalwertes.',
    'option_textarea_label' => 'Mehrzeiliges Bearbeitungsfeld',
    'option_textarea_help' => 'Legt die Anzahl der Zeilen des Bearbeitungsfeldes innerhalb von Formularen fest.',
    'option_data_subtype_label' => 'Adressen-Datentyp',
    'option_data_subtype_help' => 'Beeinhaltet dieses Anzeigefeld einen Adressen-Bestandteil, so kann hier der Typ angegeben werden. Dies wird benötigt, um Ergebnisse des Geocoders aus der Adressensuche automatisch in dieses Feld eintragen zu können. Für Felder mit Lat-/Lon-Koordinaten ist dies erforderlich, um sie durch Klick in eine Karte aktualisieren zu können.',
    'option_data_subtype_false' => 'nein',
    'option_data_subtype_location_lat' => 'Geographische Breite einer Koordinate',
    'option_data_subtype_location_lon' => 'Geographische Länge einer Koordinate',
    'option_data_subtype_location_country' => 'Land',
    'option_data_subtype_location_state' => 'Bundesland',
    'option_data_subtype_location_county' => 'Landkreis',
    'option_data_subtype_location_city' => 'Stadt/Ort',
    'option_data_subtype_location_postcode' => 'Postleitzahl',
    'option_data_subtype_location_street' => 'Straßenname',
    'option_search_label' => 'Suchfeld',
    'option_search_help' => 'Falls “Adressen-Datentyp” auf “Stadt” gesetzt wurde, kann das Anzeigefeld hiermit zum Suchfeld gemacht werden.',
    'option_search_false' => 'nein',
    'option_search_address' => 'Adressen-Suchfeld mittels externem Geocoder',
    'option_date_min_label' => 'Datumsuntergrenze',
    'option_date_min_help' => 'Das älteste Datum, welches erlaubt ist.',
    'option_date_min_false' => 'Keine Datumsgrenze festgelegt',
    'option_date_min_current' => 'Jeweils aktuelles Datum (heute)',
    'option_date_min_date' => 'Konkret festgelegtes Datum',
    'option_date_min_date_label' => 'Konkrete Datumsuntergrenze',
    'option_date_min_date_help' => 'Falls “Datumsuntergrenze” auf “Konkret festgelegtes Datum” gesetzt wurde, muss hier das Datum angegeben werden.',
    'option_date_max_label' => 'Datumsobergrenze',
    'option_date_max_help' => 'Das neueste Datum, welches erlaubt ist.',
    'option_date_max_false' => 'Keine Datumsgrenze festgelegt',
    'option_date_max_current' => 'Jeweils aktuelles Datum (heute)',
    'option_date_max_date' => 'Konkret festgelegtes Datum',
    'option_date_max_date_label' => 'Konkrete Datumsobergrenze',
    'option_date_max_date_help' => 'Falls “Datumsobergrenze” auf “Konkret festgelegtes Datum” gesetzt wurde, muss hier das Datum angegeben werden.',
    'option_lower_bound_required_label' => 'Untergrenze Plichtfeld',
    'option_lower_bound_required_help' => 'Falls gesetzt, darf das Feld beim Anlegen oder Editieren eines Datensatztes nicht leer bleiben.',
    'option_upper_bound_required_label' => 'Obergrenze Plichtfeld',
    'option_upper_bound_required_help' => 'Falls gesetzt, darf das Feld beim Anlegen oder Editieren eines Datensatztes nicht leer bleiben.',
    'option_image_show_label' => 'Bilddatei-Anzeige',
    'option_image_show_help' => 'Bestimmt, wie die Bilddatei im Anzeigefeld angezeigt wird.',
    'option_image_show_filename' => 'Nur Dateiname statt Bild',
    'option_image_show_preview' => 'Kleine Vorschau des Bildes (Größe definiert in Datei “config/media.php”)',
    'option_image_show_gallery' => 'Beleg-Galerie Artseite (nur für Bestikri!)',
    'option_image_show_specimen' => 'Bilder-Galerie Belegseite (nur für Bestikri!)',
    'option_image_link_label' => 'Bild-Link',
    'option_image_link_help' => 'Die Bildanzeige kann mit einem Link versehen werden.',
    'option_image_link_false' => 'nein',
    'option_image_link_zoomify' => 'Link zum Zoomify-Viewer (Einstellungen in Datei “config/media.php”)',
    'option_image_title_col_label' => 'Anzeigefeld für Bild-Titel',
    'option_image_title_col_help' => 'Das Anzeigefeld, das den Titel enthält. Nur erforderlich, falls bei “Bilddatei-Anzeige” eine Bestikri-Galerie ausgewählt ist.',
    'option_image_copyright_col_label' => 'Anzeigefeld für Bild-Urhebervermerk',
    'option_image_copyright_col_help' => 'Das Anzeigefeld, das den Copyright-/Urhebervermerk enthält. Nur erforderlich, falls bei “Bilddatei-Anzeige” eine Bestikri-Galerie ausgewählt ist.',
    'option_image_ppi_col_label' => 'Anzeigefeld für Bild-Auflösung',
    'option_image_ppi_col_help' => 'Das Anzeigefeld, das den PPI-Wert enthält. Nur erforderlich, falls bei “Bilddatei-Anzeige” eine Bestikri-Galerie ausgewählt ist und der Zoomify-Viewer verwendet wird.',
    'option_image_size_col_label' => 'Anzeigefeld für Bild-Dateigröße',
    'option_image_size_col_help' => 'Sofern ein Ganzzahl-Anzeigefeld für die Dateigröße angelegt wurde, kann dieses hier ausgewählt werden. Bei diesem Feld sollte die Option “Bearbeiten erlaubt” auf “schreibgeschützt” oder “nein” gesetzt werden! Das Speichern der Größe erfolgt automatisch beim Anlegen des Datensatzes mit Bild.',
    'option_image_width_col_label' => 'Anzeigefeld für Bild-Breite',
    'option_image_width_col_help' => 'Sofern ein Ganzzahl-Anzeigefeld für die Bild-Breite angelegt wurde, kann dieses hier ausgewählt werden. Bei diesem Feld sollte die Option “Bearbeiten erlaubt” auf “schreibgeschützt” oder “nein” gesetzt werden! Das Speichern der Größe erfolgt automatisch beim Anlegen des Datensatzes mit Bild.',
    'option_image_height_col_label' => 'Anzeigefeld für Bild-Höhe',
    'option_image_height_col_help' => 'Sofern ein Ganzzahl-Anzeigefeld für die Bild-Höhe angelegt wurde, kann dieses hier ausgewählt werden. Bei diesem Feld sollte die Option “Bearbeiten erlaubt” auf “schreibgeschützt” oder “nein” gesetzt werden! Das Speichern der Größe erfolgt automatisch beim Anlegen des Datensatzes mit Bild.',
    'option_map_label' => 'Kartenanzeige',
    'option_map_help' => 'Die Karte kann intern mittels Koordinaten aus dem jeweiligen Datensatz oder dessen Kind-Datensätzen erzeugt werden. Alternativ kann in jedem Datensatz eine URL zu einem externen Kartendienst angegeben werden.',
    'option_map_inline' => 'Internes Kartenmodul verwenden',
    'option_map_iframe' => 'Von externem Mapserver generierte Karte in iframe einbetten',
    'option_map_iframe_label' => 'Externer Mapserver',
    'option_map_iframe_help' => 'Nur wirksam, falls der externe Mapserver bei “Kartenanzeige” gewählt ist.',
    'option_map_iframe_false' => 'nein',
    'option_map_iframe_url' => 'URL für eingebettete Karte in iframe verwenden',
    'option_map_iframe_service' => 'DEPRECATED! Service-URL laut “config/media.php” mit Parameter “artid”',
    'option_map_geolocation_label' => 'Standortbestimmung nutzen',
    'option_map_geolocation_help' => 'Die Standortbestimmung (Geolocation API) des Browsers für Start-Koordinaten nutzen. Dies wird z.B. beim Anlegen von Datensätzen verwendet, wenn noch keine Koordinaten vorhanden sind.',
    'option_map_zoom_label' => 'Start-Zoomlevel',
    'option_map_zoom_help' => 'Die Vergrößerungsstufe beim Initialisieren der Karte.',
    'option_map_lat_col_label' => 'Anzeigefeld für Geograph. Breite',
    'option_map_lat_col_help' => 'Das Anzeigefeld das die Geograph. Breite der darzustellenden Koordinate enthält.',
    'option_map_lon_col_label' => 'Anzeigefeld für Geograph. Länge',
    'option_map_lon_col_help' => 'Das Anzeigefeld das die Geograph. Länge der darzustellenden Koordinate enthält.',
    'option_map_title_col_label' => 'Anzeigefeld für Marker-Beschriftung',
    'option_map_title_col_help' => 'Das Anzeigefeld das den Text für die Marker-Beschriftung enthält. Nur erforderlich, falls “Marker-Beschriftung” aktiviert ist.',
    'option_marker_label_label' => 'Marker-Beschriftung',
    'option_marker_label_help' => 'Der Koordinaten-Marker kann mit einer Beschriftung/Label versehen werden. Nur wirksam, falls das “Anzeigefeld für Marker-Beschriftung” ausgewählt wurde!',
    'option_marker_color_label' => 'Marker-Farbe',
    'option_marker_color_help' => 'Die Farbe des Koordinaten-Marker. Nur wirksam, falls eine “Punkt-Koordinaten-Quelle” ausgewählt wurde!',
    'option_marker_scale_label' => 'Marker-Skalierung',
    'option_marker_scale_help' => 'Die Größe des Koordinaten-Marker als Skalierungsfaktor. Nur wirksam, falls eine “Punkt-Koordinaten-Quelle” ausgewählt wurde!',
    'option_scale_line_label' => 'Maßstab',
    'option_scale_line_help' => 'Zeigt in der linken unteren Ecke der Karte den aktuellen Maßstab an.',
    'option_mouse_position_label' => 'Mausposition',
    'option_mouse_position_help' => 'Zeigt in der rechten oberen Ecke die aktuellen Koordinaten des Mauszeigers an. Dieser Wert bestimmt die Anzahl der anzuzeigenden Nachkommastellen, Null deaktiviert die Funktion.',
    'option_map_item_type_label' => 'Datensatztyp-Filter',
    'option_map_item_type_help' => 'Koordinaten nur aus untergeordneten Datensätzen des ausgewählten Typs verwenden. Nur wirksam, falls eine “Punkt-Koordinaten-Quelle” ausgewählt wurde!',
    'option_points_label' => 'Punkt-Koordinaten-Quelle',
    'option_points_help' => 'Die Koordinaten des anzuzeigenden Markers in der Karte können aus verschiedenen Datensätzen stammen. Die Eltern-Kind-Beziehung zwischen den Datensätzen wird dabei durch die Menü-Zuordnung bestimmt.',
    'option_points_false' => 'Keine Punkte anzeigen',
    'option_points_self' => 'Lat/Lon-Anzeigefelder des eigenen Datensatzes',
    'option_points_children' => 'Lat/Lon-Anzeigefeldern von untergeordneten Datensätzen',
    'option_polygons_label' => 'Polygon-Koordinaten-Quelle',
    'option_polygons_help' => 'Die Koordinaten der anzuzeigenden Polygone in der Karte können aus verschiedenen Quellen stammen.',
    'option_polygons_false' => 'Keine Polygone anzeigen',
    'option_polygons_list' => 'GeoJSON in Auswahlliste',
    'option_polygons_list_label' => 'Polygon-Auswahlliste',
    'option_polygons_list_help' => 'Die Auswahlliste in deren Elemente-Konfiguration die Pfade der GeoJSON-Dateien enthalten sind. Nur wirksam, falls “GeoJSON in Auswahlliste” als “Polygon-Koordinaten-Quelle” ausgewählt wurde!',
    'option_wms_url_label' => 'WMS-Dienst-URL',
    'option_wms_url_help' => 'Experten-Einstellung: Falls ein zusätzlicher WMS-Layer als Hintergrundkarte eingebunden werden soll, kann hier dessen URL angegeben werden.',
    'option_wms_layers_label' => 'WMS-Layers',
    'option_wms_layers_help' => 'Experten-Einstellung: Komma-separierte Liste der anzuzeigenden Layer-Namen.',
    'option_wms_extent_label' => 'WMS-Extent',
    'option_wms_extent_help' => 'Experten-Einstellung: Extent der WMS-Layer als Array im Format “[123, 456, -78, -90]”',
];
