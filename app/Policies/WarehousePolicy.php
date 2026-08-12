<?php

namespace App\Policies;

use App\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
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
        return $user->hasPermission('index', Warehouse::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Warehouse::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, Warehouse $warehouse)
    {
        return $user->hasPermission('edit', Warehouse::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, Warehouse $warehouse)
    {
        return $user->hasPermission('delete', Warehouse::class);
    }
}
