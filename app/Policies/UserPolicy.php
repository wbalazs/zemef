<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    //use HandlesAuthorization;

    /**
     * Determine whether the user can see the users.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function index(User $user)
    {
       
        return $user->isAdmin();
        //return $user->hasPermission('index', User::class);
    }

    /**
     * Determine whether the authenticate user can create users.
     *
     * @param  \App\User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return boolean
     */
    public function update(User $user, User $model)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return boolean
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can manage other users.
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function manageUsers(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the authenticate user can manage items and other related entities(tags, categories).
     *
     * @param  \App\User  $user
     * @return boolean
     */
    public function manageItems(User $user)
    {
        return $user->isAdmin();
    }

    public function manageEmployees(User $user)
    {
        return $user->isAdmin();
    }

    public function manageProducts(User $user)
    {
        return $user->isAdmin();
    }

    public function manageCustomers(User $user)
    {
        return $user->isAdmin();
    }

    public function manageTools(User $user)
    {
         return $user->isAdmin();
    }

    public function manageBusinesses(User $user)
    {
         return $user->isAdmin();
    }

    public function manageVehicles(User $user)
    {
        return $user->isAdmin();
    }

    public function manageZmstorages(User $user)
    {
        return $user->isAdmin();
    }

    public function manageProperties(User $user)
    {
         return $user->isAdmin();
    }

    public function manageCompanies(User $user)
    {
        return $user->isAdmin();
    }

    public function manageMasters(User $user)
    {
        return $user->isAdmin();
    }

    public function managePostings(User $user)
    {
         return $user->isAdmin();
    }

    public function manageWarehouses(User $user)
    {
        return $user->isAdmin();
    }

    public function managePurchaseTickets(User $user)
    {
        return $user->isAdmin();
    }

    public function managePhotography(User $user)
    {
         return $user->isAdmin();
    }
}
