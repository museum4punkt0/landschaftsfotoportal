<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'element_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_fk',
        'list_fk',
        'value_summary',
    ];
    
    /**
     * Get the list that owns the element.
     */
    public function list()
    {
        return $this->belongsTo('App\Selectlist', 'list_fk', 'list_id');
    }
    
    /**
     * The attributes that belong to the element.
     */
    public function attributes()
    {
        return $this->belongsToMany('App\Attribute', 'values', 'element_fk', 'attribute_fk')
            ->withPivot('value')->withTimestamps();
    }
    
    /**
     * Get the values of the element.
     */
    public function values()
    {
        return $this->hasMany('App\Value', 'element_fk', 'element_id');
    }
    
    /**
     * Get the children of the element.
     */
    public function childrenElements()
    {
        return $this->hasMany('App\Element', 'parent_fk', 'element_id')->with('childrenElements');
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
     * Get the configuration value for a given key from the JSON key/value store.
     */
    public function getConfigValue($key)
    {
        if($this->attributes()->firstWhere('name', 'config')) {
            $json = $this->attributes()->firstWhere('name', 'config')->pivot->value;
            $config = json_decode($json, true);
            
            return isset($config[$key]) ? $config[$key] : null;
        }
        // No config available for this item_type
        else {
            return null;
        }
    }
}
