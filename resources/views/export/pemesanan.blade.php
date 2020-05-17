<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PENDAPATAN HARIAN</title>
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
                <h4 style="">LAPORAN PEMESANAN</h4>
                <p>Tanggal Transaksi : {{$start_tanggal}} - {{ $end_tanggal }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <div class="page" >
        <table cellspacing="0" class="layout display responsive-table">
            <thead>
                <tr>
                    <th style="width: 5px;"><ceter>No</ceter></th>
                    <th ><center>Order</center></th>
                    <th ><center>Tanggal</center></th>
                    <th ><center>Tanggal Selesai</center></th>
                    <th ><center>Jam</center></th>
                    <th ><center>Status</center></th>
                    <th ><center>Metode</center></th>
                    <th ><center>Pencatat</center></th>
                    <th ><center>Pelanggan</center></th>
                    <th style="text-align: right;">Total Harga</th>
                    <th style="text-align: right;">DP</th>
                    <th style="text-align: right;">Sisa</th>
                </tr>
               
              
            </thead>
            <tbody>
                @php $no=1; @endphp
                @foreach($data['data'] as $key)
                    <tr>
                        <td style="text-align:center;border-bottom:0px">{{ $no++ }}</td>
                        <td style="text-align:center;border-bottom:0px" >{{ $key->no_transaksi }}</td>
                        <td style="text-align:center;border-bottom:0px" >{{ $key->tgl_pesan }}</td>
                        <td style="text-align:center;border-bottom:0px" >{{ $key->tgl_selesai}}</td>
                        <td style="text-align:center;border-bottom:0px" >{{ $key->jam}}</td>
                        <td style="text-align:center;border-bottom:0px" >
                            @if($key->status == "5")
                                @if($key->metode_pembayaran == "1" || $key->metode_pembayaran == "2")
                                    Sudah Diterima
                                @else
                                    Sudah Diambil
                                @endif
                            @else
                                @if($key->metode_pembayaran == "1" || $key->metode_pembayaran == "2")
                                    Belum Diterima
                                @else
                                    Belum Diambil
                                @endif
                            @endif
                        </td>
                        <td style="text-align:center;border-bottom:0px" >
                            @if($key->jenis == "2")
                                Pemesanan
                            @else
                                @if($key->metode_pembayaran == "1")
                                    TopUp
                                @elseif($key->metode_pembayaran == "2")
                                    Bank Transfer
                                @else
                                    Bayar Ditoko
                                @endif
                            @endif
                        </td>
                        <td style="text-align:center;border-bottom:0px">{{ $key->pencatat }}</td>
                        <td style="text-align:center;border-bottom:0px">{{ $key->nama }}</td>
                        <td style="text-align:right; border-bottom:0px">{{ number_format($key->total,'0','','.') }}</td>
                        <td style="text-align:right; border-bottom:0px">{{ number_format($key->uang_muka,'0','','.') }}</td>
                        <td style="text-align:right; border-bottom:0px">{{ number_format($key->sisa_bayar,'0','','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                    <tr>
                        <td colspan="8" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"></td>                
                        <td style="text-align:right; border-bottom:0px; border-top: 1px solid black;">Grand Total :</td>
                        <td style="text-align:right;border-bottom:0px;border-top: 1px solid black;" >
                            {{ $data['tfoot']->grand_total_th }} </td>
                        <td  style="text-align:right;border-bottom:0px;border-top: 1px solid black;">{{ $data['tfoot']->grand_total_dp }} </td>
                        <td  style="text-align:right;border-bottom:0px;border-top: 1px solid black;">{{ $data['tfoot']->grand_total_sisa }} </td>

                    </tr>
                    <tr>
                        <td colspan="8" style="text-align:right;border-bottom:0px"></td>
                        <td  style="text-align:right;border-bottom:0px">Pembatalan Transaksi : </td>
                        <td  style="text-align:right;border-bottom:0px" >{{ $data['tfoot']->pembatalan_transaksi_th }}</td>
                        <td  style="text-align:right;border-bottom:0px">{{ $data['tfoot']->pembatalan_transaksi_dp }}</td>
                        <td  style="text-align:right;border-bottom:0px"></td>
                    </tr>
                    <tr>
                        <td colspan="8"  style="text-align:right;border-bottom:0px"></td>
                        <td  style="text-align:right;border-bottom:0px">Total Transaksi : </td>
                        <td  style="text-align:right;border-bottom:0px" >{{ $data['tfoot']->total_transaksi_th }}</td>
                        <td  style="text-align:right;border-bottom:0px" >{{ $data['tfoot']->total_transaksi_dp }}</td>
                        <td  style="text-align:right;border-bottom:0px" ></td>
                    </tr>
            </tfoot>
        </table>  
    </div>

</body>
</html>