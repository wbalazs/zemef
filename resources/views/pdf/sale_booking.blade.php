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
      margin: 8px 0;
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
      <table style="border: none;margin-top: -10px;">
        <tr>
          <td width="70%" align="left" valign="top" style="padding-top: 0;border: none;">
            <p><strong>Szerződés megkötésének helye:</strong> 6500 Baja, Dózsa György út 75.</p>
            <p><strong>Sorszám:</strong> {{ 'FOG/' . date('Ymd') ."/". str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</p>
          </td>
          <td width="30%" align="right" valign="top" style="padding-top: 10;border: none;">
            <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="125">
          </td>
        </tr>
      </table>

      <h2>Átvételi elismervény foglalóról</h2>

      <p>
        Alulírott <strong>Zemef Kft.</strong> (6500 Baja, Dózsa György út 75.), mint <b>eladó</b>, mai napon átvettem,{{ $sale->customer_name }} mint vevő ({{ $sale->customer_address }}) foglalóként <strong>{{ number_format($sale->booking_price, 0, ',', ' ') }} Ft</strong>-ot
        az üzletünkben, a felsorolt termék(ek) megvásárlására.A felek megállapodnak abban, hogy a teljes vételár megfizetését legkésőbb {{ $sale->booking_max_date }} napjáig, a <b>vevő</b> teljesíti az <b>eladónak</b>.

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

      <div style="width: 100%;">
        <div style="float: left; width: 50%;">
          <strong>Kifizetés módja:</strong> {{ strtoupper($sale->payment_method) === 'card' ? 'Bankkártya' : (strtoupper($sale->payment_method) === 'transfer' ? 'Átutalás' : 'Készpénz') }}
        </div>
        <div style="float: right; width: 50%; text-align: right;">
          <strong>Vételár összesen:</strong>
          {{ number_format($sale->final_total_gross, 0, ',', ' ') }} Ft
        </div>
      </div>

      <p class="mt20">
        A felek tudomásul veszik, hogy a foglalót a vételárba be kell számítani. A felek kijelentik, hogy a foglaló mibenlétét ismerik, 
        az átvételével kapcsolatos jogkövetkezményekkel tisztában vannak. Amennyiben a szerződéskötés a tervezett határidőre a vevő 
        hibájából nem jön létre, úgy a foglalót elveszíti, ha az <b>eladó</b> hibájából nem jön létre, úgy az <b>eladó</b> a foglalót kétszeresen köteles visszafizetni.
      </p>

      <p>A felek a jelen elismervényt, mint akaratukkal mindenben megegyezőt, helybenhagyólag írták alá.</p>

      <p><strong>Dátum:</strong> Baja, {{ \Carbon\Carbon::parse($sale->created_at)->format('Y. m. d.') }}</p><br>

      <table class="signature" width="100%">
        <tr>
          <td class="center">___________________________________</td>
          <td></td>
          <td class="center">___________________________________</td>
        </tr>
        <tr>
          <td class="center">Eladó: Zemef Kft.<br>Ügyintéző neve:{{ $sale->employee_name }}</td>
          <td></td>
          <td class="center">Vevő: {{ $sale->customer_name }}</td>
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
