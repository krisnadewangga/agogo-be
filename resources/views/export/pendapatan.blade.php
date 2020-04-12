<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PENDAPATAN</title>
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
        #data-table td {border-collapse: none; border:1px solid black; padding:5px; font-size: 16px;}
        #data-table th {border-collapse: none; border:1px solid black; padding: 10px; background: #ededed;  font-size: 19px;}

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
        
    <table>
        <tr>
            <td>
                 <img src="{{ asset('assets/dist/img/agogo-report.png') }}" alt="Image" height="100px"/>     
            </td>
            <td align="right">
                <h3 style="">LAPORAN PENDAPATAN</h3>
                <p >{{ $data->kop_export }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <div class="page">
            <table cellspacing="0" id="data-table">
                <thead>
                   <tr>
                        <th style="width: 5px;">No</th>
                        <th>Waktu</th>
                        <th>No Transaksi</th>
                        <th>Pemesan / Kasir</th>
                        <th ><center>Jumlah Pesanan</center></th>
                        <th ><center>Total Bayar</center></th>
                        <th ><center>Jenis Transaksi</center></th>
                    </tr>
                </thead>
            	<tbody>
                    @php $no=1; @endphp
                    @foreach($data->transaksi as $key)
                        <tr>
                            <td align="center">{{ $no++ }}</td>
                            <td>{{ $key->tgl_bayar->format('d M Y H:i A') }}</td>
                            <td>{{ $key->no_transaksi }}</td>
                            <td>{{ $key->User->name }}</td>
                            <td align="center">{{ $key->ItemTransaksi()->count() }} Pesanan</td>
                            <td align="right">{{ number_format($key->total_bayar,'0','','.') }}</td>
                            <td align="center">
                                @if($key['metode_pembayaran'] == 1)
                                    TopUp
                                @elseif($key['metode_pembayaran'] == 2)
                                    Bank Transfer
                                @elseif($key['metode_pembayaran'] == 3)
                                    Bayar Di Toko
                                @endif
                            </td>
                            
                        </tr>
                    @endforeach
                    <tr>
                        <td align="center">#</td>
                        <td colspan="4" align="right">Total</td>
                        <td align="right">{{ number_format($data->total_pendapatan,'0','','.') }}</td>
                        <td></td>
                    </tr>
            	</tbody>
              
            </table>  
    </div>
</body>
</html>