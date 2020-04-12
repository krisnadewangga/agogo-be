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
           
            max-width: 80em;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        table td {border-collapse: none; border:1px solid black; padding:5px; font-size: 16px;}
        table th {border-collapse: none; border:1px solid black; padding: 10px; background: #ededed;  font-size: 19px;}

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
            <table cellspacing="0">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Kode Item</th>
                        <th rowspan="2">Nama Menu</th>
                        <th colspan="3">Produksi</th>
                        <th rowspan="2">Total Produksi</th>
                        <th rowspan="2">Pesanan diambil</th>
                        <th rowspan="2">Total Penjualan</th>
                        <th rowspan="2">Rusak</th>
                        <th rowspan="2">Lain - lain</th>
                        <th rowspan="2">Sisa Stock</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                    </tr>
                  
                </thead>
            	<tbody>
                    @php $no = 1; @endphp
                    @foreach($data as $key)
                        <tr>
                            <td align="center">{{ $no++ }}</td>
                            <td align="center">{{ $key->Item->code }}</td>
                            <td >{{ $key->Item->nama_item }}</td>
                
                            <td align="center">{{ number_format($key->produksi1,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->produksi2,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->produksi3,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->total_produksi,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->penjualan_pemesanan,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->penjualan_toko,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->ket_rusak,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->ket_lain,'0','','.') }}</td>
                            <td align="center">{{ number_format($key->sisa_stock,'0','','.') }}</td>
                        </tr>
                    @endforeach
            	</tbody>
            </table>  
    </div>
</body>
</html>