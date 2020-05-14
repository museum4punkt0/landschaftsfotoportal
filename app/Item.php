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
        #return $this->belongsTo('App\Taxon', 'taxon_fk', 'taxon_id');
    }
    
    /**
     * Get the details of the item.
     */
    public function details()
    {
        return $this->hasMany('App\Detail', 'item_fk', 'item_id');
    }
}
