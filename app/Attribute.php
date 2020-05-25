<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'attribute_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
    
    /**
     * The elements that belong to the attribute.
     */
    public function elements()
    {
        return $this->belongsToMany('App\Element', 'values', 'attribute_fk', 'element_fk')
            ->withPivot('value')->withTimestamps();
    }
    
    /**
     * Get the values of the attribute.
     */
    public function values()
    {
        return $this->hasMany('App\Value', 'element_fk', 'element_id');
    }
}
