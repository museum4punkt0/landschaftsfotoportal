<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnMapping extends Model
{
    /**
     * The table associated with the model.
     *
     * (The default would be 'column_mappings')
     *
     * @var string
     */
    protected $table = 'column_mapping';
    
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'colmap_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'column_fk',
        'item_type_fk',
        'column_group_fk',
        'taxon_fk',
        'column_order',
        'api_attribute',
        'config',
        'public',
    ];
    
    /**
     * Accessor to get the JSON config as array.
     *
     * @param  string  $value
     * @return string
     */
    public function getConfigArrayAttribute($value)
    {
        return json_decode($this->config, true);
    }
    
    /**
     * Get the column that owns the column mapping.
     */
    public function column()
    {
        return $this->belongsTo('App\Column', 'column_fk', 'column_id');
    }
    
    /**
     * Get the element that owns the column mapping.
     */
    public function item_type()
    {
        return $this->belongsTo('App\Element', 'item_type_fk', 'element_id');
    }
    
    /**
     * Get the element that owns the column.
     */
    public function column_group()
    {
        return $this->belongsTo('App\Element', 'column_group_fk', 'element_id');
    }
    
    /**
     * Get the taxon that owns the column mapping.
     */
    public function taxon()
    {
        return $this->belongsTo('App\Taxon', 'taxon_fk', 'taxon_id');
    }
    
    /**
     * Scope a query to only include column mappings for a given item and taxon.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $item_type_id
     * @param  mixed  $taxon_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForItem($query, $item_type_id, $taxon_id)
    {
        return $query->where('item_type_fk', $item_type_id)
            ->where(function ($query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function ($query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->with('column')
            ->orderBy('column_order');
    }

    /**
     * Scope a query to only include column mappings for a given item type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $item_type_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForItemType($query, $item_type_id)
    {
        return $query->where('item_type_fk', $item_type_id);
    }

    /**
     * Set the column order to the highest value (sort to the end) regarding a certain item type.
     *
     * @return int
     */
    public function setHighestColumnOrder()
    {
        $column_order = ColumnMapping::forItemType($this->item_type_fk)
            ->orderBy('column_order', 'desc')
            ->first()
            ->column_order;
        $this->column_order = $column_order + 1;
        $this->save();

        return $column_order + 1;
    }
    
    
    /**
     * Get the bound/limit for a date from config.
     *
     * @param  string  $boundary
     * @return mixed
     */
    public function getDateBoundConfig($boundary)
    {
        switch ($this->getConfigValue('date_' . $boundary)) {
            case 'date':
                return $this->getConfigValue('date_' . $boundary . '_date') ?? '';
            case 'current':
                return date('Y-m-d');
            // Might be 'false' or not set at all
            default:
                return '';
        }
    }

    /**
     * Get the configuration value for a given key from the JSON key/value store.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getConfigValue($key)
    {
        if ($this->config) {
            $config = json_decode($this->config, true);
            
            return isset($config[$key]) ? $config[$key] : null;
        }
        // No config available for this item_type
        else {
            return null;
        }
    }

    /**
     * Get the name of the validation rule for the column.
     *
     * @return array
     */
    public function getValidationRule()
    {
        switch ($this->column->getDataType()) {
            case '_boolean_':
                return ['boolean'];
            case '_date_range_':
                $min_bound = $this->getDateBoundConfig('min');
                $max_bound = $this->getDateBoundConfig('max');
                return ['array', [
                    '*' => 'date',
                    'start' =>
                        ($this->getConfigValue('lower_bound_required') ? 'required' : 'nullable') .
                        ($min_bound ? '|after_or_equal:' . $min_bound : ''),
                    'end' =>
                        ($this->getConfigValue('upper_bound_required') ? 'required' : 'nullable') .
                        '|after_or_equal:fields.'.$this->column_fk . '.start' .
                        ($max_bound ? '|before_or_equal:' . $max_bound : ''),
                ]];
            case '_multi_list_':
                return ['array|min:2', ['*' => 'integer']];
            case '_list_':
            case '_integer_':
                return ['integer'];
            case '_float_':
                return ['numeric'];
            case '_string_':
            case '_title_':
            case '_redirect_':
            case '_map_':
            case '_html_':
                return ['string'];
            case '_date_':
                $min_bound = $this->getDateBoundConfig('min');
                $max_bound = $this->getDateBoundConfig('max');
                return ['date' .
                    ($max_bound ? '|before_or_equal:' . $max_bound : '') .
                    ($min_bound ? '|after_or_equal:' . $min_bound : '')
                ];
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
    
    /**
     * Get the part of the validation rule which defines if this column is required or not.
     *
     * @return string
     */
    public function getRequiredRule()
    {
        // For data type date range the requirement rule is handled by getValidationRule()
        if ($this->column->getDataType() == '_date_range_') {
            return '';
        }
        if ($this->getConfigValue('required')) {
            return 'required|';
        } else {
            return 'nullable|';
        }
    }
}
