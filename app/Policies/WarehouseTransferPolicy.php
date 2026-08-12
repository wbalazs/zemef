<?php

namespace App\Policies;

use App\User;
use App\Models\WarehouseTransfer;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehouseTransferPolicy
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
        return $user->hasPermission('create', WarehouseTransfer::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, Tool $tool)
    {
        return $user->hasPermission('edit', WarehouseTransfer::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, Tool $tool)
    {
        return $user->hasPermission('delete', WarehouseTransfer::class);
    }
}
