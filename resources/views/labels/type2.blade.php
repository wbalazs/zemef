<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: 64mm 10mm;
            margin: 0;
        }

        html,
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7px;
            margin: 0;
            padding: 0;
            width: 64mm;
            height: 10mm;
            overflow: hidden;
        }

        .label {
            width: 64mm;
            height: 10mm;
            table-layout: fixed;
            border-collapse: collapse;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .side {
            padding: 0 1mm;
            box-sizing: border-box;
            vertical-align: top;
            line-height: 1;
        }

        .side-left {
            width: 36%;
        }

        .side-middle {
            width: 52%;
        }

        .side-right {
            width: 12%;
        }

        .product-name {
            font-weight: bold;
            font-size: 8px;
            padding: 0;
            letter-spacing: 0.2px;
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
            overflow: hidden;
            display: block;
            max-width: 100%;
        }

        .hallmark,
        .details,
        .price,
        .price2 {
            margin: 0;
            padding: 0;
            font-size: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .price {
            text-decoration: line-through;
        }

        .price2 {
            background: #000;
            color: #fff;
            display: inline-block;
            padding: 0 1px;
        }

        .ml-2 {
            margin-left: 2px;
        }

        .barcode {
            margin-top: 0.3mm;
            line-height: 0;
        }

        .barcode img {
            width: 100%;
            height: 4mm;
        }
    </style>
</head>
<body>
    <table class="label">
        <tr>
            <td class="side side-left">
                <div class="product-name ml-2">{{ $product_name }}@if(!empty($size)) M{{ $size }}@endif {{ $hallmarks }}</div>
                
                <div class="price ml-2">{{ $price }}</div>
                <div class="price2 ml-2">{{ $price }}</div>
            </td>
            <td class="side side-middle">
                <div class="barcode"> 
                    <img src="data:image/png;base64,{{ $barcode }}" alt="barcode" />
                </div>
                <div class="details">{{ $vegyjel }} {{ $inventory_id }} {{ $szallito }} {{ $karat }} Kt. {{ $weight }}</div>
                
            </td>
            <td class="side side-right">
            </td>
        </tr>
    </table>
</body>
</html>
