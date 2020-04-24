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
     * Get the values of the element.
     */
    public function values()
    {
        return $this->hasMany('App\Value', 'element_fk', 'element_id');
    }    
}
