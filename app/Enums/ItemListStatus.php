<?php

namespace App\Enums;

enum ItemListStatus: int
{
    case STORNO = 0;
    case PROCESSING = 1;
    case CLOSED = 2;
    case PRINTED = 3;
    case BOUGHT_BACK = 4;
    case PRODUCT = 5;

    public function label(): string
    {
        return match ($this) {
            self::STORNO => 'Sztorno',
            self::PROCESSING => 'Feldolgozas alatt',
            self::CLOSED => 'Lezarva',
            self::PRINTED => 'Nyomtatva',
            self::BOUGHT_BACK => 'Újravásárolva',
            self::PRODUCT => 'Termék',

        };
    }
}
