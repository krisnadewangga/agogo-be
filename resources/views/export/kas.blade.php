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
        table th,
        table td{
            text-align: left;
        }
        table.layout{
            
            border-collapse: collapse;
        }
        table.display{
            margin: 1em 0;
        }
        table.display th,
        table.display td{
            border-top: 1px solid ;
            /* border-bottom: 1px solid ; */
            padding: .5em 1em;
            border-spacing: 20px;
        }

        /* table.display th{ background: #D5E0CC; } */
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
                    <h3>LAPORAN PENDAPATAN KASIR</h3>
                    <p style="margin-left:120px">Tanggal Transaksi : {{$start_tanggal}}</p>
                </div>            
                <br>
              
            </div>
            <br>
            <hr>

    <div class="page">
        
        
            <table cellspacing="0"  class="layout display responsive-table" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kasir</th>  
                            <th style="text-align:right;border-bottom:0px">Saldo Awal</th>
                            <th style="text-align:right;border-bottom:0px">Total Transaksi</th>
                            <th style="text-align:right;border-bottom:0px">Total Refund</th>                            
                            <th style="text-align:right;border-bottom:0px">Total Pedapatan</th>
                            <th style="text-align:right;border-bottom:0px">Kas Tersedia</th>
                        </tr>
                    </thead>
                	<tbody>
                        @php $no=1; @endphp
                		@forelse($data as $key)

                        <tr>
                            <td align="center" >{{ $no++ }}</td>
                            <td >{{ $key->User->name  }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($key->saldo_awal,'0','','.') }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($key->transaksi,'0','','.') }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($key->total_refund,'0','','.') }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($key->total_pendapatan,'0','','.') }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($key->kas_tersedia,'0','','.') }}</td>
                        </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="7">Tidak ada data perhitungan Kas hari ini</td>
                            </tr>
                        @endforelse
                	</tbody>
                    
            </table>  

              <table class="display responsive-table" style="width: 40%; float: left; margin-left:20px; margin-top:-2px;border-spacing=0;">
                <thead>
                    <tr>
                        <th style="border-top: 0px solid;">Penerimaan Uang</th>
                        <th style="text-align: Center; border-left: 2px solid ;border-top: 0px solid;">Jumlah</th>
                        
                    </tr>
                </thead>
                <tbody>
                   
                    <tr>
                        <td>100.000</td>      
                        <td style="text-align: right; border-left: 2px solid ;"></td>                                                                                        
                    </tr>
                    <tr>
                        <td>50.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                                                                                                                                                                                                                                                                                                                                                                     
                    </tr>
                    <tr>
                        <td>20.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>5000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>2000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>1000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>500</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>200</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>100</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Kas Selisih </th>      
                        <th style="text-align: right;border-left: 2px solid ;"></th>                                                                                                                                                                                                                                                                                                                                             
                    </tr>
                    
                </tfoot>
            </table>
            <div>
                <h4 style="margin-top: 60%;float:right;margin-right:25%">Keterangan</h4>
                <h4 style="margin-top: 40%;float:right;margin-right:-7%">KASIR</h4>
                <h4 style="margin-top: 40%;float:right;margin-right:-35%">MANAGER</h4>
               
            </div>
          
    </div>
</body>
</html>