<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selectlist extends Model
{
    /**
     * The table associated with the model.
     *
     * (The default would be 'selectlists')
     *
     * @var string
     */
    protected $table = 'lists';
    
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'list_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'hierarchical',
        'internal',
        'attribute_order',
    ];
    
    /**
     * Get the elements of the list.
     */
    public function elements()
    {
        return $this->hasMany('App\Element', 'list_fk', 'list_id');
    }
}
