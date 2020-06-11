<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxon extends Model
{
    /**
     * The table associated with the model.
     * 
     * (The default would be 'selectlists')
     *
     * @var string
     */
    protected $table = 'taxa';
    
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'taxon_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_fk',
        'taxon_name',
        'taxon_author',
        'taxon_suppl',
        'full_name',
        'native_name',
        'valid_name',
        'rank',
        'rank_abbr',
        'gsl_id',
    ];
    
    /**
     * Get the items of the taxon.
     */
    public function items()
    {
        return $this->hasMany('App\Item', 'taxon_fk', 'taxon_id');
    }
    
    /**
     * Get the column mapping of the taxon.
     */
    public function column_mapping()
    {
        return $this->hasMany('App\ColumnMapping', 'column_fk', 'column_id');
    }
    
    
    use \Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
    
    /**
     * The parent key associated with the table.
     * 
     * (The default would be 'parent_id')
     *
     * @var string
     */
    public function getParentKeyName()
    {
        return 'parent_fk';
    }
    
    /**
     * The primary key associated with the table.
     * 
     * (The default would be 'id')
     *
     * @var string
     */
    public function getLocalKeyName()
    {
        return $this->primaryKey;
    }
}
