<?php

namespace App\Models\DataTransferObjects;

use Spatie\LaravelData\Data;
use Carbon\Carbon;

class CashTransactionData extends Data
{
    public function __construct(
        public readonly ?int $transaction_id,
        public readonly int $cash_register_id,
        public readonly string $document_number,
        public readonly Carbon $transaction_date,
        public readonly string $type,
        public readonly float $amount,
        public readonly string $payer_payee,
        public readonly string $reason,
        public readonly ?string $reason_custom,
        public readonly ?string $description,
        public readonly int $created_by,
        public readonly ?float $exchange_rate,
        public readonly ?array $denominations,
        public readonly float $balance_before,
        public readonly float $balance_after,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            transaction_id: $data['transaction_id'] ?? null,
            cash_register_id: $data['cash_register_id'],
            document_number: $data['document_number'],
            transaction_date: Carbon::parse($data['transaction_date']),
            type: $data['type'],
            amount: (float)$data['amount'],
            payer_payee: $data['payer_payee'],
            reason: $data['reason'],
            reason_custom: $data['reason_custom'] ?? null,
            description: $data['description'] ?? null,
            created_by: $data['created_by'],
            exchange_rate: isset($data['exchange_rate']) ? (float)$data['exchange_rate'] : null,
            denominations: $data['denominations'] ?? null,
            balance_before: (float)$data['balance_before'],
            balance_after: (float)$data['balance_after']
        );
    }
}