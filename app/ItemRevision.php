<?php

namespace App;

use App\DetailRevision;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRevision extends Item
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * (The default would be 'id')
     *
     * @var string
     */
    protected $primaryKey = 'item_revision_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'revision',
        'item_fk',
        'parent_fk',
        'item_type_fk',
        'taxon_fk',
        'title',
        'public',
        'created_by',
        'updated_by',
    ];
    
    /**
     * Accessor to fake the item's Id.
     *
     * @return void
     */
    public function getItemIdAttribute()
    {
        return $this->item_revision_id;
    }

    /**
     * Accessor to get the item's Id.
     *
     * @return void
     */
    public function getOriginalItemIdAttribute()
    {
        return $this->item_fk;
    }

    /**
     * Get the (original) item that owns the item (revision).
     */
    public function item()
    {
        return $this->belongsTo('App\Item', 'item_fk', 'item_id');
    }

    /**
     * Get the details of the item.
     */
    public function details()
    {
        return $this->hasMany('App\DetailRevision', 'item_revision_fk', 'item_revision_id');
    }


    /**
     * Scope a query to only include draft revisions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('revision', '<', 0);
    }

    /**
     * Scope a query to only include revisions of a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $owner
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner($query, $owner)
    {
        return $query->where('updated_by', $owner);
    }


    /**
     * Delete a revision and all its details.
     *
     * @return \App\ItemRevision
     */
    public function deleteRevisionWithDetails()
    {
        // Delete all details belonging to this revision
        $this->details()->delete();
        // Delete the revisions itself
        $this->delete();
    }

    /**
     * Create a new revision of this item including all details.
     *
     * @param  bool  $draft
     * @return \App\ItemRevision
     */
    public function cloneRevisionWithDetails($draft = false)
    {
        $revision = $this->cloneRevision($draft);

        foreach ($this->details as $detail) {
            $detail->cloneRevision($revision);
        }

        return $revision;
    }

    /**
     * Create a new revision of this item without any details.
     *
     * @param  bool  $draft
     * @return \App\ItemRevision
     */
    public function cloneRevision($draft = false)
    {
        $revision = $this->replicate();
        $revision->revision = $this->getLatestRevisionNumber($draft) + ($draft ? -1 : 1);
        $revision->updated_by = auth()->user()->id;
        $revision->save();

        return $revision;
    }

    /**
     * Get the latest revision number of this item.
     *
     * @param  bool  $draft
     * @return integer
     */
    public function getLatestRevisionNumber($draft = false)
    {
        $latest = $this->item->revisions()->where('revision', ($draft ? '<' : '>'), 0)
            ->orderBy('revision', $draft ? 'asc' : 'desc')
            ->first();
        if ($latest) {
            return $latest->revision;
        }
        else {
            return 0;
        }
    }

    /*public function updateItemFromRevision()
    {
        $item = Item::find($this->item_fk);
        $item_data = [
            'parent_fk' => $this->parent_fk,
            'item_type_fk' => $this->item_type_fk,
            'taxon_fk' => $this->taxon_fk,
            'title' => $this->title,
            'public' => $this->public,
            #'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
        $item->update($item_data);

        return $item;
    }*/

    /*private function copyDetailsFromRevision()
    {
        foreach ($this->details as $detail) {
            $detail = Detail::find($detail->detail_fk);
            $detail_data = [
                'item_fk' => $this->item_fk,
                'column_fk' => $detail->column_fk,
                'element_fk' => $detail->element_fk,
                'value_int' => $detail->value_int,
                'value_float' => $detail->value_float,
                'value_date' => $detail->value_date,
                'value_daterange' => $detail->value_daterange,
                'value_string' => $detail->value_string,
            ];
            $detail->update($detail_data);

            return $detail;
        }
    }*/
}
