<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PEMESANAN</title>
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
                <div style="width: 40%; display: inline-block;">
                <img src="{{ asset('assets/dist/img/agogo-report.png') }}" alt="Image" height="100px"/>        
                </div>
                <div style="display: inline-block; text-align: right; width: 50%; ">
                    <h3 style="">LAPORAN PEMESANAN</h3>
                    <p >Tanggal : {{$start_tanggal}} - {{ $end_tanggal }}</p>
                </div>            
                <br>
              
        </div>
          
        <hr>

    <div class="page">
            <table cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5px;">No</th>
                        <th >Order</th>
                        <th >Tanggal</th>
                        <th >Tanggal Selesai</th>
                        <th >Jam </th>
                        <th >Status Order</th>
                        <th >Pencatat</th>
                        <th >Pelanggan</th>
                        <th >Total Harga</th>
                        <th >DP</th>
                        <th >Sisa</th>
                    </tr>
                   
                  
                </thead>
            	<tbody>
                    @php $no=1; @endphp
                    @foreach($data['data'] as $key)
                        <tr>
                            <td align="center">{{ $no++ }}</td>
                             <td >{{ $key->no_transaksi }}</td>
                           
                            <td >{{ $key->tgl_pesan->format('d/m/Y') }}</td>
                            <td >{{ $key->tgl_selesai->format('d/m/Y') }}</td>
                            <td >{{ $key->waktu_selesai }}</td>
                            <td >
                                @if($key->status == "1")
                                    Belum Diambil
                                @else

                                @endif
                            </td>
                            <td >{{ $key->pencatat }}</td>
                             <td >{{ $key->nama }}</td>
                            <td align="right">{{ number_format($key->total,'0','','.') }}</td>
                            <td align="right">{{ number_format($key->uang_muka,'0','','.') }}</td>
                            <td align="right">{{ number_format($key->sisa_bayar,'0','','.') }}</td>
                        </tr>
                    @endforeach
            	</tbody>
                <tfoot>
                        <tr>
                            <td colspan="7" style="text-align:right"></td>                
                            <td  style="text-align:right">Grand Total :</td>
                            <td  style="text-align:right" >{{ $data['tfoot']->grand_total_th }} </td>
                            <td  style="text-align:right" >{{ $data['tfoot']->grand_total_dp }} </td>
                            <td  style="text-align:right" >{{ $data['tfoot']->grand_total_sisa }} </td>

                        </tr>
                        <tr>
                            <td colspan="7" style="text-align:right"></td>
                            <td   style="text-align:right">Pembatalan Transaksi : </td>
                            <td   style="text-align:right" >{{ $data['tfoot']->pembatalan_transaksi_th }}</td>
                            <td   style="text-align:right" >{{ $data['tfoot']->pembatalan_transaksi_dp }}</td>
                            <td  style="text-align:right"></td>
                        </tr>
                        <tr>
                            <td colspan="7" style="text-align:right"></td>
                            <td  style="text-align:right">Total Transaksi : </td>
                            <td  style="text-align:right" >{{ $data['tfoot']->total_transaksi_th }}</td>
                            <td  style="text-align:right" >{{ $data['tfoot']->total_transaksi_dp }}</td>
                            <td  style="text-align:right" ></td>
                        </tr>
                </tfoot>
            </table>  
    </div>
</body>
</html>