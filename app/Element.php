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
     * The details that belong to the element.
     */
    public function details()
    {
        return $this->belongsToMany('App\Detail', 'element_mapping', 'element_fk', 'detail_fk')
            ->withTimestamps();
    }
    
    /**
     * Get the children of the element.
     */
    public function childrenElements()
    {
        return $this->hasMany('App\Element', 'parent_fk', 'element_id')->with('childrenElements');
    }
    
    /**
     * Scope a query to only include elements for a given list.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $list_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfList($query, $list_id)
    {
        return $query->treeOf(function ($query) use ($list_id) {
                $query->where('parent_fk', null)->where('list_fk', $list_id);
            })
            ->depthFirst();
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
        if ($this->attributes()->firstWhere('name', 'config')) {
            $json = $this->attributes()->firstWhere('name', 'config')->pivot->value;
            $config = json_decode($json, true);
            
            return isset($config[$key]) ? $config[$key] : null;
        }
        // No config available for this item_type
        else {
            return null;
        }
    }
    
    /**
     * Get trees with all elements for given column mappings.
     *
     * @param  Illuminate\Database\Eloquent\Collection  $colmaps
     * @param  mixed  $list_id
     * @return array
     */
    public static function getTrees($colmaps)
    {
        $lists = null;
        foreach ($colmaps as $cm) {
            $list_id = $cm->column->list_fk;
            if ($list_id) {
                $lists[$list_id] = Element::ofList($list_id)->get();
            }
        }
        return $lists;
    }

    /**
     * Get value with a given attribute name.
     *
     * @param  string  $attribute
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getValueOfAttribute($attribute)
    {
        return $this->attributes()->firstWhere('name', $attribute)->pivot->value;
    }
}
