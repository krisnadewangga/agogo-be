<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PENJUALAN</title>
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
                <h3 style="">LAPORAN PENJUALAN</h3>
                <p >{{ $data['kop'] }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <div class="page">
            <table cellspacing="0" id="data-table">
                <thead>
                   <tr>
                        <th rowspan="2" style="width: 5px;">No</th>
                        <th rowspan="2" >Item</th>
                        <th colspan="{{ count($data['columns']) }}">{{ $data['kopHeader'] }}</th>
                        <th rowspan="2">Total</th>
                   </tr>
                   <tr>
                       @foreach($data['columns'] as $key_columns)
                            <th>{{ $key_columns }}</th>
                       @endforeach
                   </tr>
                </thead>
            	<tbody>
                   @php $no=1; @endphp
                   @foreach($data['data'] as $key_data)
                        <tr>
                            <td align="center">{{ $no++ }}</td>
                            <td>{{ $key_data['nama_item'] }}</td>
                            @foreach($key_data['jumlah'] as $key_jumlah)
                                <td align="center">{{ $key_jumlah }}</td>
                            @endforeach
                            <td align="center">{{ $key_data['totalJ'] }}</td>
                        </tr>
                   @endforeach
            	</tbody>
            </table>  
    </div>
</body>
</html>