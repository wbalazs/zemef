<?php

namespace App\Traits;

trait getListByName
{
    /**
     * Közvetlenül a selectnek átadható lista
     *
     * @return array
     */
    public static function getList()
    {
        return self::orderBy('name')->pluck('name', 'id');
    }
}
