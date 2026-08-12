<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ability',
        'model',
    ];

    /**
     * Megmondja, hogy ez a Permission egyenlő-e a paraméterekben megadottal
     */
    public function isEqualTo(string $ability, string $model): bool
    {
        return
            $this->model == $model &&
            $this->ability == $ability;
    }

    /**
     * The roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
