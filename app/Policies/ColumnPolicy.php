<?php

namespace App\Policies;

use App\Column;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColumnPolicy
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
        return $user->hasAccess(['viewAny-column']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Column  $column
     * @return mixed
     */
    public function view(User $user, Column $column)
    {
        return $user->hasAccess(['view-column']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-column']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Column  $column
     * @return mixed
     */
    public function update(User $user, Column $column)
    {
        return $user->hasAccess(['update-column']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Column  $column
     * @return mixed
     */
    public function delete(User $user, Column $column)
    {
        return $user->hasAccess(['delete-column']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Column  $column
     * @return mixed
     */
    public function restore(User $user, Column $column)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Column  $column
     * @return mixed
     */
    public function forceDelete(User $user, Column $column)
    {
        //
    }
}
