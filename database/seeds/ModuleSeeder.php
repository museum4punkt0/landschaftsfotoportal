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
        $this->addDownloadImage();
        $this->addTimeline();
        $this->addGallery();
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
        // Create module template for random image API
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
        // Create module template for specimen image
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

    public function addDownloadImage()
    {
        // Create module template for image download
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'download-image',
                'description' => 'link for downloading an image',
                'config' => '{
                    "name": {
                        "de": "Bild-Download",
                        "en": "Image download"
                    },
                    "description": {
                        "de": "Erzeugt einen Download-Link für Bilddateien.",
                        "en": "Creates a download link for image files."
                    },
                    "default_position": false,
                    "available_options": {
                        "image_path": {
                            "default": "' . config('media.full_dir') . '",
                            "data_type": "string",
                            "name": {
                                "de": "Pfad des Bilder-Verzeichnisses",
                                "en": "File path to image folder"
                            },
                            "help": {
                                "de": "Ordner in dem die Bilddateien liegen, relativ zum öffentlichen Medien-Ordner.",
                                "en": "Directory containing image files, relative to public media storage path."
                            }
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

    public function addTimeline()
    {
        // Create module template for timeline
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'timeline',
                'description' => 'timeline with images',
                'config' => '{
                    "name": {
                        "de": "Zeitstrahl",
                        "en": "Timeline"
                    },
                    "description": {
                        "de": "Zeitstrahl mit Bilddateien.",
                        "en": "Timeline with image files."
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
                        "daterange": {
                            "name": {
                                "de": "Datumsbereich/Zeitspanne",
                                "en": "Daterange/period of time"
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

    public function addGallery()
    {
        // Create module template for gallery
        DB::table('modules')->insertOrIgnore([
            [
                'name' => 'gallery',
                'description' => 'image gallery',
                'config' => '{
                    "name": {
                        "de": "Bilder-Galerie",
                        "en": "Image gallery"
                    },
                    "description": {
                        "de": "Galerie mit Bilddateien.",
                        "en": "Gallery with image files."
                    },
                    "default_position": false,
                    "available_options": {
                        "item_type": {
                            "name": {
                                "de": "Code des Datensatztyps (z.B. _image_)",
                                "en": "Code of item type (e.g. _image_)"
                            },
                            "data_type": "string",
                            "default": "_image_"
                        },
                        "heading-1": {
                            "name": {
                                "de": "Überschrift/Titel, Teil 1, (optional)",
                                "en": "Heading, part 1, (optional)"
                            },
                            "data_type": "column"
                        },
                        "heading-2": {
                            "name": {
                                "de": "Überschrift/Titel, Teil 2, (optional)",
                                "en": "Heading, part 2, (optional)"
                            },
                            "data_type": "column"
                        },
                        "heading-3": {
                            "name": {
                                "de": "Überschrift/Titel, Teil 3",
                                "en": "Heading, part 3"
                            },
                            "data_type": "column"
                        },
                        "subheading": {
                            "name": {
                                "de": "Zwischenüberschrift/Untertitel",
                                "en": "Subheading"
                            },
                            "data_type": "column"
                        },
                        "caption": {
                            "name": {
                                "de": "Bild-Beschreibung (Hover-Text)",
                                "en": "Image caption (hover text)"
                            },
                            "data_type": "column"
                        },
                        "missing": {
                            "name": {
                                "de": "Fehlendes Detail",
                                "en": "Missing detail"
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
}
