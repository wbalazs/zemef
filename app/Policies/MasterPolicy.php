<?php

namespace App\Policies;

use App\User;
use App\Models\Bank;
use App\Models\Address\City;
use Illuminate\Auth\Access\HandlesAuthorization;

class MasterPolicy
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
        return $user->hasPermission('index', Bank::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', Bank::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function updateMaster(User $user, Bank $bank)
    {
        return $user->hasPermission('edit', Bank::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function deleteBank(User $user, Bank $bank)
    {
        return $user->hasPermission('delete', Bank::class);
    }

    public function createSettlement(User $user, City $city)
    {
        return $user->hasPermission('create', City::class);
    }
}
