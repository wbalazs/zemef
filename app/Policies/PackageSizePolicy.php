<?php

namespace App\Policies;

use App\User;
use App\Models\Package\PackageSize;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageSizePolicy
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
        return $user->hasPermission('index', PackageSize::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', PackageSize::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, PackageSize $product)
    {
        return $user->hasPermission('edit', PackageSize::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, PackageSize $product)
    {
        return $user->hasPermission('delete', PackageSize::class);
    }
}
