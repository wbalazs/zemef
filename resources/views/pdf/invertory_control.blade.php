<!DOCTYPE html>
<html lang="hu">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leltárív</title>
    <style>
        body 
        { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Leltárív</h1>
    <p>Üzlet: {{ $inventory_control->business->name }}</p>
    <p>Raktár: {{ $inventory_control->warehouse->name }}</p>
    <p>Leltározás dátuma: {{ $inventory_control->created_at->format('Y-m-d') }}</p>
    <table border="1">
        <thead>
            <tr>
                <th>#</th>
                <th>Termék neve</th>
                <th>Márka</th>
                <th>Leírás</th>
                <th>Könyv szerinti mennyiség</th>
                <th>Leltározott mennyiség</th>
                <th>Könyv szerinti érték</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->product_id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->book_quantity }}</td>
                <td>{{ $item->inventory_quantity }}</td>
                <td>{{ number_format($item->book_price, 0, ',', ' ') }} Ft</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 style="text-align:center;">Összesítés</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Összesen</th>
                <th>Könyv szerinti mennyiség</th>
                <th>Leltározott mennyiség</th>
                <th>Könyv szerinti érték</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td>{{ $total_book_quantity }}</td>
                <td>{{ $total_inventory_quantity }}</td>
                <td>{{ number_format($total_book_price, 0, ',', ' ') }} Ft</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
