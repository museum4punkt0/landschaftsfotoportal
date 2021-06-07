<?php

namespace App\Utils;

use App\Selectlist;
use App\Value;

class Localization
{
    /**
     * Get localized values for columns, to be used in blade views.
     *
     * The type depends on localized attributes defined in database table 'attributes'.
     *
     * @param  string $lang
     * @param  string $type
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getTranslations($lang, $type)
    {
        $translations = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_translation_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang, $type) {
            $query->where('name', $type .'_'. $lang);
        })
        ->with(['attribute'])
        ->get();
        
        return $translations;
    }
    
    /**
     * Get data types of columns with localized names, to be used in blade views.
     *
     * @param  string $lang
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getDataTypes($lang)
    {
        $data_types = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_data_type_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', 'name_'. $lang);
        })
        ->with(['attribute'])
        ->get();
        
        return $data_types;
    }
}
