<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  <style type="text/css">
    body { 
      font-family: DejaVu Sans, sans-serif; 
      margin: 0;
      padding: 0;
    }
    h2{
        font-weight: normal;
        text-align: center;

    }
    table{
        margin-bottom: 30px;
        width: 100%;
        font-size: 12px;
    }
  </style>
  
</head>
<body>
  <div class="container">
    <div class="body">
        <h2>Kiküldetési rendelvény</h2>

        <table border="1" cellspacing="0" cellpadding="5">
            <tr>
                <td><strong>Cég neve:</strong></td>
                <td>Zemef Kft.</td>
                <td><strong>Munkavállaló:</strong></td>
                <td>{{ Auth::user()->employee->lastname . ' ' . Auth::user()->employee->firstname }}</td>
            </tr>
            <tr>
                <td><strong>Időszak:</strong></td>
                <td>{{ $data['year'] . '. ' . $data['month'] }}</td>
                <td><strong>Lakcím:</strong></td>
                <td>{{ Auth::user()->employee->home_zip . ' ' . Auth::user()->employee->home_city . ', ' . Auth::user()->employee->home_address }} @if(Auth::user()->employee->home_floor) {{ Auth::user()->employee->home_floor }} @endif @if(Auth::user()->employee->home_door) {{ Auth::user()->employee->home_door }} @endif</td>
            </tr>
            <tr>
                <td><strong>Forgalmi rendszám:</strong></td>
                <td>{{ Auth::user()->employee->vehicle->plate_number }}</td>
                <td><strong>Szül. hely, idő:</strong></td>
                <td>{{ Auth::user()->employee->birth_place . ', ' . Auth::user()->employee->birth_date }}</td>
            </tr>
            <tr>
                <td><strong>Gépjármű típusa:</strong></td>
                <td>{{ Auth::user()->employee->vehicle->name }}</td>
                <td><strong>Adóazonosító jel:</strong></td>
                <td>{{ Auth::user()->employee->tax_number }}</td>
            </tr>
        </table>


        <table border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>Időpont</th>
                    <th>Honnan - hová</th>
                    <th>Utazás célja</th>
                    <th>Ügyfél</th>
                    <th>Távolság (km)</th>
                    <th>Megjegyzés</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['postings'] as $posting)
                <tr>
                    <td>{{ $posting->posting_date }}</td>
                    <td>{{ $posting->from_to }}</td>
                    <td>{{ $posting->purpose }}</td>
                    <td>{{ $posting->customer->name }}</td>
                    <td>{{ $posting->distance }}</td>
                    <td>{{ $posting->comment }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5"><strong>Összesen:</strong></td>
                    <td><strong>{{ $data['total_km'] }}</strong></td>
                </tr>
            </tbody>
            
        </table>


        <table border="1" cellspacing="0" cellpadding="5">
            <tr>
                <td><strong>NAV üzemanyagár:</strong></td>
                <td align="right">{{ $data['fuel_price'] }} Ft</td>
            </tr>
            <tr>
                <td><strong>NAV alapnorma:</strong></td>
                <td align="right">{{ $data['nav_standard_consumption'] }} Ft</td>
            </tr>
            <tr>
                <td><strong>NAV amortizációs költség:</strong></td>
                <td align="right">{{ $data['nav_standard']->amort }} Ft/km</td>
            </tr>
            <tr>
                <td><strong>Üzemanyag ktg.:</strong></td>
                <td align="right">{{ number_format($data['total_fuel_price'], 0, ',', ' ') }} Ft</td>
            </tr>
            <tr>
                <td><strong>Amortizációs ktg.:</strong></td>
                <td align="right">{{ number_format($data['total_amort_price'], 0, ',', ' ') }} Ft</td>
            </tr>
            <tr>
                <td><strong>Összesen:</strong></td>
                <td align="right">{{ number_format($data['total_price'], 0, ',', ' ') }} Ft</td>
            </tr>
        </table>

    </div>
  </div>
</body>
</html>
