<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizományosi Szerződés</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        h1, h2, p {
            margin: 0;
        }
        .underline {
            text-decoration: underline;
        }
        .bold {
            font-weight: bold;
        }
        .section {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }
        .checkbox {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 1px solid black;
            margin-right: 10px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>

    @php
        $receipt = $data['consignment_receipt'];
        $customer = $receipt->customer;
        $business = $receipt->business ?? ($data['business'] ?? null);
        $isBusinessContract = (int)($customer->is_company ?? 0) === 1;

        $customerAddress = $customer ? $customer->addresses->where('address_type', 'Lakcím')->first() : null;
        $customerAddressText = trim(implode(' ', array_filter([
            $customerAddress->zip ?? null,
            $customerAddress->city ?? null,
            $customerAddress->address ?? null,
        ])));

        $customerFullName = trim(implode(' ', array_filter([
            $customer->name ?? null,
            $customer->firstname ?? null,
        ])));
        $customerBirthData = trim(implode(' ', array_filter([
            $customer->birth_place ?? null,
            $customer->birth_date ?? null,
        ])));
    @endphp

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 20%;">
                    <img src="{{ asset('img/zemef_mini.png') }}" alt="Zemef logo" style="width: 140px;">
                </td>
                <td style="width: 80%; padding-left: 20px;">
                    <p>ZEMEF Kereskedelmi és Szolgáltató Korlátolt Felelősségű Társaság, e-mail: {{ $business->email ?? '' }}</p>
                    <p>székhely: 6500 Baja, Dózsa György út 75., <br>központi ügyintézés helye: 6500 Baja, Dózsa György út 75., adószám: 13192189-2-03</p>
                </td>
            </tr>
        </table>
    </div>
    
    <p><span class="bold">Szerződés megkötésének helye:</span> {{ $business->full_address ?? '' }} <br><span class="bold">Iktató szám:</span> {{ $receipt->ticket_number }}</p>

    <h1 style="text-align: center;">Bizományosi Szerződés</h1>

    <p>Amely létrejött egyrészről Zemef Kft. (6500 Baja, Dózsa György út 75., cgj.: 13-09-200448), mint bizományos, másrészről</p>

    <div class="section">
        @if(!$isBusinessContract)
            <p><span class="bold">Név:</span> {{ $customerFullName }}, <span class="bold">Születési név:</span> {{ $customer->name ?? '' }}, <span class="bold">Született:</span> {{ $customerBirthData }}</p>
            <p><span class="bold">Anyja neve:</span> {{ $customer->mothers_name ?? '' }}, <span class="bold">Lakik:</span> {{ $customerAddressText }}, <span class="bold">Emailcím:</span> {{ $customer->email ?? '' }}</p>
            <p><span class="bold">Telefonszám:</span> {{ $customer->phone ?? '' }} <span class="bold">mint megbízó</span></p>
        @else
            <p><span class="bold">Név:</span> {{ $customer->name ?? '' }}, <span class="bold">Születési név:</span> {{ $customer->name ?? '' }}, <span class="bold">Született:</span> {{ $customerBirthData }}</p>
            <p><span class="bold">Lakik:</span> {{ $customerAddressText }}, <span class="bold">Emailcím:</span> {{ $customer->email ?? '' }}</p>
            <p><span class="bold">Kapcsolattartó:</span> {{ $customerFullName }}, <span class="bold">Telefonszám:</span> {{ $customer->phone ?? '' }}</p>
            <p><span class="bold">Adószám:</span> {{ $customer->tax_number ?? '' }}, <span class="bold">Cégjegyzékszám:</span> {{ $customer->company_registration_number ?? '' }}</p>
            <p><span class="bold">Nyilvántartási szám:</span> {{ $customer->registration_number ?? '' }}, <span class="bold">Web:</span> {{ $customer->web_address ?? '' }}</p>
            <p><span class="bold">mint megbízó</span></p>
        @endif
        <p style="font-size:9px !important;">(jelen szerződés aláírásával a megbízó hozzájárul a személyes adatai kezeléséhez és a személyes iratainak másolásához a cél elérése érdekében).</p>
    </div>

    <h2>I. A szerződés tárgya</h2>
    <p>A Megbízó megbízza a Bizományost, hogy a saját nevében, de a Megbízó javára az alábbi ingóságot díjazás ellenében értékesítse.</p>

    <div class="section">
        <p><span class="bold">A bizományos által átvett ingóság jellemzői:</p><br>
        <p style="text-align:center;"><span class="bold">(arany ingóság esetén):</span></p>
        <p><span class="bold">megnevezés:</span> ............................................... <span class="bold">gramm súlyú:</span> ................................. <span class="bold">Karát szám:</span> .................................</p>
        <p><span class="bold">fémjel típus:</span> ............................................... </p>

        <p>az arany nem rendelkezik fémjellel vagy nem magyarországi fémjellel rendelkezik. Megbízó kéri a fémjeleztetést: {{ $data["consignment_receipt"]->is_hallmark_requested == 1 ? '<b>IGEN</b>' : '<b>NEM</b>' }}</p>
        @if($data["consignment_receipt"]->is_hallmark_requested == 1) <p>(a fémjeleztetés időtartama - 1 hónap - nem számít bele a szerződés időtartamába)</p> @endif
        <p><span class="bold">Egyedi azonosító jelek az ingóság tulajdonságai:</span></p>
    </div>

    <h2>II. Az értékesítés feltételei (a megfelelő változatot X-el jelölni szükséges)</h2>
    <div class="section">
        <p>Felek által megállapított legalacsonyabb értékesítési összeg (továbbiakban minimum eladási ár): __________________-Ft.</p>
        <p>A bizományos kötelezi magát, hogy értékesítés esetén a megállapított érték 80%-át megfizeti a megbízónak 2 munkanapon belül a vételi jegy alapján.</p>

        <p>A bizományos által a későbbiekben meghatározott értéken történő értékesítés esetén a megbízót __________-Ft illeti meg.</p>

        <p>Felek által megállapított minimum eladási ár: __________________-Ft. Bizományos, a sikeres értékesítés biztosítékaként, jelen szerződés aláírásával megbízónak átadja a minimum eladási ár ____%-át, azaz __________________-Ft-ot.</p>

        <p>A sikeres értékesítést követően a bizományos – email útján – értesíti a Megbízót. Felek ezt követően két munkanapon belül elszámolnak az átvett összeg és a minimum eladási ár közötti különbözeti összeggel. A minimum eladási ár feletti összeg bizományos haszna (bizományosi díja).</p>

        <p>Ha a szerződés határideje sikeres értékesítés nélkül zárul, akkor erről a tényről a bizományos értesíti megbízót. Az értesítést követő két napon belül felek elszámolnak egymással, melynek során bizományos köteles visszaadni megbízónak az értékesítésre átvett tárgyat, megbízó pedig köteles visszaadni a szerződés létrejöttekor bizományostól átvett pénzösszeget plusz annak 5%-kal növelt kamatát.</p>

        <p>Amennyiben két munkanapon belül nem kerül sor az elszámolásra, úgy a bizományos az értékesítésre átvett tárgyon tulajdont szerez a jelen szerződés során átadott pénzösszeg mint vételi ár ellenében.</p>

        <p>A szerződés határozott időtartamra jött létre a szerződés megkötésének dátumától számított __________ hónapra.</p>

        <p>A megbízó hozzájárul ahhoz, hogy a bizományos a futamidő végén __________-Ft értéken megvásárolja az ingóságot.</p>
    </div>


    <p style="font-size:8px !important;">A bizományos jogosult az ingóságot áthelyezni az érdekeltségébe tartozó telephelyekre értékesítés végett, illetve jogosult hirdetni elektronikus formában (weblap, Facebook, stb.).
    A jelen Szerződésben nem szabályozott kérdésekben a Polgári törvénykönyvről szóló 2013. évi V. törvény rendelkezései az irányadóak.A jelen Szerződés Felek kölcsönös elolvasás és értelmezés után, mint akaratukkal mindenben megegyezőt jóváhagyólag aláírtak.</p>

    <br><p>Kelt:  {{ date('Y-m-d', strtotime($data["consignment_receipt"]->created_at)) }}</p>
    <div class="signature" style="font-size:13px !important;">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center;">
                    _______________________<br>
                    <p>{{ $isBusinessContract ? ($customer->name ?? '') : $customerFullName }}<br><strong>Megbízó</strong></p>
                </td>
                <td style="text-align: center;">
                    <br>_______________________<br>
                    <p>{{ $receipt->employee->lastname." ".$receipt->employee->firstname }}<br><strong>Bizományos</strong><br>A Zemef Kft. nevében eljáró ügyintéző</p>
                </td>
            </tr>
        </table>
    </div>
    

</body>
</html>
