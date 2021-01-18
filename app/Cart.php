<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * The table associated with the model.
     *
     * (The default would be 'carts')
     *
     * @var string
     */
    protected $table = 'cart';
    
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'cart_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_fk',
        'created_by',
        'updated_by',
    ];
    
    /**
     * Get the item that owns the comment.
     */
    public function item()
    {
        return $this->belongsTo('App\Item', 'item_fk', 'item_id');
    }
    
    /**
     * Get the user who created the comment.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    
    /**
     * Get the user who updated the comment.
     */
    public function editor()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
}
