<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nyugta</title>
</head>
<body>
    <h2>Nyugta</h2>
    <p><strong>Dátum:</strong> {{ $sale->created_at }}</p>

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Termék</th>
                <th>Mennyiség</th>
                <th>Nettó ár</th>
                <th>ÁFA</th>
                <th>Bruttó ár</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }} db</td>
                <td>{{ number_format($item->price_net, 0, ',', ' ') }} Ft</td>
                <td>{{ $item->vat }}%</td>
                <td>{{ number_format($item->price_gross, 0, ',', ' ') }} Ft</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $discountPercent = $sale->discount;
        $discountGross = round($sale->total_gross * ($discountPercent / 100));
        $couponGross = round($sale->coupon * 1.27);
        $giftcardGross = round($sale->giftcard * 1.27);
        $loyaltyGross = round($sale->loyalty * 1.27);
        $totalDiscountGross = $discountGross + $couponGross + $giftcardGross + $loyaltyGross;
    @endphp
    <br>
    <p><strong>Összesen bruttó:</strong> {{ number_format($sale->total_gross, 0, ',', ' ') }} Ft</p>

    <p><strong>Ügyfélkedvezmény ({{ $discountPercent }}%):</strong> -{{ number_format($discountGross, 0, ',', ' ') }} Ft</p>
    <p><strong>Kupon:</strong> -{{ number_format($couponGross, 0, ',', ' ') }} Ft</p>
    <p><strong>Ajándékkártya:</strong> -{{ number_format($giftcardGross, 0, ',', ' ') }} Ft</p>
    <p><strong>Hűségkártya:</strong> -{{ number_format($loyaltyGross, 0, ',', ' ') }} Ft</p>
    <p><strong>Kedvezmények összesen:</strong> {{ number_format($totalDiscountGross, 0, ',', ' ') }} Ft</p>

    <p><strong>Fizetendő végösszeg:</strong> {{ number_format($sale->final_total_gross, 0, ',', ' ') }} Ft</p>
</body>
</html>
