<!DOCTYPE html>
<html lang="hu">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leltár Eltérések</title>
    <style>
        body 
        { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        .uptable, .uptable th, .uptable td {
            border: none;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #cccccc;
        }
        .grey {
            background-color: #cccccc;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Leltár Eltérések ív</h1>
    <table width="100%" class="uptable">
        <tr>
            <td width="70%" valign="top">
                <p><u>Sorszám:</u> {{ $inventory_control->inventory_control_number }}</p>
                <p><u>Üzlet:</u> {{ $inventory_control->business->name }}</p>
                <p><u>Raktár:</u> {{ $inventory_control->warehouse->name }}</p>
                <p><u>Leltározás dátuma:</u> {{ $inventory_control->created_at->format('Y-m-d') }}</p>
                <p><u>Leltár zárás dátuma:</u> {{ $inventory_control->is_closed == 1 ? $inventory_control->updated_at->format('Y-m-d') : ""  }}</p>
                <p><u>Leltározást végző személy(ek):</u> {{ $inventory_control->responsible_user->name." ,".$inventory_control->inspector_user->name }}</p>
                <p><u>Jelen volt:</u> {{ $inventory_control->responsible_user->name." ,".$inventory_control->inspector_user->name }}</p>
            </td>
            <td width="30%" align="right" valign="top" style="padding-top: 0;">
                <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="250" height="125">
            </td>
        </tr>
    </table>
    
    <h2>Eltérések:</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cikkszám</th>
                <th>Termék neve</th>
                <th>Márka</th>
                <th>Megnevezés</th>
                <th>Súly</th>
                <th>Könyv szerinti mennyiség</th>
                <th>Leltározott mennyiség</th>
                <th>Különbözet</th>
                <th>Érték</th>
                <th>Különbözet értékben</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->product_id }}</td>
                <td>{{ $item->product?->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->product?->weight }}</td>
                <td class="grey">{{ $item->book_quantity }}</td>
                <td class="grey">{{ $item->inventory_quantity }}</td>
                <td class="grey">{{ $item->inventory_quantity - $item->book_quantity }}</td>
                <td>{{ number_format($item->book_price, 0, ',', ' ') }} Ft</td>
                <td>{{ number_format(($item->inventory_quantity - $item->book_quantity) * $item->book_price, 0, ',', ' ') }} Ft</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br><br><br><br>
    <table width="100%" class="uptable">
        <tr>
            <td width="50%" align="center" style="text-align: center !important;">
                <hr style="width: 80%; border: 1px solid black;">
                <p>Leltározást végző személy(<u>ek</u>) neve</p>
            </td>
            <td width="50%" align="center" style="text-align: center !important;">
                <hr style="width: 80%; border: 1px solid black;">
                <p>Jelen lévő személy(<u>ek</u>) neve</p>
            </td>
        </tr>
    </table>
</body>
</html>
