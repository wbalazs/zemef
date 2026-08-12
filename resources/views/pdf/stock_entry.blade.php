<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">


  <style type="text/css">
    body { 
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    position: relative;
    }

    

    h2{
        font-weight: normal;
        text-align: center;

    }

    h3{
        font-weight: normal;
    }
    table{
        margin-bottom: 30px;
        width: 100%;
        font-size: 12px;
    }
    table, th, td {
        border: 1px solid #ccc;
    }
    th, td {
        padding: 6px;
        text-align: left;
        vertical-align: top;
    }
    td[align="center"] {
        text-align: center;
    }
    .date{
        float: left;
    }
    .serial{
        float: right;
    }
    .fs10{
        font-size: 10px;
    }
    .fs11{
        font-size: 11px;
    }
    .fs12{
        font-size: 12px;
    }
    .fs13{
        font-size: 13px;
    }
    .fs16{
        font-size: 16px;
    }
    .fs28{
        font-size: 28px;
    }
    .mt50{
        margin-top: 50px;
    }
    p {
        line-height: 0.5;
        margin-bottom: 1px;
    }
  </style>
  
</head>
<body>
  <div class="container">
    <div class="body">

        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="text-align:right;">
                    <span class="fs13">SORSZÁM: {{ $data['stock_entry']->ticket_number }}</span><br>
                    @php
                        if($data['stock_entry']->is_storno){
                            echo '<span class="fs16">SZTORNÓZOTT ÉKSZER VÁSÁRLÁS: '.$data['stock_entry']->relatedticketnumber.'</span><br>';
                        }
                    @endphp
                    <!--<span class="fs16">DÁTUM: {{ date('Y. m. d.', strtotime($data['stock_entry']->printed_at)) }}</span>-->
                </td>
            </tr>
        </table>
        

        <table width="100%">
            <tr>
                <td width="34%" align="right" valign="top" style="padding-top: 0;">
                    <img src="{{ asset('img/zemef_logo_biz_ekszer_zalog_bef.png') }}" alt="zemef_logo" width="200" height="84">
                </td>
                <td width="46%" align="left" valign="middle" style="text-align:left;">
                    <br>
                    @php
                        if($data['stock_entry']->is_storno){
                            echo '<div style="font-size:30px;margin-left:14px;margin-top:10px;">Sztornó Ékszer Vásárlás</div><br>';
                        }else{
                            echo '<div style="font-size:30px;margin-left:14px;margin-top:10px;">Ékszer Vásárlás</div><br>';
                        }  
                    @endphp
                </td>
                <td width="20%" valign="top">

                </td>
            </tr>
        </table>
        
        <p>Eladó neve: {{ $data['stock_entry']->customer->name }}</p>
        <p>Címe/székhelye: {{ $data['stock_entry']->customer->addresses()->where('address_type', 'Lakcím')->first()->zip . ' ' . $data['stock_entry']->customer->addresses()->where('address_type', 'Lakcím')->first()->city . ', ' . $data['stock_entry']->customer->addresses()->where('address_type', 'Lakcím')->first()->address }}</p>
        <p>Adószáma: {{ $data['stock_entry']->customer->tax_number }}</p>
        <p>Cégjegyzékszáma: {{ $data['stock_entry']->customer->company_registration_number }}</p>
        <br>
        <p>Számla sorszáma: {{ $data['stock_entry']->invoice_number }}</p>
        <p>Hozzátartozó törtarany/ezüst szállítólevél száma: {{ $data['stock_entry']->relatedticketnumber }}</p>
        <br>
        <p><u>Vásárolt tételek:</u></p>
        <br>
        <table class="fs11" border="1" cellspacing="0" cellpadding="15">
            <thead>
                <tr>
                    <th class="fs11" align="center">MEGNEVEZÉS</th>
                    <th class="fs11" align="center">LEÍRÁS</th>
                    <th class="fs11" align="center">MENNYISÉG</th>
                    <th class="fs11" align="center">SÚLY</th>
                    <th class="fs11" align="center">EGYSÉGÁR</th>
                    <th class="fs11" align="center">VÉTELÁR</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($data['items'] ?? []) as $item)
                @php
                    $itemData = is_array($item->item_data ?? null)
                        ? $item->item_data
                        : (json_decode($item->item_data ?? '[]', true) ?: []);

                    $termekTipusId = $itemData['termek_tipus_id']
                        ?? ($item->termek_tipus_id ?? ($data['stock_entry']->header_data['termek_tipus_id'] ?? null));

                    $termekTipusName = $termekTipusId
                        ? ($data['product_type_names'][$termekTipusId] ?? null)
                        : null;
                @endphp
                <tr>
                    <td>{{ $termekTipusName ?: ($item->name ?: ($item->product->name ?? '')) }}</td>
                    <td>{{ $item->description ?? '' }}</td>
                    <td align="center">{{ $item->quantity ?? 0 }} db</td>
                    <td align="center">{{ $item->weight ?? 0 }}</td>
                    <td align="center">{{ number_format(((float)($item->oneprice ?? 0))*((float)($data['stock_entry']->currency_value ?? 1)), 0, ',', ' ') }} {{ $data['stock_entry']->currency->sign ?? '' }}</td>
                    <td align="center">{{ number_format(((float)($item->price ?? 0))*((float)($data['stock_entry']->currency_value ?? 1)), 0, ',', ' ') }} {{ $data['stock_entry']->currency->sign ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
            
        </table>
        <br/><br/>
        <p>Dátum: {{ $data['stock_entry']->business->city.", ".date('Y. m. d.', strtotime($data['stock_entry']->printed_at)) }}</p>
        <br>



        <table class="mt50" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="34%" align="center"><span class="fs13">..............................................................................</span></td>
                <td width="33%" align="center"></td>
                <td width="33%" align="center"></td>
            </tr>
            <tr>
                <td width="34%" align="center"><span class="fs13">Bizonylatot kiállította</span></td>
                <td width="33%" align="center"></td>
                <td width="33%" align="center"></td>
            </tr>
        </table>



        
    </div>
  </div>
</body>
</html>
