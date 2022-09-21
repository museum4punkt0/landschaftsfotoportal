<?php

namespace Database\Seeders;

use App\Attribute;
use App\Element;
use App\Selectlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addInitialLists();
        $this->addDataTypeRelation();
        $this->addDataTypeConfig();
    }

    public function addInitialLists()
    {
        // Create default lists
        $data_type_list = Selectlist::create([
            'name' => '_data_type_',
            'description' => 'Daten-Typen für Anzeigefelder',
            'hierarchical' => false,
            'internal' => true,
        ]);
        $item_type_list = Selectlist::create([
            'name' => '_item_type_',
            'description' => 'Inhalts-Typen für Datensätze',
            'hierarchical' => false,
            'internal' => true,
        ]);
        $translation_list = Selectlist::create([
            'name' => '_translation_',
            'description' => 'Übersetzte Namen und Beschreibungen für Anzeigespalten',
            'hierarchical' => false,
            'internal' => true,
        ]);
        $column_group_list = Selectlist::create([
            'name' => '_column_group_',
            'description' => 'Gliederungspunkte für Anzeigefelder',
            'hierarchical' => false,
            'internal' => true,
        ]);
        
        // Create list elements for data types and their names
        
        // Data type: list
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_list_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Auswahlliste']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'dropdown list']
        );
        // Data type: multi selection list
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_multi_list_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Auswahlliste mit Mehrfachauswahl']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'dropdown list with multiple selection']
        );
        // Data type: boolean
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_boolean_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Checkbox']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'checkbox']
        );
        // Data type: integer
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_integer_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Ganzzahlwert']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'integer value']
        );
        // Data type: float
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_float_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Fließkommawert']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'float value']
        );
        // Data type: string
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_string_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Zeichenkette']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'text value']
        );
        // Data type: date
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_date_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Datumswert']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'date value']
        );
        // Data type: date range
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_date_range_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Zeitspanne']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'date range']
        );
        // Data type: url
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_url_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Weblink (URL)']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'web link (URL)']
        );
        // Data type: image
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_image_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Bilddatei']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'image file']
        );
        // Data type: map
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_map_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Karte']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'map']
        );
        // Data type: taxon
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_taxon_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Taxon']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'taxon']
        );
        // Data type: html
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_html_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'HTML Text']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'HTML text']
        );
        // Data type: image title
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_image_title_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Bildtitel']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'image title']
        );
        // Data type: image copyright
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_image_copyright_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Bildrechte']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'image copyright']
        );
        // Data type: image ppi
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_image_ppi_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Bild-ppi (Pixel pro Zoll)']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'image ppi (pixels per inch)']
        );
        // Data type: redirect
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_redirect_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Weiterleitungsziel']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'target for redirection']
        );
    }

    public function addDataTypeRelation()
    {
        $data_type_list = Selectlist::where([
            'name' => '_data_type_',
            'internal' => true,
        ])->first();
        // Data type: relation
        $element = Element::create([
            'list_fk' => $data_type_list->list_id,
            'parent_fk' => null,
            'value_summary' => '',
        ]);
        $element->attributes()->attach(
            Attribute::where('name', 'code')->value('attribute_id'),
            ['value' => '_relation_']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_de')->value('attribute_id'),
            ['value' => 'Beziehung zu anderem Datensatz']
        );
        $element->attributes()->attach(
            Attribute::where('name', 'name_en')->value('attribute_id'),
            ['value' => 'relation to other record']
        );
    }

    public function addDataTypeConfig()
    {
        $data_type_list = Selectlist::where([
            'name' => '_data_type_',
            'internal' => true,
        ])->first();
        $config_attribute_id = Attribute::where('name', 'config')->value('attribute_id');

        // Some options used by many data types
        $show_title = [
            "default" => true,
            "data_type" => "select",
            "select_options" => [
                "true" => true,
                "false" => false,
                "hide" => "hide",
            ],
        ];
        $required = [
            "default" => false,
            "data_type" => "bool",
        ];
        $editable = [
            "default" => true,
            "data_type" => "select",
            "select_options" => [
                "true" => true,
                "false" => false,
                "readonly" => "readonly",
            ],
        ];

        // Data type: relation
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "embed" => [
                "default" => false,
                "data_type" => "bool",
            ],
            "show_link" => [
                "default" => false,
                "data_type" => "bool",
            ],
            "relation_item_type" => [
                "data_type" => "item_type",
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_relation_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: taxon
        $options = ["available_options" => [
            "show_title" => $show_title,
            "taxon_show" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "full_name" => "full_name",
                    "native_name" => "native_name",
                    "synonyms" => "synonyms",
                ],
            ],
            "taxon_parent" => [
                "default" => false,
                "data_type" => "string",
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_taxon_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: list
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_list_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: list with multiple selection
        // Use same options as for list
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_multi_list_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: boolean
        $options = ["available_options" => [
            "show_title" => $show_title,
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_boolean_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: integer
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "scale_factor" => [
                "default" => 1,
                "data_type" => "float",
            ],
            "precision" => [
                "default" => 0,
                "data_type" => "integer",
                "min" => -6,
                "max" => 12,
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_integer_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: float
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "scale_factor" => [
                "default" => 1,
                "data_type" => "float",
            ],
            "precision" => [
                "default" => 0,
                "data_type" => "integer",
                "min" => -6,
                "max" => 12,
            ],
            "data_subtype" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "location_lat" => "location_lat",
                    "location_lon" => "location_lon",
                ],
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_float_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: string
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "textarea" => [
                "default" => 0,
                "data_type" => "integer",
                "min" => 0,
                "max" => 10,
            ],
            "data_subtype" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "location_country" => "location_country",
                    "location_state" => "location_state",
                    "location_county" => "location_county",
                    "location_city" => "location_city",
                    "location_postcode" => "location_postcode",
                    "location_street" => "location_street",
                ],
            ],
            "search" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "address" => "address",
                ],
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_string_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: html
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "textarea" => [
                "default" => 5,
                "data_type" => "integer",
                "min" => 3,
                "max" => 15,
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_html_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: url
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_url_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: date
        // Use same options as for url
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_date_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: date range
        // Use same options as for date
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_date_range_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: image
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "image_show" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "filename" => "filename",
                    "preview" => "preview",
                    "gallery" => "gallery",
                    "specimen" => "specimen",
                ],
            ],
            "image_link" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "zoomify" => "zoomify",
                ],
            ],
            "image_size_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_integer_",
            ],
            "image_width_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_integer_",
            ],
            "image_height_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_integer_",
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_image_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);

        // Data type: map
        $options = ["available_options" => [
            "show_title" => $show_title,
            "required" => $required,
            "editable" => $editable,
            "map" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "inline" => "inline",
                    "iframe" => "iframe",
                ],
            ],
            "map_iframe" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "url" => "url",
                    "service" => "service",
                ],
            ],
            "map_geolocation" => [
                "default" => false,
                "data_type" => "bool",
            ],
            "map_zoom" => [
                "default" => 12,
                "data_type" => "integer",
                "min" => 1,
                "max" => 19,
            ],
            "map_lat_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_float_",
            ],
            "map_lon_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_float_",
            ],
            "map_title_col" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_string_",
            ],
            "marker_label" => [
                "default" => false,
                "data_type" => "bool",
            ],
            "marker_color" => [
                "default" => "#ffffff",
                "data_type" => "color",
            ],
            "marker_scale" => [
                "default" => 1,
                "data_type" => "float",
                "min" => 0.25,
                "max" => 3,
                "step" => 0.05,
            ],
            "scale_line" => [
                "default" => false,
                "data_type" => "bool",
            ],
            "mouse_position" => [
                "default" => 4,
                "data_type" => "integer",
                "min" => 0,
                "max" => 6,
            ],
            "map_item_type" => [
                "data_type" => "item_type",
            ],
            "points" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "self" => "self",
                    "children" => "children",
                ],
            ],
            "polygons" => [
                "default" => false,
                "data_type" => "select",
                "select_options" => [
                    "false" => false,
                    "list" => "list",
                ],
            ],
            "polygons_list" => [
                "default" => false,
                "data_type" => "column",
                "column_data_type" => "_list_",
            ],
            "wms_url" => [
                "default" => false,
                "data_type" => "string",
            ],
            "wms_layers" => [
                "default" => false,
                "data_type" => "string",
            ],
            "wms_extent" => [
                "default" => false,
                "data_type" => "string",
            ],
        ]];
        $element = Element::whereHas('values', function (Builder $query) {
            $query->where('value', '_map_');
        })->firstWhere('list_fk', $data_type_list->list_id);

        $element->attributes()->syncWithoutDetaching([$config_attribute_id =>
            ['value' => json_encode($options)]
        ]);
    }
}
