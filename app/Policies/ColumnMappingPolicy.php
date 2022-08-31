<?php

namespace App\Policies;

use App\ColumnMapping;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColumnMappingPolicy
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
        return $user->hasAccess(['viewAny-colmap']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\ColumnMapping  $columnMapping
     * @return mixed
     */
    public function view(User $user, ColumnMapping $columnMapping)
    {
        return $user->hasAccess(['view-colmap']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-colmap']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\ColumnMapping  $columnMapping
     * @return mixed
     */
    public function update(User $user, ColumnMapping $columnMapping)
    {
        return $user->hasAccess(['update-colmap']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ColumnMapping  $columnMapping
     * @return mixed
     */
    public function delete(User $user, ColumnMapping $columnMapping)
    {
        return $user->hasAccess(['delete-colmap']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\ColumnMapping  $columnMapping
     * @return mixed
     */
    public function restore(User $user, ColumnMapping $columnMapping)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ColumnMapping  $columnMapping
     * @return mixed
     */
    public function forceDelete(User $user, ColumnMapping $columnMapping)
    {
        //
    }

    /**
     * Determine whether the user can show own models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function map(User $user)
    {
        return $user->hasAccess(['map-colmap']);
    }

    /**
     * Determine whether the user can show own models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function sort(User $user)
    {
        return $user->hasAccess(['sort-colmap']);
    }

    /**
     * Determine whether the user can change the public visibility.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function publish(User $user)
    {
        return $user->hasAccess(['publish-colmap']);
    }
}
