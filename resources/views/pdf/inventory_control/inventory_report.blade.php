<!DOCTYPE html>
<html lang="hu">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Leltári Jegyzőkönyv</title>
    <style>
        body 
        { 
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Leltári jegyzőkönyv</h1>
    <table width="100%">
        <tr>
            <td width="70%" valign="top">
                <p><u>Sorszám:</u> {{ $inventory_control->inventory_control_number }}</p>
                <p><u>Üzlet:</u> {{ $inventory_control->business?->name }}</p>
                <p><u>Raktár:</u> {{ $inventory_control->warehouse?->name }}</p>
                <p><u>Leltározás dátuma:</u> {{ $inventory_control->created_at?->format('Y-m-d') }}</p>
                <p><u>Jegyzőkönyv kiállításának dátuma:</u></p>
                <p><u>Leltár zárás dátuma:</u> {{ $inventory_control->is_closed == 1 ? $inventory_control->updated_at?->format('Y-m-d') : ""  }}</p>
                <p><u>Leltározást végző személy(ek):</u> {{ $inventory_control->responsible_user?->name." ,".$inventory_control->inspector_user?->name }}</p>
                <p><u>Jelen volt:</u> {{ $inventory_control->responsible_user?->name." ,".$inventory_control->inspector_user?->name }}</p>
                <br>
                <p><u>Leltár eredménye:</u></p>
            </td>
            <td width="30%" align="right" valign="top" style="padding-top: 0;">
                <img src="{{ asset('img/zemef_logo_biz.png') }}" alt="zemef_logo" width="250" height="125">
            </td>
        </tr>
    </table>

    @php
    $differences = $items->filter(function($item) {
        return $item->inventory_quantity != $item->book_quantity;
    });
@endphp

    <h2>Leltár állapot</h2>

    @if($differences->isEmpty())
        <p>Leltár teljes egyezést mutat, további teendő nincsen. Leltár zárható. Szép munka!</p>
    @else
        <p>A leltárban eltérés mutatkozik, továbbiakban még igényel vele való foglalkozást.</p>
        <p>Eltérést mutató termékek cikkszáma:</p>
        <ul>
            @foreach($differences as $item)
                <li>{{ $item->product_id }} - {{ $item->name }} ({{ $item->inventory_quantity - $item->book_quantity }})</li>
            @endforeach
        </ul>
        <p>Jegyzőkönyv a fent felsorolt termékekben eltérést mutat, mely kivizsgálásra kerül. Kivizsgálást követően új jegyzőkönyv készül a megállapításról.</p>
    @endif

    <h3>Megállapítás:</h3>
    <textarea name="conclusion" rows="10" cols="80" placeholder="Ide írható a megállapítás..."></textarea>

    <br><br>
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

    <br>
    <h3>Jegyzőkönyv kötelező mellékletei:</h3>
    <ul>
        <li>Leltár Eltérések Ív</li>
        <li>Leltár ív</li>
    </ul>


</body>
</html>
