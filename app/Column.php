<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'column_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'column_group_fk',
        'data_type_fk',
        'translation_fk',
        'list_fk',
        'description',
    ];
    
    /**
     * Get the element that owns the column.
     */
    public function column_group()
    {
        return $this->belongsTo('App\Element', 'column_group_fk', 'element_id');
    }
    
    /**
     * Get the element that owns the column.
     */
    public function data_type()
    {
        return $this->belongsTo('App\Element', 'data_type_fk', 'element_id');
    }
    
    /**
     * Get the element that owns the column.
     */
    public function translation()
    {
        return $this->belongsTo('App\Element', 'translation_fk', 'element_id');
    }
    
    /**
     * Get the list that owns the column.
     */
    public function list()
    {
        return $this->belongsTo('App\Selectlist', 'list_fk', 'list_id');
    }
    
    /**
     * Get the details of the element.
     */
    public function details()
    {
        #return $this->hasMany('App\Detail', 'column_fk', 'column_id');
    }
    
    /**
     * Get the configs of the element.
     */
    public function configs()
    {
        #return $this->hasMany('App\Config', 'column_fk', 'column_id');
    }
}
