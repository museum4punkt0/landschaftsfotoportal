<?php

namespace App\Policies;

use App\Selectlist;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListPolicy
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
        return $user->hasAccess(['viewAny-list']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function view(User $user, Selectlist $selectlist)
    {
        return $user->hasAccess(['view-list']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-list']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function update(User $user, Selectlist $selectlist)
    {
        return $user->hasAccess(['update-list']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function delete(User $user, Selectlist $selectlist)
    {
        return $user->hasAccess(['delete-list']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function restore(User $user, Selectlist $selectlist)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function forceDelete(User $user, Selectlist $selectlist)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function internal(User $user)
    {
        return $user->hasAccess(['internal-list']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function tree(User $user, Selectlist $selectlist)
    {
        return $user->hasAccess(['tree-list']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Selectlist  $selectlist
     * @return mixed
     */
    public function export(User $user, Selectlist $selectlist)
    {
        return $user->hasAccess(['export-list']);
    }

}
