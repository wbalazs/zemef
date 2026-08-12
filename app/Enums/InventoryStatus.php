<?php

namespace App\Enums;

enum InventoryStatus: int
{
    case SOLD_OUT = 0;
    case IN_STOCK = 1;
    case PENDING = 2;
    case UNDER_REPAIR = 3;
    case CONSIGNMENT = 4;
    case RESERVED = 5;
    case DELIVERY_NOTE = 6;
    case WAREHOUSE_TRANSFER = 7;
    case HALLMARK_IN_PROGRESS = 8;

    public function label(): string
    {
        return match ($this) {
            self::SOLD_OUT => 'Kivezetve',
            self::IN_STOCK => 'Raktaron',
            self::PENDING => 'Fuggoben',
            self::UNDER_REPAIR => 'Javitas alatt',
            self::CONSIGNMENT => 'Bizomanyban',
            self::RESERVED => 'Foglalt',
            self::DELIVERY_NOTE => 'Szallitolevel',
            self::WAREHOUSE_TRANSFER => 'Raktarkozi atadas',
            self::HALLMARK_IN_PROGRESS => 'Femjelzes alatt',
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
