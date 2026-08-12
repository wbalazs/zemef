<?php

namespace App\Services\CashRegister;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\DataTransferObjects\CashTransactionData;
use App\Services\CashRegister\DataTransferObjects\AutoCashTransactionData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class CreateAutoCashTransaction
{
    protected AutoCashTransactionData $data;

    public function __construct(AutoCashTransactionData $data)
    {
        $this->data = $data;
    }

    public static function from(AutoCashTransactionData $data): self
    {
        return new self($data);
    }

    public function handle(): CashTransaction
    {
        $user = Auth::user();

        $businessId = $this->data->business_id ?? $user?->employee?->active_business_id;
        $employeeId = $this->data->employee_id ?? $user?->employee_id;
        $createdBy = $this->data->created_by ?? $user?->id;

        if (!$businessId) {
            throw new InvalidArgumentException('Active business is required for automatic cash transaction.');
        }

        if (!$createdBy) {
            throw new InvalidArgumentException('Created by user is required for automatic cash transaction.');
        }

        $transactionDate = $this->data->transaction_date ?? Carbon::now();

        $cashRegister = CashRegister::where('business_id', $businessId)
            ->where('is_active', true)
            ->orderByDesc('cash_register_id')
            ->first();

        if (!$cashRegister) {
            $cashRegister = $this->createAutoCashRegister(
                (int) $businessId,
                $employeeId ? (int) $employeeId : null,
                $transactionDate
            );
        }

        $cashData = CashTransactionData::fromArray([
            'cash_register_id' => $cashRegister->cash_register_id,
            'document_number' => '',
            'transaction_date' => $transactionDate,
            'type' => $this->data->type,
            'amount' => max(0, (float) $this->data->amount),
            'payer_payee' => $this->data->payer_payee,
            'reason' => $this->data->reason,
            'reason_custom' => $this->data->reason_custom,
            'description' => $this->data->description,
            'created_by' => (int) $createdBy,
            'exchange_rate' => $this->data->exchange_rate,
            'denominations' => $this->data->denominations,
            'balance_before' => 0,
            'balance_after' => 0,
        ]);

        return CreateCashTransaction::from($cashData)->handle();
    }

    protected function createAutoCashRegister(int $businessId, ?int $employeeId, Carbon $date): CashRegister
    {
        $dateToken = $date->format(config('cash_transactions.auto_register.date_format', 'ymd'));

        $namePattern = (string) config('cash_transactions.auto_register.name_pattern', 'AUTO_{date}');
        $prefixPattern = (string) config('cash_transactions.auto_register.prefix_pattern', 'AUTO_{date}');

        $name = str_replace('{date}', $dateToken, $namePattern);
        $basePrefix = str_replace('{date}', $dateToken, $prefixPattern);
        $prefix = $this->resolveUniquePrefix($basePrefix);

        return CashRegister::create([
            'name' => $name,
            'prefix' => $prefix,
            'currency_id' => (int) config('cash_transactions.auto_register.currency_id', 6),
            'opening_balance' => (float) config('cash_transactions.auto_register.opening_balance', 0),
            'opening_balance_date' => $date->toDateString(),
            'business_id' => $businessId,
            'employee_id' => $employeeId,
            'is_active' => true,
        ]);
    }

    protected function resolveUniquePrefix(string $basePrefix): string
    {
        $prefix = $basePrefix;
        $suffix = 2;

        while (CashRegister::where('prefix', $prefix)->exists()) {
            $prefix = $basePrefix . '_' . $suffix;
            $suffix++;
        }

        return $prefix;
    }
}
