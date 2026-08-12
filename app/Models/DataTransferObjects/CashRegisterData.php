<?php
// app/Models/CashRegister/DataTransferObjects/CashRegisterData.php

namespace App\Models\DataTransferObjects;

use Spatie\LaravelData\Data;
use Carbon\Carbon;

class CashRegisterData extends Data
{
    public function __construct(
        public readonly ?int $cash_register_id,
        public readonly string $name,
        public readonly string $prefix,
        public readonly int $currency_id,
        public readonly float $opening_balance,
        public readonly Carbon $opening_balance_date,
        public readonly int $business_id,
        public readonly ?int $employee_id,
        public readonly bool $is_active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            cash_register_id: $data['cash_register_id'] ?? null,
            name: $data['name'],
            prefix: $data['prefix'],
            currency_id: $data['currency_id'],
            opening_balance: (float)$data['opening_balance'],
            opening_balance_date: Carbon::parse($data['opening_balance_date']),
            business_id: $data['business_id'],
            employee_id: $data['employee_id'] ?? null,
            is_active: $data['is_active'] ?? true
        );
    }
}