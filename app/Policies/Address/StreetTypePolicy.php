<?php

namespace App\Policies\Address;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Address\StreetType;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class StreetTypePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermission('viewAny', StreetType::class);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response
    {
        return $user->hasPermission('create', StreetType::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\StreetType  $streetType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, StreetType $streetType): Response
    {
        return $user->hasPermission('update', StreetType::class);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\StreetType  $streetType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, StreetType $streetType): Response
    {
        return $user->hasPermission('delete', StreetType::class);
    }
}
