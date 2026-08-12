<?php

namespace App\Policies;

use App\User;
use App\Models\WebshopOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebshopOrderOrderPolicy
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
        return $user->hasPermission('index', WebshopOrder::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', WebshopOrder::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\WebshopOrder  $WebshopOrder
     * @return boolean
     */
    public function update(User $user, WebshopOrder $WebshopOrder)
    {
        return $user->hasPermission('edit', WebshopOrder::class);
    }
    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\WebshopOrder  $WebshopOrder
     * @return boolean
     */
    public function edit(User $user, WebshopOrder $WebshopOrder)
    {
        return $user->hasPermission('edit', WebshopOrder::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Models\WebshopOrder  $WebshopOrder
     * @return boolean
     */
    public function delete(User $user, WebshopOrder $WebshopOrder)
    {
        return $user->hasPermission('delete', WebshopOrder::class);
    }
}
