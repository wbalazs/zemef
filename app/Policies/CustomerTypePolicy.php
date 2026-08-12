<?php

namespace App\Policies;

use App\User;
use App\Models\CustomerType;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerTypePolicy
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
        return $user->hasPermission('index', CustomerType::class);
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', CustomerType::class);
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param  \App\User  $user
     * @param  \App\Models\CustomerType  $customer_type
     * @return boolean
     */
    public function update(User $user, CustomerType $customer_type)
    {
        return $user->hasPermission('edit', CustomerType::class);
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param  \App\User  $user
     * @param  \App\Models\CustomerType  $customer_type
     * @return boolean
     */
    public function delete(User $user, CustomerType $customer_type)
    {
        return $user->hasPermission('delete', CustomerType::class);
    }
}
