<?php

namespace App\Policies;

use App\ItemRevision;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemRevisionPolicy
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
        return $user->hasAccess(['viewAny-revision']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function view(User $user = null, ItemRevision $revision)
    {
        if ($user->id === $revision->creator->id) {
            return true;
        }
        else {
            return $user->hasAccess(['view-revision']);
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
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function update(User $user, ItemRevision $revision)
    {
        return $user->hasAccess(['update-revision']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function delete(User $user, ItemRevision $revision)
    {
        // Caution!
        // revision->creator is the original creator of the item
        // revision->editor is the creator of this revision (means: editor of the item)
        if ($user->id === $revision->editor->id) {
            return true;
        }
        else {
            return $user->hasAccess(['delete-revision']);
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function restore(User $user, ItemRevision $revision)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function forceDelete(User $user, ItemRevision $revision)
    {
        //
    }

    /**
     * Determine whether the user can delete drafts of the model.
     *
     * @param  \App\User  $user
     * @param  \App\ItemRevision  $revision
     * @return mixed
     */
    public function deleteDraft(User $user, ItemRevision $revision)
    {
        // Caution!
        // revision->creator is the original creator of the item
        // revision->editor is the creator of this revision (means: editor of the item)
        if ($user->id === $revision->editor->id) {
            return true;
        }
        else {
            return $user->hasAccess(['deleteDraft-revision']);
        }
    }
}
