<?php

namespace App\Policies;

use App\User;
use App\Models\CustomerCard\CustomerCard;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerCardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the categories.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('index', CustomerCard::class);
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', CustomerCard::class);
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return boolean
     */
    public function update(User $user, CustomerCard $customercard)
    {
        return $user->hasPermission('edit', CustomerCard::class);
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\Category  $category
     * @return boolean
     */
    public function delete(User $user, CustomerCard $customercard)
    {
        return $user->hasPermission('delete', CustomerCard::class);
    }
}
