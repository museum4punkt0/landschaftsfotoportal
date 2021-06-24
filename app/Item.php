<?php

namespace App;

use App\ColumnMapping;
use App\Detail;
use App\Location;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'item_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_fk',
        'item_type_fk',
        'taxon_fk',
        'title',
        'public',
        'created_by',
        'updated_by',
    ];
    
    /**
     * Get the element that owns the item.
     */
    public function item_type()
    {
        return $this->belongsTo('App\Element', 'item_type_fk', 'element_id');
    }
    
    /**
     * Get the taxon that owns the item.
     */
    public function taxon()
    {
        return $this->belongsTo('App\Taxon', 'taxon_fk', 'taxon_id');
    }
    
    /**
     * Get the user who created the item.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    
    /**
     * Get the user who updated the item.
     */
    public function editor()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
    
    /**
     * The columns that belong to the item.
     */
    public function columns()
    {
        return $this->belongsToMany('App\Column', 'details', 'item_fk', 'column_fk')
            ->withPivot('element_fk', 'value_int', 'value_float', 'value_string')->withTimestamps();
    }
    
    /**
     * Get the details of the item.
     */
    public function details()
    {
        return $this->hasMany('App\Detail', 'item_fk', 'item_id');
    }
    
    /**
     * Get the comments of the item.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment', 'item_fk', 'item_id');
    }
    
    /**
     * Get the carts of the item.
     */
    public function carts()
    {
        return $this->hasMany('App\Cart', 'item_fk', 'item_id');
    }
    
    /**
     * Scope a query to only include items of a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $owner
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMyOwn($query, $owner)
    {
        return $query->where('created_by', $owner);
    }
    
    
    use \Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
    
    /**
     * The parent key associated with the table.
     *
     * (The default would be 'parent_id')
     *
     * @var string
     */
    public function getParentKeyName()
    {
        return 'parent_fk';
    }
    
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    public function getLocalKeyName()
    {
        return $this->primaryKey;
    }
    
    
    /**
     * Get the id of the column containing a title or name string representing this item.
     */
    public function getTitleColumnId()
    {
        if ($this->item_type->attributes()->firstWhere('name', 'config')) {
            $json = $this->item_type->attributes()->firstWhere('name', 'config')->pivot->value;
            $config = json_decode($json, true);
            
            return isset($config['title_column']) ? $config['title_column'] : null;
        }
        // No config available for this item_type
        else {
            return null;
        }
    }
    
    /**
     * Get the title or name string representing this item.
     *
     * The column storing that string is set in the JSON config that belongs to the item_type.
     *
     * @param  bool  $fromTaxon
     * @return string
     */
    public function getTitleColumn($fromTaxon = false)
    {
        $title = __('items.no_title_column');
        
        // TODO: getTitleColumnId() is much too slow, we should create a column 'item'.'title'
        if (!$fromTaxon && $this->getTitleColumnId()) {
            $title_column = $this->columns->firstWhere('column_id', $this->getTitleColumnId());
            if ($title_column) {
                $title = $title_column->pivot->value_string;
            }
        }
        // Try to fetch a taxon name instead if a taxon is linked with this item
        else {
            if ($this->taxon_fk) {
                $title = $this->taxon->full_name;
            }
        }
        
        return $title;
    }
    
    /**
     * Get the element ID of a given data type.
     *
     * @param  string  $name
     * @return int
     */
    public function getDataTypeId($name)
    {
        // Check all columns of this item for given data type
        foreach ($this->columns as $col) {
            if ($col->getDataType() == $name) {
                return $col->data_type_fk;
            }
        }
        
        return null;
    }
    
    /**
     * Get an item's detail with given data type.
     *
     * @param  string  $name
     * @return string
     */
    public function getDetailWhereDataType($name)
    {
        $detail = __('items.no_detail_with_data_type');
        
        $data_type_id = $this->getDataTypeId($name);
        if ($data_type_id) {
            // Get first column with given data type
            $column = $this->columns->firstWhere('data_type_fk', $data_type_id);
            if ($column) {
                // Details can be of different data types
                switch ($name) {
                    case '_float_':
                        $detail = $column->pivot->value_float;
                        break;
                    case '_integer_':
                    case '_image_ppi_':
                        $detail = $column->pivot->value_int;
                        break;
                    default:
                        $detail = $column->pivot->value_string;
                }
            }
        }
        return $detail;
    }
    
    /**
     * Update map latitude and longitude from given location.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function updateLatLon($location)
    {
        if ($location) {
            // Get the config containing column IDs for latitude and longitute
            $cm = ColumnMapping::where('config', 'ILIKE', '%map_lat_col%')->first();
            $config = $cm->config_array;
            // Check for existing config keys
            if (isset($config['map_lat_col']) && isset($config['map_lon_col'])) {
                // Update latitude and longitude
                Detail::updateOrCreate(
                    ['item_fk' => $this->item_id, 'column_fk' => $config['map_lat_col']],
                    ['value_float' => $location->lat]
                );
                Detail::updateOrCreate(
                    ['item_fk' => $this->item_id, 'column_fk' => $config['map_lon_col']],
                    ['value_float' => $location->lon]
                );
            }
        }
    }
}
