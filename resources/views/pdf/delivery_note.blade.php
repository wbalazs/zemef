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
    .date{
        float: left;
    }
    .serial{
        float: right;
    }
    .fs11{
        font-size: 11px;
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
                    <span class="fs13">Sorszám: {{ $data['delivery_note']->delivery_note_number }}</span><br>
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
                        if($data['delivery_note']->is_storno){
                            echo '<div style="font-size:30px;margin-left:14px;margin-top:10px;">Sztornó Szállítólevél</div><br>';
                        }else{
                            echo '<div style="font-size:30px;margin-left:14px;margin-top:10px;">Szállítólevél</div><br>';
                        }  
                    @endphp
                </td>
                <td width="20%" valign="top">

                </td>
            </tr>
        </table>
        <br>

        <p class="fs12">Átadó: {{ $data['delivery_note']->business->name.", ".$data['delivery_note']->employee_name }}</p>
        <p class="fs12">Átvevő: {{ $data['delivery_note']->business->name.", ".$data['delivery_note']->receive_employee_name }}</p>
        <p class="fs12">Átadás módja:
            @php if($data['delivery_note']->shipping_date){ @endphp
                Futárral: {{ " ".$data['delivery_note']->shipping_date."  -  (".$data['delivery_note']->shipping_label.")" }}
            @php }else{ @endphp
                Személyesen
            @php } @endphp
        </p>

        <br>
        <div class="fs12"><u>Szállítólevél tételek:</u></div><br>
        <table class="fs12" border="1" cellspacing="0" cellpadding="15">
            <thead>
                <tr>
                    <th class="fs12" align="center">Sorszám</th>
                    <th class="fs12" align="center">Megnevezés</th>
                    <th class="fs12" align="center">Mennyiség</th>
                    <th class="fs12" align="center">Súly</th>
                    <th class="fs12" align="center">Leírás</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['delivery_note']->delivery_note_items as $item)
                    <tr>
                        <td>#{{ $item->inventory_item_id ?? $item->note_items_id }}</td>
                        <td>{{ $item->product_name ?? '-' }}</td>
                        <td>{{ $item->quantity ?? 0 }} db</td>
                        <td align="center">{{ $item->weight ?? 0 }}</td>
                        <td>{{ $item->product_description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" align="center">Nincs tétel a szállítólevélhez.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <br>
        <p>Dátum: {{ $data['delivery_note']->business->city.", ".date('Y. m. d.', strtotime($data['delivery_note']->printed_at)) }}</p>
        <br>





        <br>
        <table class="mt50" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center"><span class="fs12">..............................................................................</span></td>
                <td></td>
                <td align="center"><span class="fs12">..............................................................................</span></td>
            </tr>
            <tr>
                <td align="center"><span class="fs12">Átadó</span></td>
                <td></td>
                <td align="center"><span class="fs12">Átvevő</span></td>
            </tr>
            <tr>
                <td align="center"><span class="fs12">{{ date('Y. m. d.', strtotime($data['delivery_note']->printed_at)).", ".$data['delivery_note']->business->city }}</span></td>
                <td></td>
                <td align="center"><span class="fs12">{{ $data['delivery_note']->received_at ? date('Y. m. d.', strtotime($data['delivery_note']->received_at)).", ".$data['delivery_note']->business->city : '-' }}</span></td>
            </tr>
        </table>



        
    </div>
  </div>
</body>
</html>
