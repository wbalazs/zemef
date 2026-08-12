<?php

namespace App\Policies\Address;

use App\Models\Address\Country;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CountryPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermission('viewAny', Country::class);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response
    {
        return $user->hasPermission('create', Country::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\Country  $country
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Country $country): Response
    {
        return $user->hasPermission('update', Country::class);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\Country  $country
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Country $country): Response
    {
        return $user->hasPermission('delete', Country::class);
    }
}
