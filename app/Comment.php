<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'comment_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_fk',
        'message',
        'public',
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
    
    /**
     * Scope a query to only include items of a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $owner
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMyOwn($query, $owner)
    {
        return $query->where('created_by', $owner);
    }
}
