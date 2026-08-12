<?php

namespace App\Policies;

use App\Models\BillingoBill;
use App\User;
use Illuminate\Auth\Access\Response;

class BillingoBillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function index(User $user): bool
    {
        //return $user->can('view billingo bills');
         return true;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->can('view billingo bills');
         return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BillingoBill $billingoBill): bool
    {
        //return $user->can('view billingo bills');
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BillingoBill $billingoBill): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BillingoBill $billingoBill): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BillingoBill $billingoBill): bool
    {
        //
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BillingoBill $billingoBill): bool
    {
        //
        return true;
    }
}
