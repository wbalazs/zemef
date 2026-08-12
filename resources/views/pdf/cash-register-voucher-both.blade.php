{{-- resources/views/pdf/cash-register-voucher-both.blade.php --}}
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Összesítő pénztárbizonylat - {{ $cashRegister->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 5px 0 0;
            font-size: 16px;
            color: #333;
        }
        .document-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-col {
            display: table-cell;
            padding: 0 5px;
        }
        .info-col-4 {
            width: 33.33%;
        }
        .label {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .value {
            border-bottom: 1px solid #999;
            padding: 3px 0;
            min-height: 18px;
        }
        .voucher-container {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        .voucher-half {
            flex: 1;
            border: 1px solid #000;
            padding: 10px;
        }
        .voucher-half.income {
            border-right: 2px solid #000;
        }
        .voucher-half h4 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
            color: {{ $incomeTotal > $expenseTotal ? '#28a745' : '#dc3545' }};
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }
        table.items th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }
        table.items td {
            border: 1px solid #000;
            padding: 3px;
        }
        table.items td.amount {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary-box {
            margin-top: 15px;
            padding: 10px;
            border: 2px solid #000;
            background-color: #f5f5f5;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .balance {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-item {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-bottom: 5px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ÖSSZESÍTŐ PÉNZTÁRBIZONYLAT</h2>
        <h3>{{ $cashRegister->name }}</h3>
        <div class="document-number">{{ $documentNumber }}</div>
    </div>

    <div class="info-row">
        <div class="info-col info-col-4">
            <div class="label">Dátum:</div>
            <div class="value">{{ $dateFormatted }}</div>
        </div>
        <div class="info-col info-col-4">
            <div class="label">Devizanem:</div>
            <div class="value">{{ $currency->code }}</div>
        </div>
        <div class="info-col info-col-4">
            <div class="label">Pénztáros:</div>
            <div class="value">{{ $incomeTransactions->first()->createdBy->name ?? $expenseTransactions->first()->createdBy->name ?? '' }}</div>
        </div>
    </div>

    <div class="voucher-container">
        {{-- Bevételi oldal --}}
        <div class="voucher-half income">
            <h4 class="text-success">BEVÉTELEK</h4>
            
            <table class="items">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Bizonylatszám</th>
                        <th style="width: 25%;">Kinek/Kitől</th>
                        <th style="width: 30%;">Jogcím</th>
                        <th style="width: 20%;">Összeg</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomeTransactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $transaction->document_number }}</td>
                        <td>{{ $transaction->payer_payee }}</td>
                        <td>
                            {{ $transaction->reason }}
                            @if($transaction->reason_custom)
                                <br><small>({{ $transaction->reason_custom }})</small>
                            @endif
                        </td>
                        <td class="amount text-success">{{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Nincs bevétel ezen a napon</td>
                    </tr>
                    @endforelse
                    @if($incomeTransactions->count() > 0)
                    <tr class="total-row">
                        <td colspan="4" class="text-right text-bold">BEVÉTEL ÖSSZESEN:</td>
                        <td class="amount text-bold text-success">{{ $incomeTotalFormatted }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            
            @if($incomeTransactions->count() > 0)
            <div class="text-center mt-10">
                <div class="text-bold">Bevétel összeg betűvel:</div>
                <div class="amount-text">{{ $incomeTotalText }}</div>
            </div>
            @endif
        </div>

        {{-- Kiadási oldal --}}
        <div class="voucher-half expense">
            <h4 class="text-danger">KIADÁSOK</h4>
            
            <table class="items">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Bizonylatszám</th>
                        <th style="width: 25%;">Kinek/Kitől</th>
                        <th style="width: 30%;">Jogcím</th>
                        <th style="width: 20%;">Összeg</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseTransactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $transaction->document_number }}</td>
                        <td>{{ $transaction->payer_payee }}</td>
                        <td>
                            {{ $transaction->reason }}
                            @if($transaction->reason_custom)
                                <br><small>({{ $transaction->reason_custom }})</small>
                            @endif
                        </td>
                        <td class="amount text-danger">{{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Nincs kiadás ezen a napon</td>
                    </tr>
                    @endforelse
                    @if($expenseTransactions->count() > 0)
                    <tr class="total-row">
                        <td colspan="4" class="text-right text-bold">KIADÁS ÖSSZESEN:</td>
                        <td class="amount text-bold text-danger">{{ $expenseTotalFormatted }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            
            @if($expenseTransactions->count() > 0)
            <div class="text-center mt-10">
                <div class="text-bold">Kiadás összeg betűvel:</div>
                <div class="amount-text">{{ $expenseTotalText }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Összesítő --}}
    <div class="summary-box">
        <div class="summary-row">
            <span class="text-bold">Napi nyitó egyenleg:</span>
            <span>{{ number_format($cashRegister->getBalanceForDate($date->copy()->subDay()), $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row">
            <span class="text-bold text-success">Összes bevétel:</span>
            <span class="text-success">+ {{ $incomeTotalFormatted }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row">
            <span class="text-bold text-danger">Összes kiadás:</span>
            <span class="text-danger">- {{ $expenseTotalFormatted }} {{ $currency->code }}</span>
        </div>
        <div class="summary-row" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
            <span class="text-bold">Napi változás:</span>
            <span class="{{ ($incomeTotal - $expenseTotal) >= 0 ? 'text-success' : 'text-danger' }}">
                {{ ($incomeTotal - $expenseTotal) >= 0 ? '+' : '' }}{{ number_format($incomeTotal - $expenseTotal, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}
            </span>
        </div>
        <div class="summary-row balance">
            <span class="text-bold">Napi záró egyenleg:</span>
            <span>{{ number_format($cashRegister->getBalanceForDate($date), $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}</span>
        </div>
    </div>

    <div class="footer">
        <div class="signature">
            <div class="signature-item">
                <div class="signature-line"></div>
                <span>Pénztáros</span>
            </div>
            <div class="signature-item">
                <div class="signature-line"></div>
                <span>Ellenőrizte</span>
            </div>
            <div class="signature-item">
                <div class="signature-line"></div>
                <span>Jóváhagyta</span>
            </div>
        </div>
    </div>
</body>
</html>