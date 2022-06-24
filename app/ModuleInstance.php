<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleInstance extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'module_instance_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];


    /**
     * Get the module that owns the instance.
     */
    public function instances()
    {
        return $this->belongsTo('App\Module', 'module_fk', 'module_id');
    }

    /**
     * Scope a query to only include modules for a given item.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $item_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForItem($query, $item_id)
    {
        return $query->whereNull('item_fk')
            ->orWhere('item_fk', $item_id);
    }
}
