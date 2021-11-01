<?php

namespace App;

use App\Casts\DateRangeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRevision extends Detail
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'detail_revision_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'detail_fk',
        'item_revision_fk',
        'item_fk',  // used for mutuator only
        'column_fk',
        'element_fk',
        'value_int',
        'value_float',
        'value_date',
        'value_daterange',
        'value_string',
    ];
    
    protected $casts = [
        'value_daterange' => DateRangeCast::class,
    ];

    /**
     * Accessor to fake the detail's Id.
     *
     * @return void
     */
    public function getDetailIdAttribute()
    {
        return $this->detail_revision_id;
    }

    /**
     * Accessor to fake the item's foreign key.
     *
     * @return void
     */
    /*public function getItemFkAttribute()
    {
        #return $this->item_revision_fk;
    }*/
    
    /**
     * Mutator to fake the related item's foreign key.
     *
     * @param  string  $value
     * @return int
     */
    /*public function setItemFkAttribute($value)
    {
        $this->attributes['item_revision_fk'] = intval($value);
    }*/

    /**
     * Get the item that owns the detail.
     */
    public function item()
    {
        return $this->belongsTo('App\ItemRevision', 'item_revision_fk', 'item_revsion_id');
    }

    /**
     * Get the (original) detail that owns the detail.
     */
    public function detail()
    {
        return $this->belongsTo('App\Detail', 'detail_fk', 'detail_id');
    }

    /**
     * The elements that belong to the detail.
     */
    public function elements()
    {
        return $this->belongsToMany('App\Element', 'element_mapping_revisions', 'detail_revision_fk', 'element_fk')
            ->withTimestamps();
    }

    /**
     * Create a new revision of this detail.
     *
     * @param  \App\ItemRevision  $item_revision
     * @return \App\DetailRevision
     */
    public function cloneRevision(ItemRevision $item_revision)
    {
        $revision = $this->replicate();
        $revision->item_revision_fk = $item_revision->item_revision_id;
        $revision->save();
        
        // Save multiple elements of lists if available
        if ($this->elements()->count()) {
            $elements = $this->elements()->get()->pluck('element_id')->all();
            $revision->elements()->attach($elements);
        }

        return $revision;
    }
}
