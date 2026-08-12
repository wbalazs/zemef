<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Foglaló bizonylat</title>
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

    .cut-line {
      text-align: center;
      font-size: 12px;
      margin: 0;
    }

    .cut-line::before {
      content: "✂️————————————————————————————————————————————————————————————————————✂️";
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

    .bold {
      font-weight: bold;
    }

    .mt20 {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="wrapper">
  @for($i = 0; $i < 2; $i++)
    <div class="half">
      <table style="border: none;">
        <tr>
          <td width="70%" align="left" valign="top" style="padding-top: 0;border: none;">
            <p><strong>Szerződés megkötésének helye:</strong> 6500 Baja, Dózsa György út 75.</p>
            <p><strong>Sorszám:</strong> {{ 'BJ' . date('Ymd') . str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</p>
          </td>
          <td width="30%" align="right" valign="top" style="padding-top: 0;border: none;">
            <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="125">
          </td>
        </tr>
      </table>

      <h2>Átvételi elismervény foglalóról</h2>

      <p>
        Alulírott <strong>Zemef Kft.</strong> (6500 Baja, Dózsa György út 75.), mint eladó, mai napon átvettem<br>
        <strong>{{ $sale->customer_name }}</strong> vevőtől ({{ $sale->customer_address }}) foglalóként <strong>{{ number_format($sale->final_total_gross, 0, ',', ' ') }} Ft</strong>-ot<br>
        az üzletünkben, a következő termék(ek) megvásárlására:
      </p>

      <table>
        <thead>
          <tr>
            <th>Megnevezés</th>
            <th>Leírás</th>
            <th>Db</th>
            <th>Vételár</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->description }}</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="center">{{ number_format($item->price_gross, 0, ',', ' ') }} Ft</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <p class="mt20"><strong>Kifizetés módja:</strong> {{ strtoupper($sale->payment_method) === 'card' ? 'Bankkártya' : (strtoupper($sale->payment_method) === 'transfer' ? 'Átutalás' : 'Készpénz') }}</p>
      <p><strong>Vételár összesen:</strong> {{ number_format($sale->final_total_gross, 0, ',', ' ') }} Ft</p>

      <p class="mt20">
        A felek tudomásul veszik, hogy a foglalót a vételárba be kell számítani. A foglaló visszafizetésére vonatkozó szabályokat ismerik és elfogadják.
      </p>

      <p><strong>Dátum:</strong> Baja, {{ \Carbon\Carbon::parse($sale->created_at)->format('Y. m. d.') }}</p><br><br>

      <table class="signature" width="100%">
        <tr>
          <td class="center">___________________________________</td>
          <td></td>
          <td class="center">___________________________________</td>
        </tr>
        <tr>
          <td class="center">Eladó<br>Zemef Kft.<br>Ügyintéző neve: _____________________</td>
          <td></td>
          <td class="center">Vevő<br>{{ $sale->customer_name }}</td>
        </tr>
      </table>
    </div>

    @if($i == 0)
      <div class="cut-line"></div>
    @endif
  @endfor
</div>

</body>
</html>
