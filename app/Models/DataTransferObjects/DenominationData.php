<?php
// app/Models/Denomination/DataTransferObjects/DenominationData.php

namespace App\Models\DataTransferObjects;

use Spatie\LaravelData\Data;

class DenominationData extends Data
{
    public function __construct(
        public readonly ?int $denomination_id,
        public readonly int $currency_id,
        public readonly string $name,
        public readonly float $value,
        public readonly string $type,
        public readonly bool $is_active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            denomination_id: $data['denomination_id'] ?? null,
            currency_id: $data['currency_id'],
            name: $data['name'],
            value: (float)$data['value'],
            type: $data['type'] ?? 'coin',
            is_active: $data['is_active'] ?? true
        );
    }
}