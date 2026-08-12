{{-- resources/views/pdf/cash-register-voucher.blade.php --}}
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeName }} pénztárbizonylat - {{ $cashRegister->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .voucher {
            width: 100%;
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .voucher:first-child {
            margin-top: 0;
        }
        .voucher:last-child {
            margin-bottom: 0;
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
        .info-col-3 {
            width: 25%;
        }
        .info-col-4 {
            width: 33.33%;
        }
        .info-col-6 {
            width: 50%;
        }
        .label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .value {
            border-bottom: 1px solid #999;
            padding: 3px 0;
            min-height: 20px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.items th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        table.items td {
            border: 1px solid #000;
            padding: 6px;
        }
        table.items td.amount {
            text-align: right;
        }
        table.items tr.total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .amount-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            background-color: #f9f9f9;
        }
        .amount-box .amount {
            font-size: 24px;
            font-weight: bold;
        }
        .amount-box .currency {
            font-size: 14px;
            margin-left: 5px;
        }
        .amount-text {
            font-style: italic;
            margin: 10px 0;
            padding: 5px;
            border-top: 1px dashed #999;
            border-bottom: 1px dashed #999;
        }
        .summary {
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .signature {
            margin-top: 30px;
        }
        .signature-line {
            display: inline-block;
            width: 45%;
            border-top: 1px solid #000;
            margin: 0 2%;
            padding-top: 5px;
            text-align: center;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mt-20 { margin-top: 20px; }
        .mb-10 { margin-bottom: 10px; }
    </style>
</head>
<body>
    {{-- Első példány --}}
    <div class="voucher">
        <div class="header">
            <h2>{{ strtoupper($typeName) }} PÉNZTÁRBIZONYLAT</h2>
            <h3>{{ $cashRegister->name }}</h3>
            <div class="document-number">Szám: {{ $documentNumber }}</div>
            <div style="font-size: 10px; color: #666;">(első példány - nyilvántartás)</div>
        </div>

        <div class="info-row">
            <div class="info-col info-col-4">
                <div class="label">Pénztár megnevezése:</div>
                <div class="value">{{ $cashRegister->name }}</div>
            </div>
            <div class="info-col info-col-4">
                <div class="label">Devizanem:</div>
                <div class="value">{{ $currency->code }} ({{ $currency->name }})</div>
            </div>
            <div class="info-col info-col-4">
                <div class="label">Kelt:</div>
                <div class="value">{{ $dateFormatted }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-col info-col-12">
                <div class="label">Bizonylat típusa:</div>
                <div class="value">{{ $typeName === 'bevételi' ? 'BEVÉTEL' : 'KIADÁS' }}</div>
            </div>
        </div>

        <h4 style="margin: 15px 0 5px;">Tételek:</h4>
        
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Bizonylatszám</th>
                    <th style="width: 20%;">Kinek/Kitől</th>
                    <th style="width: 30%;">Jogcím</th>
                    <th style="width: 15%;">Összeg</th>
                    <th style="width: 15%;">Pénztáros</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
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
                    <td class="amount">{{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}</td>
                    <td>{{ $transaction->createdBy->name ?? '' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right text-bold">ÖSSZESEN:</td>
                    <td class="amount text-bold">{{ $totalAmountFormatted }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="amount-box">
            <div class="label">{{ $typeName === 'bevételi' ? 'Befizetett' : 'Kifizetett' }} összeg:</div>
            <div class="amount">{{ $totalAmountFormatted }}</div>
            <div class="currency">{{ $currency->code }}</div>
        </div>

        <div class="amount-text">
            <strong>Összeg betűvel:</strong> {{ $totalAmountText }}
        </div>

        <div class="footer">
            <div class="info-row">
                <div class="info-col info-col-6">
                    <div class="label">Nyitó egyenleg ({{ $dateFormatted }}):</div>
                    <div class="value">{{ number_format($cashRegister->getBalanceForDate($date->copy()->subDay()), $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}</div>
                </div>
                <div class="info-col info-col-6">
                    <div class="label">Záró egyenleg ({{ $dateFormatted }}):</div>
                    <div class="value">{{ number_format($cashRegister->getBalanceForDate($date), $currency->code === 'HUF' ? 0 : 2, ',', ' ') }} {{ $currency->code }}</div>
                </div>
            </div>
            
            <div class="signature">
                <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-bottom: 5px;"></div>
                        <span>Pénztáros aláírása</span>
                    </div>
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-bottom: 5px;"></div>
                        <span>Ellenőrizte</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Második példány --}}
    <div class="voucher" style="page-break-before: always;">
        <div class="header">
            <h2>{{ strtoupper($typeName) }} PÉNZTÁRBIZONYLAT</h2>
            <h3>{{ $cashRegister->name }}</h3>
            <div class="document-number">Szám: {{ $documentNumber }}</div>
            <div style="font-size: 10px; color: #666;">(második példány - bizonylat)</div>
        </div>

        <div class="info-row">
            <div class="info-col info-col-4">
                <div class="label">Pénztár megnevezése:</div>
                <div class="value">{{ $cashRegister->name }}</div>
            </div>
            <div class="info-col info-col-4">
                <div class="label">Devizanem:</div>
                <div class="value">{{ $currency->code }} ({{ $currency->name }})</div>
            </div>
            <div class="info-col info-col-4">
                <div class="label">Kelt:</div>
                <div class="value">{{ $dateFormatted }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-col info-col-12">
                <div class="label">Bizonylat típusa:</div>
                <div class="value">{{ $typeName === 'bevételi' ? 'BEVÉTEL' : 'KIADÁS' }}</div>
            </div>
        </div>

        <h4 style="margin: 15px 0 5px;">Tételek:</h4>
        
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Bizonylatszám</th>
                    <th style="width: 20%;">Kinek/Kitől</th>
                    <th style="width: 30%;">Jogcím</th>
                    <th style="width: 15%;">Összeg</th>
                    <th style="width: 15%;">Pénztáros</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
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
                    <td class="amount">{{ number_format($transaction->amount, $currency->code === 'HUF' ? 0 : 2, ',', ' ') }}</td>
                    <td>{{ $transaction->createdBy->name ?? '' }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right text-bold">ÖSSZESEN:</td>
                    <td class="amount text-bold">{{ $totalAmountFormatted }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="amount-box">
            <div class="label">{{ $typeName === 'bevételi' ? 'Befizetett' : 'Kifizetett' }} összeg:</div>
            <div class="amount">{{ $totalAmountFormatted }}</div>
            <div class="currency">{{ $currency->code }}</div>
        </div>

        <div class="amount-text">
            <strong>Összeg betűvel:</strong> {{ $totalAmountText }}
        </div>

        <div class="footer">
            <div class="signature">
                <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-bottom: 5px;"></div>
                        <span>Pénztáros aláírása</span>
                    </div>
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-bottom: 5px;"></div>
                        <span>Ellenőrizte</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>