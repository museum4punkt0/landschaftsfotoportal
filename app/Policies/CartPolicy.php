<?php

namespace App\Policies;

use App\Cart;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
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
     * Determine whether the user can show own models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewOwn(User $user)
    {
        return $user->hasAccess(['viewOwn-cart']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function add(User $user)
    {
        return $user->hasAccess(['add-cart']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Cart  $cart
     * @return mixed
     */
    public function remove(User $user, Cart $cart)
    {
        if ($user->id === $cart->creator->id) {
            return true;
        }
        else {
            return $user->hasAccess(['remove-cart']);
        }
    }
}
