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
     * Get the part of the validation rule which defines if this column is required or not.
     *
     * @return string
     */
    public function getRequiredRule()
    {
        if ($this->getConfigValue('required')) {
            return 'required|';
        } else {
            return 'nullable|';
        }
    }
}
