<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PERGERAKAN STOCK PRODUKSI</title>
    <style>
       body{
            padding: 0;
            margin: 0;
        }
        .page{
            font-size: 16px !important; 
            max-width: 80em;
            margin: 0 auto;
        }
        table 
        { 
        table-layout:auto !important; 
        width: 100% !important;
        }


        table th,
        table td{
            text-align: center;
        }

        table, th, td {
            border-bottom: 1px solid black;
        }
        table.layout{
            width: 100%;
            border-collapse: collapse;
        }
        table.display{
            margin: 1em 0;
        }
        table.display th,
        table.display td{
            border-bottom: 1px solid black;
            padding: .2em 0,8em;
        }

        table.display th{ background: #fff; }
        table.display td{ background: #fff; }

        table.responsive-table{
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
        }

        .listcust {
            margin: 0;
            padding: 0;
            list-style: none;
            display:table;
            border-spacing: 10px;
            border-collapse: separate;
            list-style-type: none;
        }

        .customer {
            padding-left: 600px;
        }
        hr { 
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 1px;
            } 
    </style>
</head>
<body>
        <div class="header">
                <img src="{{ asset('assets/dist/img/agogo-report.png') }}" alt="Image" height="100px"/>        
                <div style="float:right;margin-top:-30px">
                    <h3>LAPORAN STOCK PRODUKSI</h3>
                    <p style="margin-left:120px">Tanggal Produksi : {{$start_tanggal}}</p>
                </div>            
                <br>
              
            </div>
            <br>
            <hr>

    <div class="page">
           
            <table cellspacing="0" class="layout display responsive-table" style="border-bottom:0px">
                <thead>
                    <tr>
                        <th >No.</th>
                        <th >Kode Menu</th>
                        <th >Nama Menu</th>
                        <th >Stock Awal</th>
                        <th >Produksi</th>
                        <th >Toko</th>
                        <th >Pemesanan</th>
                        <th >Total Penjualan</th>
                        <th >Rusak</th>
                        <th >Lain - lain</th>
                        <th >Sisa Stock</th>
                    </tr>
                    
                  
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($data as $key)
                        <tr>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ $no++ }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ $key->Item->code }}</td>
                            <td style="text-align:center;border-bottom:0px">{{ $key->Item->nama_item }}</td>
                
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->stock_awal,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->produksi1,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->penjualan_toko,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->penjualan_pemesanan,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->total_penjualan,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->ket_rusak,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->ket_lain,'0','','.') }}</td>
                            <td align="center" style="text-align:center;border-bottom:0px">{{ number_format($key->sisa_stock,'0','','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>  
    </div>
</body>
</html>