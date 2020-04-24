<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'value_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'element_fk',
        'attribute_fk',
        'value',
    ];
    
    /**
     * Get the element that owns the value.
     */
    public function element()
    {
        return $this->belongsTo('App\Element', 'element_fk', 'element_id');
    }
    
    /**
     * Get the attribute that owns the value.
     */
    public function attribute()
    {
        return $this->belongsTo('App\Attribute', 'attribute_fk', 'attribute_id');
    }
}
