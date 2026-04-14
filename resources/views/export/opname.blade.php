<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN OPNAME</title>
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
            font-size: 14px !important;
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
                 <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/dist/img/agogo-report.png'))) }}" alt="Image" height="100px"/>     
            </td>
            <td  style="width: 70%; text-align: right;">
                <h4 style="">LAPORAN OPNAME</h4>
                <span >Tanggal : {{ $start_tanggal }}</span>
            </td>
        </tr>
    </table>
    <div class="page">
         <table class="layout display responsive-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th style="text-align:center;">Kode Menu</th>
                    <th style="text-align:center;">Nama Menu</th>
                    <th style="text-align:center;">Stock Awal Komputer</th>
                    <th style="text-align:center;">Stock Asli</th>
                    <th style="text-align:center;">Stock Awal Fisik Pagi</th>
                    <th style="text-align:center;">Selisih Pagi</th>
                    <th style="text-align:center;">Produksi</th>
                    <th style="text-align:center;">Rusak</th>
                    <th style="text-align:center;">Terjual</th>
                    <th style="text-align:center;">Stock Akhir Komputer</th>
                    <th style="text-align:center;">Stock Akhir Fisik Malam</th>
                    <th style="text-align:center;">Selisih Malam</th>
                    <th style="text-align:center;">Stock Opname</th>
                </tr>
            </thead>
            <tbody>
               @php $no=1;@endphp
               @forelse($data as $key)
                    @if($key->stock_awal !== 0 || $key->stock_akhir !== 0 || $key->produksi !== 0 || $key->terjual !== 0)
                        <tr>
                            <td align="center" style="text-align:center;">{{ $no++ }}</td>
                            <td style="text-align:center;">{{ $key['code'] }}</td>
                            <td style="text-align:center;">{{ $key['nama_item'] }}</td>
                            <td style="text-align:center;">{{ $key->stock_awal }}</td>
                            <td style="text-align:center;">{{ $key->sisa_stock }}</td>
                            <td style="text-align:center;">{{ $key->stock_fisik_pagi }}</td>
                            <td style="text-align:center;">{{ $key->selisih_pagi }}</td>
                            <td style="text-align:center;">{{ $key->produksi }}</td>
                            <td style="text-align:center;">{{ $key->rusak }}</td>
                            <td style="text-align:center;">{{ $key->terjual }}</td>
                            <td style="text-align:center;">{{ $key->stock_akhir }}</td>
                            <td style="text-align:center;">{{ $key->stock_fisik_malam }}</td>
                            <td style="text-align:center;">{{ $key->selisih_malam }}</td>
                            <td style="text-align:center;">{{ $key->stock_toko }}</td>
                        </tr>
                    @endif
               @empty
                <tr>
                    <td class="text-center" colspan="5">Tidak ada data opname hari ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>  
    </div>
</body>
</html>