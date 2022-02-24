<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Casts\DateRangeCast;

#use Belamov\PostgresRange\Casts\DateRangeCast;

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
        'value_daterange',
        'value_string',
    ];
    
    protected $casts = [
        'value_daterange' => DateRangeCast::class,
    ];
    
    /**
     * Get the item that owns the detail.
     */
    public function item()
    {
        return $this->belongsTo('App\Item', 'item_fk', 'item_id');
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
    
    /**
     * The elements that belong to the detail.
     */
    public function elements()
    {
        return $this->belongsToMany('App\Element', 'element_mapping', 'detail_fk', 'element_fk')
            ->withTimestamps();
    }

    /**
     * Get the revisions of the item.
     */
    public final function revisions()
    {
        return $this->hasMany('App\DetailRevision', 'detail_fk', 'detail_id');
    }

    /**
     * Create a new revision of this detail.
     *
     * @param  \App\ItemRevision  $item_revision
     * @return \App\DetailRevision
     */
    public final function createRevision(ItemRevision $item_revision)
    {
        $revision = new DetailRevision([
            'item_revision_fk' => $item_revision->item_revision_id,
            'item_fk' => $this->item_fk,
            'column_fk' => $this->column_fk,
            'element_fk' => $this->element_fk,
            'value_int' => $this->value_int,
            'value_float' => $this->value_float,
            'value_date' => $this->value_date,
            'value_daterange' => $this->value_daterange,
            'value_string' => $this->value_string,
        ]);
        $this->revisions()->save($revision);
        
        // Save multiple elements of lists if available
        if ($this->elements()->count()) {
            $elements = $this->elements()->get()->pluck('element_id')->all();
            $revision->elements()->attach($elements);
        }

        return $revision;
    }
}
