<?php

namespace App\Enums;

enum ConsignmentReceiptStatus: int
{
    case IN_PROGRESS = 1;
    case UNDER_REPAIR = 2;
    case UNDER_HALLMARKING = 3;
    case IN_TRANSIT = 4;
    case FOR_SALE = 5;
    case RETURNED_TO_CUSTOMER = 6;

    public function label(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'Folyamatban',
            self::UNDER_REPAIR => 'Javítás alatt',
            self::UNDER_HALLMARKING => 'Fémjelzés alatt',
            self::IN_TRANSIT => 'Szállítás alatt',
            self::FOR_SALE => 'Értékesítés alatt',
            self::RETURNED_TO_CUSTOMER => 'Ügyfél visszavette',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}