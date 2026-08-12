<?php

namespace App\Policies;

use App\User;
use App\Models\StockEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockEntryPolicy
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
        return $user->hasPermission('index', StockEntry::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        if ($user->employee->active_business_id === null) {
            return false;
        }

        return $user->hasPermission('create', StockEntry::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, StockEntry $stockentry)
    {
        if ($user->employee->active_business_id === null) {
            return false;
        }

        return $user->hasPermission('edit', StockEntry::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, StockEntry $stockentry)
    {
        return $user->hasPermission('delete', StockEntry::class);
    }
}
