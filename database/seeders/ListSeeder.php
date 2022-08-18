<?php

namespace Database\Seeders;

use App\Attribute;
use App\Element;
use App\Selectlist;
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
}
