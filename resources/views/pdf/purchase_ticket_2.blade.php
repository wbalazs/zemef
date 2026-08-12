<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Vételi jegy</title>
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
      font-size: 16px;
      margin: 10px 0;
    }


    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
    }

    th, td {
      border: 1px solid #000;
      padding: 4px;
    }

    .signature td {
      border: none;
      padding-top: 2px;
      padding-bottom: 0;
    }

    .center {
      text-align: center;
    }

    .underline {
      text-decoration: underline;
    }

    .mt20 {
      margin-top: 20px;
    }

    .bold {
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="wrapper">
  @for($i = 0; $i < 2; $i++)
    <div class="half" {!! $i === 0 ? 'style="page-break-after: always;"' : '' !!}>
      <table>
        <tr>
            <td width="70%" align="left" valign="top" style="padding-top: 0;border: none;">
                <p class="ln_0_3"><strong>Szerződés megkötésének helye:</strong> {{ $data['purchase_ticket']->business->zip }} {{ $data['purchase_ticket']->business->city }}, {{ $data['purchase_ticket']->business->address }} {{ $data['purchase_ticket']->business->floor }} {{ $data['purchase_ticket']->business->door }}</p>
                <p class="ln_0_3"><strong>Sorszám:</strong> {{ $data['purchase_ticket']->ticket_number }}</p>
            </td>
            <td width="25%" align="right" valign="top" style="padding-top: 0;border: none;margin-right: 25px;">
                <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="125" height="63">
            </td>
            <td width="5%" align="right" valign="top" style="padding-top: 0;border: none;"></td>
        </tr>
      </table>

      <h2>
        @if($data['purchase_ticket']->is_storno)
          SZTORNÓ VÉTELI JEGY
        @else
          VÉTELI JEGY
        @endif
      </h2>

      <p>
        Amely létrejött egyrészről <strong>Zemef Kft.</strong> (6500 Baja, Dózsa György út 75., cgj.: 03-09-138127, adószám: 13192189-2-03), mint vásárló,másrészről <strong>Név:</strong> {{ $data['purchase_ticket']->customer->name }}, 
        <strong>Lakcím:</strong> {{ $data['purchase_ticket']->customer->addresses()->where('address_type', 'Lakcím')->first()->zip }} {{ $data['purchase_ticket']->customer->addresses()->where('address_type', 'Lakcím')->first()->city }}, {{ $data['purchase_ticket']->customer->addresses()->where('address_type', 'Lakcím')->first()->address }},
        <strong>Szem. ig. száma:</strong> {{ $data['purchase_ticket']->customer->identity_card_number }}, mint eladó között.
      </p>

      <p class="bold underline mt20">Vételi jegy tárgya:</p>

      @php
        $inventoryItems = $data['purchase_ticket']->inventoryItems;
        $categoryIds = $inventoryItems->map(function ($item) {
          return data_get($item->item_data, 'category_id');
        })->filter()->unique()->values();
        $karatIds = $inventoryItems->map(function ($item) {
          return data_get($item->item_data, 'karat_id');
        })->filter()->unique()->values();

        $categoryNames = \App\Category::whereIn('id', $categoryIds)->pluck('name', 'id');
        $karatNames = \App\Models\Karat::whereIn('karat_id', $karatIds)->pluck('name', 'karat_id');
      @endphp

      <table>
        <thead>
          <tr>
            <th>Kategória</th>
            <th>Megnevezés</th>
            <th>Leírás / Gyártási szám</th>
            <th>Karát</th>
            <th>Db</th>
            <th>Súly</th>
            <th>Vételár</th>
          </tr>
        </thead>
        <tbody>
          @foreach($inventoryItems as $item)
          @php
            $itemCategoryId = (int) data_get($item->item_data, 'category_id', 0);
            $itemKaratId = (int) data_get($item->item_data, 'karat_id', 0);
          @endphp
          <tr>
            <td>{{ $itemCategoryId ? ($categoryNames[$itemCategoryId] ?? '') : '' }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }} {{ $item->factory_number }}</td>
            <td>{{ $itemKaratId ? ($karatNames[$itemKaratId] ?? '') : '' }}</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="center">{{ $item->weight }}</td>
            <td class="center">{{ number_format($item->price * $data['purchase_ticket']->currency_value, 0, ',', ' ') }} {{ $data['purchase_ticket']->currency->sign }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div style="width: 100%;">
        <div style="float: left; width: 50%;">
          <strong>Kifizetés módja:</strong> Készpénz
        </div>
        <div style="float: right; width: 50%; text-align: right;">
          <strong>Vételár összesen:</strong>
          {{ number_format($inventoryItems->sum('price') * $data['purchase_ticket']->currency_value, 0, ',', ' ') }}
          {{ $data['purchase_ticket']->currency->sign }}
        </div>
      </div>

      <p><strong>Dátum:</strong> {{ $data['purchase_ticket']->business->city }}, {{ date('Y. m. d.', strtotime($data['purchase_ticket']->printed_at)) }}</p><br><br><br><br><br>

      <table class="signature">
        <tr>
          <td class="center" style="padding-bottom: 2px;">_________________________________________________</td>
          <td></td>
          <td class="center" style="padding-bottom: 2px;">_____________________________________________________</td>
        </tr>
        <tr>
          <td class="center">Eladó<br>{{ $data['purchase_ticket']->customer->name }}</td>
          <td></td>
          <td class="center">Vásárló<br>A Zemef Kft. nevében eljáró ügyintéző neve:<br> {{ $data['purchase_ticket']->employee->lastname }} {{ $data['purchase_ticket']->employee->firstname }}</td>
        </tr>
      </table>
    </div>

  @endfor
</div>

</body>
</html>
