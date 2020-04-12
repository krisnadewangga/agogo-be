<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PENDAPATAN KASIR</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }
        .page{
            font-size: 20px !important; 
            max-width: 80em;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        table td {border-collapse: none; border:1px solid black; padding:5px;}
        table th {border-collapse: none; border:1px solid black; padding: 10px; background: #ededed;}

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
                    <h3>LAPORAN PENDAPATAN KASIR</h3>
                    <p style="margin-left:120px">Tanggal Transaksi : {{$start_tanggal}}</p>
                </div>            
                <br>
              
            </div>
            <br>
            <hr>

    <div class="page">
        
        
            <table cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kasir</th>
                        
                            <th >Saldo Awal</th>
                            <th >Total Transaksi</th>
                            <th >Total Refund</th>                            
                            <th >Total Pedapatan</th>
                            <th >Kas Tersedia</th>
                        </tr>
                    </thead>
                	<tbody>
                        @php $no=1; @endphp
                		@foreach($data as $key)

                        <tr>
                            <td align="center">{{ $no++ }}</td>
                            <td>{{ $key->User->name  }}</td>
                            <td >Rp. {{ number_format($key->saldo_awal,'0','','.') }}</td>
                            <td >Rp. {{ number_format($key->transaksi,'0','','.') }}</td>
                            <td >Rp. {{ number_format($key->total_refund,'0','','.') }}</td>
                            <td >Rp. {{ number_format($key->total_pendapatan,'0','','.') }}</td>
                            <td >Rp. {{ number_format($key->kas_tersedia,'0','','.') }}</td>
                        </tr>

                        @endforeach
                	</tbody>
                    
            </table>  

          
    </div>
</body>
</html>