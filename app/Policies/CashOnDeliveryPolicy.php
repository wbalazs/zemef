<?php

namespace App\Policies;

use App\User;
use App\Models\Package\CashOnDelivery;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashOnDeliveryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', CashOnDelivery::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, CashOnDelivery $product)
    {
        return $user->hasPermission('edit', CashOnDelivery::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, CashOnDelivery $product)
    {
        return $user->hasPermission('delete', CashOnDelivery::class);
    }
}
