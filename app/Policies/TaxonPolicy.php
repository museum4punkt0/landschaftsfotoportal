<?php

namespace App\Policies;

use App\Taxon;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonPolicy
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
        return $user->hasAccess(['viewAny-taxon']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Taxon  $taxon
     * @return mixed
     */
    public function view(User $user, Taxon $taxon)
    {
        return $user->hasAccess(['view-taxon']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['create-taxon']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Taxon  $taxon
     * @return mixed
     */
    public function update(User $user, Taxon $taxon)
    {
        return $user->hasAccess(['update-taxon']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Taxon  $taxon
     * @return mixed
     */
    public function delete(User $user, Taxon $taxon)
    {
        return $user->hasAccess(['delete-taxon']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Taxon  $taxon
     * @return mixed
     */
    public function restore(User $user, Taxon $taxon)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Taxon  $taxon
     * @return mixed
     */
    public function forceDelete(User $user, Taxon $taxon)
    {
        //
    }
}
