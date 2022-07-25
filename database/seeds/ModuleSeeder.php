<?php

use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addRandomImage();
        $this->addApiRandomImage();
        $this->addApiSpecimenImage();
    }

    public function addRandomImage()
    {
        // Create module template for random image
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'random-image',
                'description' => 'shows a random image at a certain module position',
                'config' => '{
                    "default_position": "content-module-right",
                    "name": {
                        "de": "Zufallsbild",
                        "en": "Random image"
                    },
                    "description": {
                        "de": "Zeigt ein zufälliges Bild an einer festgelegten Modul-Position an.",
                        "en": "Shows a random image at a certain module position."
                    },
                    "available_options": {
                        "blade_name": {
                            "default": "random_image",
                            "data_type": "string",
                            "name": {
                                "de": "Name des Blade-Templates",
                                "en": "Name of the Blade template"
                            },
                            "help": {
                                "de": "Experten-Einstellung: Das für die Ausgabe verwendete Blade-Template.",
                                "en": "Advanced option: Blade template for displaying the content."
                            }
                        },
                        "image_path": {
                            "default": "images/home-random",
                            "data_type": "string",
                            "name": {
                                "de": "Pfad des Bilder-Verzeichnisses",
                                "en": "File path to image folder"
                            },
                            "help": {
                                "de": "Ordner in dem die Bilddateien liegen, relativ zum öffentlichen Medien-Ordner.",
                                "en": "Directory containing image files, relative to public media storage path."
                            }
                        }
                    }
                }',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function addApiRandomImage()
    {
        // Create module template for random image
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'api-random-image',
                'description' => 'image related meta data for random image API',
                'config' => '{
                    "name": {
                        "de": "Bild-Metadaten",
                        "en": "Meta data of images"
                    },
                    "description": {
                        "de": "Bild-Metadaten für Zufallsbild-API",
                        "en": "Meta data of images for random image API"
                    },
                    "default_position": false,
                    "available_options": {
                        "city": {
                            "name": {
                                "de": "Stadt/Ort",
                                "en": "city"
                            },
                            "data_type": "column"
                        },
                        "state": {
                            "name": {
                                "de": "Bundesland",
                                "en": "state"
                            },
                            "data_type": "column"
                        },
                        "country": {
                            "name": {
                                "de": "Land",
                                "en": "country"
                            },
                            "data_type": "column"
                        },
                        "author": {
                            "name": {
                                "de": "Bild-Autor",
                                "en": "Image author"
                            },
                            "data_type": "column"
                        },
                        "copyright": {
                            "name": {
                                "de": "Lizenzvermerk",
                                "en": "Licence annotation"
                            },
                            "data_type": "column"
                        },
                        "description": {
                            "name": {
                                "de": "Bild-Beschreibung",
                                "en": "Image description"
                            },
                            "data_type": "column"
                        },
                        "filename": {
                            "name": {
                                "de": "Dateiname mit Endung",
                                "en": "File name including extension"
                            },
                            "data_type": "column"
                        }
                    }
                }',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function addApiSpecimenImage()
    {
        // Create module template for random image
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'api-specimen-image',
                'description' => 'image related meta data for specimen API',
                'config' => '{
                    "name": {
                        "de": "Bild-Metadaten",
                        "en": "Meta data of images"
                    },
                    "description": {
                        "de": "Bild-Metadaten für Specimen-API",
                        "en": "Meta data of images for specimen API"
                    },
                    "default_position": false,
                    "available_options": {
                        "filename": {
                            "name": {
                                "de": "Dateiname mit Endung",
                                "en": "File name including extension"
                            },
                            "data_type": "column"
                        },
                        "ppi": {
                            "name": {
                                "de": "Pixel pro Inch (PPI)",
                                "en": "Pixel per inch (PPI)"
                            },
                            "data_type": "column"
                        },
                        "title": {
                            "name": {
                                "de": "Bild-Titel",
                                "en": "Image title"
                            },
                            "data_type": "column"
                        },
                        "copyright": {
                            "name": {
                                "de": "Lizenzvermerk",
                                "en": "Licence annotation"
                            },
                            "data_type": "column"
                        }
                    }
                }',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
