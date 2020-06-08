<?php

namespace App;

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
        if($this->item_type->attributes()->firstWhere('name', 'config')) {
            $json = $this->item_type->attributes()->firstWhere('name', 'config')->pivot->value;
            $config = json_decode($json, true);
            
            return $config['title_column'];
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
     */
    public function getTitleColumn()
    {
        $title = __('items.no_title_column');
        
        if($this->getTitleColumnId()) {
            $title_column = $this->columns->firstWhere('column_id', $this->getTitleColumnId());
            if($title_column) {
                $title = $title_column->pivot->value_string;
            }
        }
        
        return $title;
    }
}
