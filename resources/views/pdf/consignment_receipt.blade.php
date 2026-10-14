<!DOCTYPE html>
<html lang="hu">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bizományosi Szerződés</title>
    <style type="text/css">
        @page {
            margin: 10px 25px 10px 25px;
            size: A4 portrait;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.8pt;
            line-height: 1.2;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        p {
            margin: 0 0 2px 0;
            padding: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-justify {
            text-align: justify;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .italic {
            font-style: italic;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: bold;
            text-align: center;
            margin: 4px 0;
        }
        .section-title {
            font-size: 8.8pt;
            font-weight: bold;
            text-align: center;
            margin: 4px 0 2px 0;
        }
        .bordered-box {
            border: 1px solid #000000;
            padding: 3px 6px;
            margin-bottom: 4px;
            text-align: justify;
        }
        .checkbox-img {
            display: inline-block;
            vertical-align: middle;
            width: 11px;
            height: 11px;
            margin-right: 3px;
        }
        .small-text {
            font-size: 7pt;
            line-height: 1.15;
        }
        .very-small-text {
            font-size: 6.8pt;
            line-height: 1.15;
        }
        .signature-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            font-size: 8pt;
        }
    </style>
</head>
<body>

@php
    $receipt = $data['consignment_receipt'];
    $customer = $receipt->customer;
    $business = $receipt->business ?? ($data['business'] ?? null);
    $isBusinessContract = (int)($customer->is_company ?? ($receipt->header_data['customer_is_company'] ?? 0)) === 1;

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

    $businessAddress = $business ? trim(implode(' ', array_filter([
        $business->zip ?? null,
        $business->city ?? null,
        $business->address ?? null,
    ]))) : '';
    if (empty($businessAddress)) {
        $businessAddress = $business->full_address ?? '7400 Kaposvár, Dózsa György u. 2.';
    }

    $businessEmail = $business->email ?? 'zemefkaposvar@gmail.com';
    if (empty($businessEmail)) {
        $businessEmail = 'zemefkaposvar@gmail.com';
    }

    // Image paths (fallback to base64 or public path)
    $img1Path = public_path('img/doc_images/image1.png');
    if (!file_exists($img1Path)) {
        $img1Path = storage_path('app/public/document_storage/images/image1.png');
    }
    $image1Src = file_exists($img1Path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($img1Path)) : asset('img/zemef_mini.png');

    $img2Path = public_path('img/doc_images/image2.png');
    if (!file_exists($img2Path)) {
        $img2Path = storage_path('app/public/document_storage/images/image2.png');
    }
    $image2Src = file_exists($img2Path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($img2Path)) : '';

    $img3Path = public_path('img/doc_images/image3.png');
    if (!file_exists($img3Path)) {
        $img3Path = storage_path('app/public/document_storage/images/image3.png');
    }
    $image3Src = file_exists($img3Path) ? 'data:image/png;base64,' . base64_encode(file_get_contents($img3Path)) : '';

    // Collect items info
    $items = $receipt->inventoryItems;
    if ($items->isEmpty() && method_exists($receipt, 'consignment_receipt_items')) {
        $items = $receipt->consignment_receipt_items;
    }

    $karatMap = \App\Models\Karat::pluck('name', 'karat_id')->toArray();
    $productTypeMap = \App\Models\ProductType::pluck('name', 'product_type_id')->toArray();

    $isHallmarkRequested = ((int)($receipt->is_hallmark_requested ?? 0) === 1) || ((int)($receipt->header_data['hallmarks'] ?? 0) === 1);

    // Duration in months
    $durationMonths = '';
    if (!empty($receipt->expire_date) && !empty($receipt->created_at)) {
        try {
            $cDate = \Carbon\Carbon::parse($receipt->created_at);
            $eDate = \Carbon\Carbon::parse($receipt->expire_date);
            $diff = $cDate->diffInMonths($eDate);
            if ($diff > 0) {
                $durationMonths = (string) $diff;
            }
        } catch (\Exception $e) {}
    }
@endphp

    <table class="header-table">
        <tr>
            
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 6px; font-size: 8.5pt;">
        <tr>
            <td style="width: 60%;">
                <span class="bold">Szerződés megkötésének helye:</span> {{ $businessAddress }}
            </td>
            <td style="width: 40%; text-align: right;">
                <span class="bold">Iktató szám:</span> {{ $receipt->header_data['filenumber'] ?? $receipt->ticket_number }}
            </td>
        </tr>
    </table>

    <div class="doc-title">Bizományosi Szerződés</div>

    <p style="text-align: justify; margin-bottom: 4px;">
        Amely létrejött egyrészről <span class="bold">Zemef Kft.</span> (2161 Csomád, Levente utca 14. A. ép., cgj.: 13-09-200448), mint <span class="bold">bizományos</span>, másrészről
    </p>

    <div class="bordered-box">
        @if(!$isBusinessContract)
            <p>
                <span class="bold">Név:</span> {{ $customerFullName ?: '__________________________' }},
                <span class="bold">Születési név:</span> {{ ($customer->birth_name ?? $customer->name) ?: '_______________________' }},
                <span class="bold">Született:</span> {{ $customerBirthData ?: '______________________' }},
            </p>
            <p>
                <span class="bold">Anyja neve:</span> {{ $customer->mothers_name ?: '_________________' }},
                <span class="bold">Lakik:</span> {{ $customerAddressText ?: '___________________________________' }},
                <span class="bold">Emailcím:</span> {{ $customer->email ?: '_____________' }}
            </p>
            <p>
                <span class="bold">Telefonszám:</span> {{ $customer->phone ?: '________________________' }} <span class="bold">mint megbízó</span>
            </p>
        @else
            <p>
                <span class="bold">Név:</span> {{ $customer->name ?: '__________________________' }},
                <span class="bold">Születési név:</span> {{ $customer->name ?: '_______________________' }},
                <span class="bold">Született:</span> {{ $customerBirthData ?: '______________________' }},
            </p>
            <p>
                <span class="bold">Lakik:</span> {{ $customerAddressText ?: '___________________________________' }},
                <span class="bold">Emailcím:</span> {{ $customer->email ?: '_____________' }}
            </p>
            <p>
                <span class="bold">Kapcsolattartó:</span> {{ $customerFullName }},
                <span class="bold">Telefonszám:</span> {{ $customer->phone ?: '________________________' }},
                <span class="bold">Adószám:</span> {{ $customer->tax_number ?? '' }},
                <span class="bold">Cégjegyzékszám:</span> {{ $customer->company_registration_number ?? '' }}
                <span class="bold">mint megbízó</span>
            </p>
        @endif
        <p class="small-text" style="margin-top: 2px;">
            (jelen szerződés aláírásával a megbízó hozzájárul a személyes adatai kezeléséhez és a személyes iratainak másolásához a cél elérése érdekében).
        </p>
    </div>

    <div class="section-title">I. A szerződés tárgya</div>
    <p style="text-align: justify; margin-bottom: 4px;">
        A Megbízó megbízza a Bizományost, hogy a saját nevében, de a Megbízó javára az alábbi ingóságot díjazás ellenében értékesítse.
    </p>

    @if($items->isNotEmpty())
        @foreach($items as $itm)
            @php
                $itemData = is_array($itm->item_data) ? $itm->item_data : json_decode($itm->item_data ?? '[]', true);
                if (!is_array($itemData)) {
                    $itemData = [];
                }
                $termekTipusId = $itm->termek_tipus_id ?? ($itemData['termek_tipus_id'] ?? null);
                $itmName = ($termekTipusId && isset($productTypeMap[$termekTipusId]))
                    ? $productTypeMap[$termekTipusId]
                    : ($itm->name ?: ($itm->product->name ?? ''));

                $itmKaratId = $itemData['karat_id'] ?? ($itemData['karats'][0] ?? null);
                $itmKaratName = ($itmKaratId && isset($karatMap[$itmKaratId]))
                    ? $karatMap[$itmKaratId]
                    : ($itm->karat->name ?? ($itemData['karat_name'] ?? ''));

                $itmWeightText = !empty($itm->weight)
                    ? number_format((float)$itm->weight, 2, ',', ' ') . ' g'
                    : '';

                $itmIsHallmark = ((int)($itemData['hallmarks'] ?? 0) === 1)
                    || ((int)($receipt->is_hallmark_requested ?? 0) === 1)
                    || ((int)($receipt->header_data['hallmarks'] ?? 0) === 1);
            @endphp
            <div class="bordered-box">
                <p><span class="bold">A bizományos által átvett ingóság jellemzői:</span></p>
                <p class="text-center italic" style="margin: 2px 0;"><span class="bold">(arany ingóság esetén:)</span></p>
                <p style="margin-bottom: 4px;">
                    megnevezés: {{ $itmName ?: '............................................' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    gramm súlyú: {{ $itmWeightText ?: '.................................' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Karát szám: {{ $itmKaratName ?: '................................' }}
                </p>
                <p style="margin-bottom: 4px;">
                    fémjel típus: ............................................
                </p>
                <p style="margin-bottom: 2px;">
                    <img src="{{ $itmIsHallmark ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]">
                    az arany nem rendelkezik fémjellel vagy nem magyarországi fémjellel rendelkezik. Megbízó kéri a fémjeleztetést <span class="bold">{{ $itmIsHallmark ? 'IGEN' : 'IGEN / NEM' }}</span>
                </p>
                <p class="very-small-text bold" style="padding-left: 18px; margin-bottom: 4px;">
                    (amennyiben IGEN kéri a fémjeleztetést, úgy annak időtartama - 1 hónap - nem számít bele a szerződés időtartamába)
                </p>
                <p><span class="bold">Egyedi azonosító jelek, az ingóság tulajdonságai:</span> {{ $itm->description }}</p>
            </div>
        @endforeach
    @else
        <div class="bordered-box">
            <p><span class="bold">A bizományos által átvett ingóság jellemzői:</span></p>
            <p class="text-center italic" style="margin: 2px 0;"><span class="bold">(arany ingóság esetén:)</span></p>
            <p style="margin-bottom: 4px;">
                megnevezés: ............................................&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                gramm súlyú: .................................&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Karát szám: ................................
            </p>
            <p style="margin-bottom: 4px;">
                fémjel típus: ............................................
            </p>
            <p style="margin-bottom: 2px;">
                <img src="{{ $isHallmarkRequested ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]">
                az arany nem rendelkezik fémjellel vagy nem magyarországi fémjellel rendelkezik. Megbízó kéri a fémjeleztetést <span class="bold">{{ $isHallmarkRequested ? 'IGEN' : 'IGEN / NEM' }}</span>
            </p>
            <p class="very-small-text bold" style="padding-left: 18px; margin-bottom: 4px;">
                (amennyiben IGEN kéri a fémjeleztetést, úgy annak időtartama - 1 hónap - nem számít bele a szerződés időtartamába)
            </p>
            <p><span class="bold">Egyedi azonosító jelek, az ingóság tulajdonságai:</span></p>
        </div>
    @endif

    <div class="section-title">II. Az értékesítés feltételei <span style="font-weight: normal; font-size: 8pt;">(a megfelelő változatot X-el jelölni szükséges)</span></div>

    <div class="bordered-box">
        <p style="margin-bottom: 5px;">
            <img src="{{ !empty($receipt->header_data['sales_condition_1']) ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]">
            Felek által megállapított legalacsonyabb értékesítési összeg (<span class="italic">továbbiakban minimum eladási ár</span>): {{ !empty($receipt->header_data['sales_condition_1_min_price']) ? number_format((float)$receipt->header_data['sales_condition_1_min_price'], 0, ',', ' ') : '__________________' }},-Ft,. A bizományos kötelezi magát, hogy értékesítés esetén a megállapított érték 80%-át megfizeti a megbízónak 2 munkanapon belül a vételi jegy alapján.
        </p>
        
        <p style="margin-bottom: 5px;">
            <img src="{{ !empty($receipt->header_data['sales_condition_2']) ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]">
            A bizományos által a későbbiekben meghatározott értéken történő értékesítés. Értékesítés esetén a megbízót {{ !empty($receipt->header_data['sales_condition_2_amount']) ? number_format((float)$receipt->header_data['sales_condition_2_amount'], 0, ',', ' ') : '__________' }},-Ft, illeti meg.
        </p>

        <p style="margin-bottom: 5px;">
            <img src="{{ !empty($receipt->header_data['sales_condition_3']) ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]">
            Felek által megállapított minimum eladási ár: {{ !empty($receipt->header_data['sales_condition_3_min_price']) ? number_format((float)$receipt->header_data['sales_condition_3_min_price'], 0, ',', ' ') : '__________________' }}.-Ft. Bizományos – a sikeres értékesítés biztosítékaként - jelen szerződés aláírásával megbízónak átadja a minimum eladási ár {{ !empty($receipt->header_data['sales_condition_3_percent']) ? $receipt->header_data['sales_condition_3_percent'] : '____' }}%-át, azaz {{ !empty($receipt->header_data['sales_condition_3_amount']) ? number_format((float)$receipt->header_data['sales_condition_3_amount'], 0, ',', ' ') : '__________________' }}Ft-ot. A sikeres értékesítést követően bizományos – email útján - Megbízót értesíti. Felek ezt követően két munkanapon belül elszámolnak az átvett összeg és a minimum eladási ár közötti különbözeti összeggel. A minimum eladási ár feletti összeg bizományos haszna (bizományosi díja). Ha a szerződés határideje sikeres értékesítés nélkül zárul, akkor erről a tényről a fenti módon bizományos értesíti megbízót. Az értesítést követő két napon belül felek elszámolnak egymással, melynek során bizományos köteles visszaadni megbízónak – átadás-átvételi jegyzőkönyv felvétele mellett – az értékesítésre átvett tárgyat, megbízó pedig köteles visszaadni a szerződés létrejöttekor bizományostól átvett pénzösszeget plusz annak 5%-kal növelt kamatát. Amennyiben két munkanapon belül nem kerül sor az elszámolásra, úgy bizományos az értékesítésre átvett tárgyon tulajdont szerez a jelen szerződés során átadott pénzösszeg, mint vételi ár ellenében. Ez esetben megbízó bizományossal szemben semmilyen további követeléssel nem élhet.
        </p>

        <p style="margin-bottom: 4px;">
            A szerződés határozott időtartamra jött létre a szerződés megkötésének dátumától számított {{ !empty($receipt->header_data['sales_condition_4_months']) ? $receipt->header_data['sales_condition_4_months'] : ($durationMonths ?: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') }} <img src="{{ !empty($receipt->header_data['sales_condition_4']) ? $image2Src : $image3Src }}" class="checkbox-img" alt="[]"> hónap.
        </p>

        <p>
            A megbízó hozzájárul ahhoz, hogy a bizományos a futamidő végén {{ !empty($receipt->header_data['sales_condition_4_buyout_price']) ? number_format((float)$receipt->header_data['sales_condition_4_buyout_price'], 0, ',', ' ') : '______________' }},-Ft, értéken megvásárolja az ingóságot.
        </p>
    </div>

    <p class="very-small-text text-justify" style="margin-bottom: 8px;">
        A bizományos jogosult az ingóságot áthelyezni az érdekeltségébe tartozó telephelyekre értékesítés végett, illetve jogosult hirdetni elektronikus formában (weblap, facebook, stb.) A jelen Szerződésben nem szabályozott kérdésekben a Polgári törvénykönyvről szóló 2013. évi V. törvény rendelkezései az irányadóak. A jelen Szerződés Felek kölcsönös elolvasás és értelmezés után, mint akaratukkal mindenben megegyezőt jóváhagyólag aláírtak.
    </p>

    <p style="font-size: 8.5pt; margin-bottom: 12px;">
        <span class="bold">Kelt:</span> {{ date('Y-m-d', strtotime($receipt->created_at ?? now())) }}
    </p>

    <table class="signature-table">
        <tr>
            <td>
                ……………………………………………<br>
                <span class="bold">Megbízó</span><br>
                <span style="font-size: 8pt;">{{ $isBusinessContract ? ($customer->name ?? '') : $customerFullName }}</span>
            </td>
            <td>
                ……………………………………………<br>
                <span class="bold">Bizományos</span><br>
                A Zemef Kft. nevében eljáró ügyintéző<br>
                <span style="font-size: 8pt;">{{ trim(($receipt->employee->lastname ?? '') . ' ' . ($receipt->employee->firstname ?? '')) }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
