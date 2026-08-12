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

    body::before {
        content: "";
        position: fixed; 
        top: 0;
        right: 0;
        bottom: 40;
        left: 0;
        z-index: -1; 

        background-image: url('{{ asset('pdf/images/purchase_ticket/zemef_back.png') }}');
        background-repeat: no-repeat;
        background-position: center bottom;
        background-size: contain;
        background-attachment: fixed;
        opacity: 0.1;
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
  </style>
  
</head>
<body>
  <div class="container">
    <div class="body">

        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                <span class="fs16">SORSZÁM: {{ $data['warehouse_transfer']->warehouse_transfer_number }}</span><br>
                    <span class="fs16">DÁTUM: {{ date('Y. m. d.', strtotime($data['warehouse_transfer']->created_at)) }}</span>
                </td>
            </tr>
        </table>
        
        <div style="font-size:50px;text-align:center;">RAKTÁRKÖZI BIZONYLAT</div><br>


        <table border="0" cellspacing="0" cellpadding="15">
            <tr>
                <td style="width:40%;border:1px solid rgb(85, 83, 83);" align="center"><strong>ÁTADÓ</strong></td>
                <td style="width:20%;"></td>
                <td style="width:40%;border:1px solid rgb(85, 83, 83);" align="center"><strong>ÁTVEVŐ</strong></td>
            </tr>
            <tr>
                <td style="width:40%;border:1px solid rgb(85, 83, 83);" align="center"><strong>{{ $data['warehouse_transfer']->employee_name.' - ('.$data['warehouse_transfer']->warehouse_from_name.')' }}</strong></td>
                <td style="width:20%; border-left: none; border-right: none;"></td>
                <td style="width:40%;border:1px solid rgb(85, 83, 83);" align="center"><strong>{{ $data['warehouse_transfer']->warehouse_to_name }}</strong></td>
            </tr>
        </table>


        <div class="fs28">Raktárközi bizonylat tételek</div><br>

            <table class="fs11" border="1" cellspacing="0" cellpadding="15">
                <thead>
                    <tr>
                        <th class="fs11" align="center">MEGNEVEZÉS</th>
                        <th class="fs11" align="center">MENNYISÉG</th>
                        <th class="fs11" align="center">LEÍRÁS</th>
                        <th class="fs11" align="center">GYÁRTÁSI SZÁM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['warehouse_transfer']->warehouse_transfer_items as $item)
                            <tr>
                                <td align="center">{{ $item->inventoryItem->name ?? 'Ismeretlen' }} ({{ $item->inventoryItem->code ?? 'Ismeretlen' }})</td>
                                <td align="center">{{ $item->quantity }} db</td>
                                <td align="center">{{ $item->note }}</td>
                                <td align="center">{{ $item->inventoryItem->factory_number ?? 'Ismeretlen' }}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
            <br>


        <br>
        <table class="mt50" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center"><span class="fs13">..............................................................................</span></td>
                <td></td>
                <td align="center"><span class="fs13">..............................................................................</span></td>
            </tr>
            <tr>
                <td align="center"><span class="fs13">ÁTADÓ ALÁÍRÁSA</span></td>
                <td></td>
                <td align="center"><span class="fs13">ÁTVEVŐ ALÁÍRÁSA</span></td>
            </tr>
        </table>



        
    </div>
  </div>
</body>
</html>
