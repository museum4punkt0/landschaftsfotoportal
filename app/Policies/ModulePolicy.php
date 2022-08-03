<?php

namespace App\Policies;

use App\ModuleInstance;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
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
        return $user->hasAccess(['viewAny-module']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\ModuleInstance  $moduleInstance
     * @return mixed
     */
    public function view(User $user, ModuleInstance $moduleInstance)
    {
        return $user->hasAccess(['view-module']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-module']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\ModuleInstance  $moduleInstance
     * @return mixed
     */
    public function update(User $user, ModuleInstance $moduleInstance)
    {
        return $user->hasAccess(['update-module']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ModuleInstance  $moduleInstance
     * @return mixed
     */
    public function delete(User $user, ModuleInstance $moduleInstance)
    {
        return $user->hasAccess(['delete-module']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\ModuleInstance  $moduleInstance
     * @return mixed
     */
    public function restore(User $user, ModuleInstance $moduleInstance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ModuleInstance  $moduleInstance
     * @return mixed
     */
    public function forceDelete(User $user, ModuleInstance $moduleInstance)
    {
        //
    }
}
