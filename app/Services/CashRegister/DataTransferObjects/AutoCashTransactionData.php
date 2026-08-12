<?php

namespace App\Services\CashRegister\DataTransferObjects;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class AutoCashTransactionData extends Data
{
    public function __construct(
        public readonly string $type,
        public readonly float $amount,
        public readonly string $payer_payee,
        public readonly string $reason,
        public readonly ?string $reason_custom = null,
        public readonly ?string $description = null,
        public readonly ?Carbon $transaction_date = null,
        public readonly ?int $business_id = null,
        public readonly ?int $employee_id = null,
        public readonly ?int $created_by = null,
        public readonly ?float $exchange_rate = null,
        public readonly ?array $denominations = null,
    ) {}
}
