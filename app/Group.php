<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'group_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
    ];
    
    /**
     * Get the user that owns the group.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'group_fk', 'user_id');
    }
}
