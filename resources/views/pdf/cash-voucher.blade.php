{{-- resources/views/pdf/cash-voucher.blade.php --}}
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($voucherType) }} pénztárbizonylat</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
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
        .row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .col {
            display: table-cell;
            padding: 0 5px;
        }
        .col-3 {
            width: 25%;
        }
        .col-4 {
            width: 33.33%;
        }
        .col-6 {
            width: 50%;
        }
        .col-8 {
            width: 66.66%;
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
        .stamp {
            position: relative;
            margin-top: 10px;
        }
        .stamp-circle {
            border: 2px solid #000;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            position: absolute;
            right: 20px;
            top: -40px;
            opacity: 0.3;
            transform: rotate(-15deg);
        }
        .stamp-text {
            position: absolute;
            right: 25px;
            top: -20px;
            font-size: 16px;
            font-weight: bold;
            color: #666;
            transform: rotate(-15deg);
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
            <h2>{{ strtoupper($voucherType) }} PÉNZTÁRBIZONYLAT</h2>
            <h3>{{ $cashRegister->name }}</h3>
            <div class="document-number">Szám: {{ $voucherNumber }}</div>
            <div style="font-size: 10px; color: #666;">(első példány - nyilvántartás)</div>
        </div>

        <div class="row">
            <div class="col col-6">
                <div class="label">Kelt:</div>
                <div class="value">{{ $date->format('Y. m. d.') }}</div>
            </div>
            <div class="col col-6">
                <div class="label">Pénztáros:</div>
                <div class="value">{{ $transaction->createdBy->name ?? '' }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col col-12">
                <div class="label">{{ $voucherType === 'bevételi' ? 'Befizető' : 'Kifizetett' }}:</div>
                <div class="value">{{ $transaction->payer_payee }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col col-12">
                <div class="label">Jogcím:</div>
                <div class="value">{{ $transaction->reason }} {{ $transaction->reason_custom ? '(' . $transaction->reason_custom . ')' : '' }}</div>
            </div>
        </div>

        <div class="amount-box">
            <div class="label">{{ $voucherType === 'bevételi' ? 'Befizetett' : 'Kifizetett' }} összeg:</div>
            <div class="amount">{{ $amountFormatted }}</div>
            <div class="currency">{{ $currency->code }}</div>
        </div>

        <div class="amount-text">
            <strong>Összeg betűvel:</strong> {{ $amountText }}
        </div>

        @if($transaction->description)
        <div class="row">
            <div class="col col-12">
                <div class="label">Megjegyzés:</div>
                <div class="value">{{ $transaction->description }}</div>
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="row">
                <div class="col col-6">
                    <div class="label">Átvevő aláírása:</div>
                    <div class="signature-line"></div>
                </div>
                <div class="col col-6">
                    <div class="label">Pénztáros aláírása:</div>
                    <div class="signature-line"></div>
                </div>
            </div>
            
            <div class="stamp">
                <div class="stamp-circle"></div>
                <div class="stamp-text">PÉNZTÁR</div>
            </div>
        </div>
    </div>

    {{-- Második példány --}}
    <div class="voucher" style="page-break-before: always;">
        <div class="header">
            <h2>{{ strtoupper($voucherType) }} PÉNZTÁRBIZONYLAT</h2>
            <h3>{{ $cashRegister->name }}</h3>
            <div class="document-number">Szám: {{ $voucherNumber }}</div>
            <div style="font-size: 10px; color: #666;">(második példány - bizonylat)</div>
        </div>

        <div class="row">
            <div class="col col-6">
                <div class="label">Kelt:</div>
                <div class="value">{{ $date->format('Y. m. d.') }}</div>
            </div>
            <div class="col col-6">
                <div class="label">Pénztáros:</div>
                <div class="value">{{ $transaction->createdBy->name ?? '' }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col col-12">
                <div class="label">{{ $voucherType === 'bevételi' ? 'Befizető' : 'Kifizetett' }}:</div>
                <div class="value">{{ $transaction->payer_payee }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col col-12">
                <div class="label">Jogcím:</div>
                <div class="value">{{ $transaction->reason }} {{ $transaction->reason_custom ? '(' . $transaction->reason_custom . ')' : '' }}</div>
            </div>
        </div>

        <div class="amount-box">
            <div class="label">{{ $voucherType === 'bevételi' ? 'Befizetett' : 'Kifizetett' }} összeg:</div>
            <div class="amount">{{ $amountFormatted }}</div>
            <div class="currency">{{ $currency->code }}</div>
        </div>

        <div class="amount-text">
            <strong>Összeg betűvel:</strong> {{ $amountText }}
        </div>

        @if($transaction->description)
        <div class="row">
            <div class="col col-12">
                <div class="label">Megjegyzés:</div>
                <div class="value">{{ $transaction->description }}</div>
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="row">
                <div class="col col-6">
                    <div class="label">Átvevő aláírása:</div>
                    <div class="signature-line"></div>
                </div>
                <div class="col col-6">
                    <div class="label">Pénztáros aláírása:</div>
                    <div class="signature-line"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>