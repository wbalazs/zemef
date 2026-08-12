<?php

namespace App\Policies;

use App\User;
use App\Models\PurchaseTicket;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseTicketPolicy
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
        return $user->hasPermission('index', PurchaseTicket::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', PurchaseTicket::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, PurchaseTicket $purchaseticket)
    {
        return $user->hasPermission('edit', PurchaseTicket::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, PurchaseTicket $purchaseticket)
    {
        return $user->hasPermission('delete', PurchaseTicket::class);
    }
}
