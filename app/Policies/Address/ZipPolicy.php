<?php

namespace App\Policies\Address;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Address\Zip;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class ZipPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermission('viewAny', Zip::class);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response
    {
        return $user->hasPermission('create', Zip::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\Zip  $zip
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Zip $zip): Response
    {
        return $user->hasPermission('update', Zip::class);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Address\Zip  $zip
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Zip $zip): Response
    {
        return $user->hasPermission('delete', Zip::class);
    }
}
