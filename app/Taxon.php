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
        'bfn_namnr',
        'bfn_sipnr',
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
        return $this->hasMany('App\ColumnMapping', 'taxon_fk', 'taxon_id');
    }
    
    /**
     * Get all synonyms of the taxon.
     */
    public function synonyms()
    {
        return $this->hasMany('App\Taxon', 'valid_name');
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
    
    /**
     * Get the taxon's ancestor with the given rank name.
     *
     * @param  string  $rank  The abbreviated taxonomic rank, e.g. ORD, FAM, GAT, SPE
     * @return App\Taxon
     */
    public function getAncestorWhereRank($rank)
    {
        $a = $this->ancestors->firstWhere('rank_abbr', '=', $rank);
        if ($a) {
            return $a;
        } else {
            return null;
        }
    }
}
