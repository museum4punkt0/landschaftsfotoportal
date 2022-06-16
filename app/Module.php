<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'module_id';

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
     * Get the instances of the module.
     */
    public function instances()
    {
        return $this->hasMany('App\ModuleInstance', 'module_fk', 'module_id');
    }

    /**
     * Get the instances of the module.
     */
    public function getByName($name)
    {
        #return $this->where('name', $name);
    }
}
