<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'column_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data_type_fk',
        'translation_fk',
        'list_fk',
        'description',
    ];
    
    /**
     * Get the element that owns the column.
     */
    public function data_type()
    {
        return $this->belongsTo('App\Element', 'data_type_fk', 'element_id');
    }
    
    /**
     * Get the element that owns the column.
     */
    public function translation()
    {
        return $this->belongsTo('App\Element', 'translation_fk', 'element_id');
    }
    
    /**
     * Get the list that owns the column.
     */
    public function list()
    {
        return $this->belongsTo('App\Selectlist', 'list_fk', 'list_id');
    }
    
    /**
     * The items that belong to the column.
     */
    public function items()
    {
        return $this->belongsToMany('App\Item', 'details', 'column_fk', 'item_fk')
            ->withPivot('element_fk', 'value_int', 'value_float', 'value_string')->withTimestamps();
    }
    
    /**
     * Get the details of the column.
     */
    public function details()
    {
        return $this->hasMany('App\Detail', 'column_fk', 'column_id');
    }
    
    /**
     * Get the mapping of the column.
     */
    public function column_mapping()
    {
        return $this->hasMany('App\ColumnMapping', 'column_fk', 'column_id');
    }
    
    /**
     * Get the data type of the column.
     */
    public function getDataType()
    {
        return $this->data_type->attributes()->firstWhere('name', 'code')->pivot->value;
    }
    
    /**
     * Get the name of the validation rule for the column.
     */
    public function getValidationRule()
    {
        switch ($this->getDataType()) {
            case '_boolean_':
                return ['boolean'];
            case '_date_range_':
                return ['array', ['*' => 'date']];
            case '_multi_list_':
                return ['array', ['*' => 'integer']];
            case '_list_':
            case '_integer_':
                return ['integer'];
            case '_float_':
                return ['numeric'];
            case '_string_':
            case '_title_':
            case '_image_title_':
            case '_image_copyright_':
            case '_redirect_':
            case '_map_':
            case '_html_':
                return ['string'];
            case '_date_':
                return ['date'];
            case '_url_':
                return ['url'];
            case '_image_':
                return ['', ['file' => 'image|mimes:jpeg|max:8192', 'dummy' => 'integer']];
            default:
                return [''];
        }
    }
}
