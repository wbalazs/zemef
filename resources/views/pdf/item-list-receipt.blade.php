{{-- resources/views/pdf/item-list-receipt.blade.php --}}
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Átvételi jegyzőkönyv</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            height: 267mm;
            page-break-inside: avoid;
        }

        .half {
            height: 129mm;
            margin-bottom: 5mm;
        }

        h2 {
            text-align: center;
            font-size: 18px;
            margin: 15px 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .cut-line {
            text-align: center;
            font-size: 12px;
            margin: 5px 0;
        }

        .cut-line::before {
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .signature td {
            border: none;
            padding-top: 2px;
            padding-bottom: 0;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .underline {
            text-decoration: underline;
        }

        .mt20 {
            margin-top: 20px;
        }

        .mb10 {
            margin-bottom: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .info-box {
            border: 1px solid #000;
            padding: 8px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }

        .total-row {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding: 8px;
            border-top: 2px solid #000;
        }
        
        .text-danger {
            color: #dc3545;
        }
    </style>
</head>
<body>

<div class="wrapper">
    @for($i = 0; $i < 1; $i++)
        <div class="half">
            {{-- Fejléc --}}
            <table class="header-table">
                <tr>
                    <td width="70%" align="left" valign="top" style="padding-top: 0; border: none;">
                        <p class="bold">Sorszám:</p>
                        <p>{{ $documentNumber }}</p>
                        <p class="bold">Átvétel dátuma:</p>
                        <p>{{ $date->format('Y. m. d.') }}</p>
                    </td>
                    <td width="25%" align="right" valign="top" style="padding-top: 0; border: none;">
                         <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="125" height="63">
                    </td>
                    <td width="5%" align="right" valign="top" style="padding-top: 0; border: none;"></td>
                </tr>
            </table>

            <h2>ÁTVÉTELI JEGYZŐKÖNYV</h2>

            {{-- Átvevő és átadó adatai --}}
            <div class="info-box">
                <table style="border: none; margin: 0;">
                    <tr>
                        <td style="border: none; width: 50%;">
                            <p class="bold">Átvevő (Vevő):</p>
                            <p><strong>Zemef Kft.</strong></p>
                            <p>6500 Baja, Dózsa György út 75.</p>
                            <p>Adószám: 13192189-2-03</p>
                            <p>Cgj.: 03-09-138127</p>
                        </td>
                        <td style="border: none; width: 50%;">
                            <p class="bold">Átadó (Eladó):</p>
                            @if($customer)
                                <p><strong>{{ $customer->name ?? 'Ismeretlen' }}</strong></p>
                                @php $address = $customer->addresses()->where('address_type', 'Székhely')->first(); @endphp
                                @if($address)
                                    <p>{{ $address->zip ?? '' }} {{ $address->city ?? '' }}, {{ $address->address ?? '' }}</p>
                                @endif
                            @endif
                            @if($customer && $customer->identity_card_number)
                                <p>Szem. ig. száma: {{ $customer->identity_card_number }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <p class="bold underline">Átvett tételek:</p>

            {{-- Tételek táblázata az item_list_items táblából --}}
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Megnevezés</th>
                        <th style="width: 25%;">Leírás / Gyártási szám</th>
                        <th style="width: 8%;">Db</th>
                        <th style="width: 10%;">Súly (g)</th>
                        <th style="width: 15%;">Egységár</th>
                        <th style="width: 17%;">Összesen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            {{ $item->description ?? '' }}
                            @if($item->factory_number)
                                <br><small>Gyártási szám: {{ $item->factory_number }}</small>
                            @endif
                            @if($item->hallmark)
                                <br><small>Fémjelzés: Igen</small>
                            @endif
                            @if($item->is_fake)
                                <br><small class="text-danger">Hamisítvány!</small>
                            @endif
                        </td>
                        <td class="center">{{ $item->quantity ?? 1 }}</td>
                        <td class="right">{{ number_format($item->weight, 2, ',', ' ') }}</td>
                        <td class="right">{{ number_format($item->oneprice ?? 0, 0, ',', ' ') }} Ft</td>
                        <td class="right">{{ number_format($item->price ?? 0, 0, ',', ' ') }} Ft</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="center">Nincsenek átvett tételek</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right bold">ÖSSZESEN:</td>
                        <td class="right bold">{{ number_format($totalWeight, 2, ',', ' ') }} g</td>
                        <td class="right bold"></td>
                        <td class="right bold">{{ number_format($totalAmount, 0, ',', ' ') }} Ft</td>
                    </tr>
                </tfoot>
                @endif
            </table>

            {{-- Összesítő adatok --}}
            <div class="summary-box">
                <div>
                    <p>
                        <strong>Átvett tételek száma:</strong> {{ $totalQuantity }} db, 
                        <strong>Összes súly:</strong> {{ number_format($totalWeight, 2, ',', ' ') }} g</p>
                </div>
                <p><strong>Kelt:</strong> Baja, {{ $date->format('Y. m. d.') }}</p>    
            </div>

            

            {{-- Aláírások --}}
            <table class="signature" style="margin-top: 25px;">
                <tr>
                    <td class="center" style="padding-bottom: 2px; width: 40%;">
                        _____________________________________________
                    </td>
                    <td style="width: 20%;"></td>
                    <td class="center" style="padding-bottom: 2px; width: 40%;">
                        _____________________________________________
                    </td>
                </tr>
                <tr>
                    <td class="center bold">Átadó (Eladó)</td>
                    <td></td>
                    <td class="center bold">Átvevő (Vevő)</td>
                </tr>
                <tr>
                    <td class="center">{{ $customerData['name'] ?? '' }}</td>
                    <td></td>
                    <td class="center">
                        Zemef Kft.<br>
                        {{ $employeeName ?? $userName ?? '' }}
                    </td>
                </tr>
            </table>
            
            {{-- Egyéb megjegyzések --}}
            @if($itemList->comment || $itemList->inv_comment)
            <div style="margin-top: 10px; font-size: 9px;">
                <p><strong>Megjegyzés:</strong> {{ $itemList->comment ?? $itemList->inv_comment }}</p>
            </div>
            @endif
        </div>

        @if($i == 0)
            <div class="cut-line"></div>
        @endif
    @endfor
</div>

</body>
</html>