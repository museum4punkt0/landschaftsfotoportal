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
     * Scope a query to only include columns with a given data type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDataType($query, $type)
    {
        return $query->whereHas('data_type', function ($query) use ($type) {
            $query->whereHas('values', function ($query) use ($type) {
                $query->where('value', $type);
            });
        });
    }
    
    /**
     * Scope a query to only include columns with a given data subtype.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSubType($query, $type)
    {
        return $query->whereHas('column_mapping', function ($query) use ($type) {
            $query->where('config', 'ILIKE', "%{$type}%");
        });
    }
    
    /**
     * Scope a query to only include columns with a given item type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfItemType($query, $type)
    {
        return $query->whereHas('column_mapping', function ($query) use ($type) {
            $query->whereHas('item_type', function ($query) use ($type) {
                $query->whereHas('values', function ($query) use ($type) {
                    $query->where('value', $type);
                });
            });
        });
    }
    
    
    /**
     * Get the data type of the column.
     *
     * @return String
     */
    public function getDataType()
    {
        return $this->data_type_name;
    }

    /**
     * Get the data type' name of the column.
     *
     * Warning: This must be used for auto filling the column model only!
     *
     * @return String
     */
    public function getDataTypeName()
    {
        return $this->data_type->attributes()->firstWhere('name', 'code')->pivot->value;
    }
    
    /**
     * Get the data subtype of the column.
     */
    public function getDataSubType()
    {
        return $this->column_mapping()->first()->getConfigValue('data_subtype');
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
                return ['array|min:2', ['*' => 'integer']];
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
                return ['', [
                    'file' => 'image|mimes:jpeg|max:' . config('media.image_max_size', 2048),
                    'filename' => 'string',
                ]];
            default:
                return [''];
        }
    }
}
