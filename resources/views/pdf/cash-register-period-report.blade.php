{{-- resources/views/pdf/cash-register-period-report.blade.php --}}
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Időszaki pénztárjelentés - {{ $cashRegister->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 16px;
            color: #666;
        }
        .report-number {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            color: #0066cc;
        }
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .info-item {
            flex: 1;
            text-align: center;
        }
        .info-label {
            font-weight: bold;
            margin-bottom: 3px;
            color: #555;
        }
        .info-value {
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8.5px;
        }
        th {
            background-color: #e6e6e6;
            border: 1px solid #000;
            padding: 6px 3px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #000;
            padding: 4px 3px;
        }
        td.amount {
            text-align: right;
            padding-right: 5px;
        }
        td.center {
            text-align: center;
        }
        .income-row {
            background-color: #f0fff0;
        }
        .expense-row {
            background-color: #fff0f0;
        }
        .separator-row td {
            background-color: #f0f0f0;
            height: 3px;
            padding: 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .summary-box {
            margin-top: 20px;
            border: 2px solid #000;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 3px 0;
            border-bottom: 1px dotted #999;
        }
        .summary-row.total {
            font-weight: bold;
            font-size: 11px;
            border-bottom: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .summary-row.final {
            font-weight: bold;
            font-size: 12px;
            color: #0066cc;
            border-top: 2px solid #000;
            padding-top: 8px;
            margin-top: 8px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .running-balance {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>IDŐSZAKI PÉNZTÁRJELENTÉS</h1>
        <h2>{{ $cashRegister->name }} ({{ $currency->code }})</h2>
        <div class="report-number">Jelentés száma: {{ $reportNumber }}</div>
    </div>

    <div class="info-box">
        <div class="info-item">
            <div class="info-label">Jelentés időszaka:</div>
            <div class="info-value">{{ $startDateFormatted }} - {{ $endDateFormatted }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Készítette:</div>
            <div class="info-value">{{ Auth::user()->name ?? 'Pénztáros' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Készítés dátuma:</div>
            <div class="info-value">{{ now()->format('Y. m. d. H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Dátum</th>
                <th style="width: 12%;">Bizonylatszám</th>
                <th style="width: 25%;">Megnevezés / Jogcím</th>
                <th style="width: 10%;">Kinek/Kitől</th>
                <th style="width: 10%;">Bevétel</th>
                <th style="width: 10%;">Kiadás</th>
                <th style="width: 10%;">Egyenleg</th>
                <th style="width: 15%;">Megjegyzés</th>
            </tr>
        </thead>
        <tbody>
            {{-- Nyitó egyenleg sor --}}
            <tr style="background-color: #e6f3ff; font-weight: bold;">
                <td colspan="6" class="text-right">Nyitó egyenleg ({{ $startDateFormatted }}):</td>
                <td class="amount">{{ $openingBalanceFormatted }}</td>
                <td class="center">{{ $currency->code }}</td>
            </tr>
            
            {{-- Tranzakciók --}}
            @foreach($transactions as $transaction)
            <tr class="{{ $transaction->type === 'income' ? 'income-row' : 'expense-row' }}">
                <td class="center">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                <td class="center">{{ $transaction->document_number }}</td>
                <td>
                    <strong>{{ $transaction->reason }}</strong>
                    @if($transaction->reason_custom)
                        <br><small>{{ $transaction->reason_custom }}</small>
                    @endif
                </td>
                <td>{{ $transaction->payer_payee }}</td>
                <td class="amount text-success">
                    @if($transaction->type === 'income')
                        {{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}
                    @else
                        0
                    @endif
                </td>
                <td class="amount text-danger">
                    @if($transaction->type === 'expense')
                        {{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}
                    @else
                        0
                    @endif
                </td>
                <td class="amount running-balance">{{ number_format($transaction->running_balance, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}</td>
                <td>{{ $transaction->description ?? '' }}</td>
            </tr>
            @endforeach
            
            {{-- Elválasztó vonal --}}
            <tr class="separator-row">
                <td colspan="8"></td>
            </tr>
            
            {{-- Záró egyenleg sor --}}
            <tr style="background-color: #e6f3ff; font-weight: bold; font-size: 10px;">
                <td colspan="6" class="text-right">Záró egyenleg ({{ $endDateFormatted }}):</td>
                <td class="amount">{{ $closingBalanceFormatted }}</td>
                <td class="center">{{ $currency->code }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Összesítő adatok --}}
    <div class="summary-box">
        <div class="summary-title">Időszaki összesítő ({{ $startDateFormatted }} - {{ $endDateFormatted }})</div>
        
        <div class="summary-row">
            <span>Tranzakciók száma összesen:</span>
            <span class="text-bold">{{ $transactions->count() }} db</span>
        </div>
        <div class="summary-row">
            <span>- ebből bevétel:</span>
            <span class="text-success text-bold">{{ $incomeCount }} db</span>
        </div>
        <div class="summary-row">
            <span>- ebből kiadás:</span>
            <span class="text-danger text-bold">{{ $expenseCount }} db</span>
        </div>
        
        <div class="summary-row" style="margin-top: 5px;">
            <span>Bevételek összesen:</span>
            <span class="text-success text-bold">{{ $totalIncomeFormatted }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row">
            <span>Kiadások összesen:</span>
            <span class="text-danger text-bold">{{ $totalExpenseFormatted }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row total">
            <span>Összes forgalom:</span>
            <span class="text-bold">{{ $totalTurnoverFormatted }} {{ $currency->code }}</span>
        </div>
        
        <div class="summary-row" style="margin-top: 5px;">
            <span>Nyitó egyenleg ({{ $startDateFormatted }}):</span>
            <span class="text-bold">{{ $openingBalanceFormatted }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row">
            <span>Időszaki változás (bevétel - kiadás):</span>
            <span class="{{ ($totalIncome - $totalExpense) >= 0 ? 'text-success' : 'text-danger' }} text-bold">
                {{ ($totalIncome - $totalExpense) >= 0 ? '+' : '' }}{{ number_format($totalIncome - $totalExpense, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}
            </span>
        </div>
        <div class="summary-row final">
            <span>Záró egyenleg ({{ $endDateFormatted }}):</span>
            <span class="text-bold">{{ $closingBalanceFormatted }} {{ $currency->code }}</span>
        </div>
    </div>

    {{-- Ellenőrző képlet --}}
    <div style="margin-top: 15px; padding: 8px; background-color: #f0f0f0; border-left: 3px solid #0066cc;">
        <div style="font-size: 8px; color: #333;">
            <strong>Ellenőrzés:</strong> Nyitó egyenleg ({{ $openingBalanceFormatted }}) + Bevételek ({{ $totalIncomeFormatted }}) - Kiadások ({{ $totalExpenseFormatted }}) = Záró egyenleg ({{ $closingBalanceFormatted }})
        </div>
    </div>

    <div class="footer">
        <p>Pénztárjelentés - {{ $cashRegister->name }} - {{ $startDateFormatted }} - {{ $endDateFormatted }}</p>
        <p>Készült: {{ now()->format('Y. m. d. H:i:s') }} - {{ config('app.name') }}</p>
    </div>
</body>
</html>