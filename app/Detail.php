<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'detail_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_fk',
        'column_fk',
        'element_fk',
        'value_int',
        'value_float',
        'value_date',
        'value_string',
    ];
    
    /**
     * Get the item that owns the detail.
     */
    public function item()
    {
        #return $this->belongsTo('App\Item', 'item_fk', 'item_id');
    }
    
    /**
     * Get the column that owns the detail.
     */
    public function column()
    {
        return $this->belongsTo('App\Column', 'column_fk', 'column_id');
    }
    
    /**
     * Get the element that owns the detail.
     */
    public function element()
    {
        return $this->belongsTo('App\Element', 'element_fk', 'element_id');
    }
}
