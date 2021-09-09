<?php

namespace App\Policies;

use App\User;
use App\Value;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValuePolicy
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
        return $user->hasAccess(['viewAny-value']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Value  $value
     * @return mixed
     */
    public function view(User $user, Value $value)
    {
        return $user->hasAccess(['view-value']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-value']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Value  $value
     * @return mixed
     */
    public function update(User $user, Value $value)
    {
        return $user->hasAccess(['update-value']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Value  $value
     * @return mixed
     */
    public function delete(User $user, Value $value)
    {
        return $user->hasAccess(['delete-value']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Value  $value
     * @return mixed
     */
    public function restore(User $user, Value $value)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Value  $value
     * @return mixed
     */
    public function forceDelete(User $user, Value $value)
    {
        //
    }
}
