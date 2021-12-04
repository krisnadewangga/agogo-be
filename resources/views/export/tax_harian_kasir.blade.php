<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN TAX HARIAN KASIR</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }
     
      .page{
            font-size: 24px !important; 
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
        
    <table border="0">
        <tr>
            <td>
                 <img src="{{ asset('assets/dist/img/agogo-report.png') }}" alt="Image" height="100px"/>     
            </td>
            <td  style="width: 70%; text-align: right;">
                <h4 style="">LAPORAN TOTAL TAX HARIAN KASIR</h4>
                <p style="font-size: 20px">Tanggal Transaksi : {{ $start_tanggal }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <div class="page">
         <table class="layout display responsive-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th style="text-align:center;">Tanggal Transaksi</th>
                    <th style="text-align:right;">Transaksi</th>
                    <th style="text-align:right;">Tax</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($data['data'] as $key)
                     <tr>
                
                        <td style="text-align:center;border-bottom:0px">{{ $no++ }}</td>
                        <td style="text-align:center;border-bottom:0px">{{ Carbon\Carbon::parse($key->tgl)->format('d/m/Y') }}</td>                                            
                        <td style="text-align:right;border-bottom:0px">{{ number_format($key->total_transaksi) }}</td>
                        <td style="text-align:right;border-bottom:0px">{{ number_format($key->total_tax) }}</td>
                        
                    </tr>
                @empty
                     <tr>
                        <td class="text-center" colspan="5">Tidak ada data penjualan bulan ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">Grand Total : </th>
                    <th style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{ $data['grandTotal'] }}</th>
                    <th style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{ $data['grandTotalTax'] }}</th>
                   
                </tr>      
            </tfoot>
        </table>
</div>
</body>
</html>