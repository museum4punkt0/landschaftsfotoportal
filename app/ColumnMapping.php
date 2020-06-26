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
        'config',
    ];
    
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
     * Get the configuration value for a given key from the JSON key/value store.
     */
    public function getConfigValue($key)
    {
        if($this->config) {
            $config = json_decode($this->config, true);
            
            return isset($config[$key]) ? $config[$key] : null;
        }
        // No config available for this item_type
        else {
            return null;
        }
    }
}
