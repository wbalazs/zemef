<?php

namespace App\Policies;

use App\User;
use App\Models\PurchaseTicketItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseTicketItemPolicy
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
        return $user->hasPermission('index', PurchaseTicketItem::class);
    }

    /**
     * Determine whether the user can create items.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->hasPermission('create', PurchaseTicketItem::class);
    }

    /**
     * Determine whether the user can update the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function update(User $user, PurchaseTicketItem $purchaseticketitem)
    {
        return $user->hasPermission('edit', PurchaseTicketItem::class);
    }

    /**
     * Determine whether the user can delete the item.
     *
     * @param  \App\User  $user
     * @param  \App\Item  $item
     * @return boolean
     */
    public function delete(User $user, PurchaseTicketItem $purchaseticketitem)
    {
        return $user->hasPermission('delete', PurchaseTicketItem::class);
    }
}
