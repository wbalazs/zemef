<?php

namespace App\Enums;

class ProductTipus
{
    public const NYAKLANC = 1;
    public const FULBEVALO = 2;
    public const GYURU = 3;
    public const KARKOTO = 4;

    protected static array $data = [
        self::NYAKLANC => 'Nyaklánc',
        self::FULBEVALO => 'Fülbevaló',
        self::GYURU => 'Gyűrű',
        self::KARKOTO => 'Karkötő',
    ];

    public static function getName(?int $value): string
    {
        if ($value === null) {
            return '-';
        }

        return self::$data[$value] ?? '-';
    }

    public static function toArray(): array
    {
        return self::$data;
    }

    public static function values(): array
    {
        return array_keys(self::$data);
    }
}
