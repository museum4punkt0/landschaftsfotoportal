<?php

namespace App\Policies;

use App\Item;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize all actions for user group super-admin.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function before($user, $ability)
    {
        if ($user->inGroup('super-admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasAccess(['viewAny-item']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function view(User $user = null, Item $item)
    {
        // WARNING: Guest users are allowed to view by default!
        if (is_null($user)) {
            return true;
        }
        // Following rule does only apply on authentificated user!
        else {
            return $user->hasAccess(['view-item']);
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-item']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function update(User $user, Item $item)
    {
        if ($user->id === $item->creator->id) {
            return true;
        }
        else {
            return $user->hasAccess(['update-item']);
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function delete(User $user, Item $item)
    {
        if ($user->id === $item->creator->id) {
            return true;
        }
        else {
            return $user->hasAccess(['delete-item']);
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function restore(User $user, Item $item)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function forceDelete(User $user, Item $item)
    {
        //
    }

    /**
     * Determine whether the user can delete drafts of the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function deleteDraft(User $user, Item $item)
    {
        // Caution!
        // revision->creator is the original creator of the item
        // revision->editor is the creator of this revision (means: editor of the item)
        if ($user->id === $item->editor->id) {
            return true;
        }
        else {
            return $user->hasAccess(['deleteDraft-item']);
        }
    }

    /**
     * Determine whether the user can update titles of the model.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function titles(User $user)
    {
        return $user->hasAccess(['titles-item']);
    }

    /**
     * Determine whether the user can publish the model.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function publish(User $user, Item $item = null)
    {
        return $user->hasAccess(['publish-item']);
    }

    /**
     * Determine whether the user can show own models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewOwn(User $user)
    {
        return $user->hasAccess(['viewOwn-item']);
    }

    /**
     * Determine whether the user can download models.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return mixed
     */
    public function download(User $user, Item $item)
    {
        return $user->hasAccess(['download-item']);
    }

}
