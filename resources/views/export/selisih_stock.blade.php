<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Selisih Stok Barang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header {
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th {
            background-color: #f2f2f2;
            padding: 5px;
            font-size: 10px;
        }
        td {
            padding: 4px;
            font-size: 9px;
        }
        .badge-yellow {
            color: #8a6d3b;
            font-weight: bold;
        }
        .badge-green {
            color: #3c763d;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h3>LAPORAN SELISIH STOK BARANG</h3>
        <p>Periode: {{ $start_tanggal }} S/D {{ $end_tanggal }} 
           @if(($data['jenis_selisih'] ?? '1') == '2') (Selisih Pagi Saja)
           @elseif(($data['jenis_selisih'] ?? '1') == '3') (Selisih Malam Saja)
           @else (Keduanya) @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th>Nama Item</th>
                <th width="15%" class="text-right">Harga Item</th>
                @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '2')
                    <th width="14%" class="text-center">Selisih Pagi</th>
                    <th width="15%" class="text-right">Rp. Pagi</th>
                @endif
                @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '3')
                    <th width="14%" class="text-center">Selisih Malam</th>
                    <th width="15%" class="text-right">Rp. Malam</th>
                @endif
                <th width="18%" class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data['data']) && count($data['data']) > 0)
                @foreach($data['data'] as $key => $row)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $row->nama_item }}</td>
                        <td class="text-right">{{ $row->harga }}</td>
                        @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '2')
                            <td class="text-center">
                                <span class="{{ $row->total_selisih_pagi != 0 ? 'badge-yellow' : 'badge-green' }}">
                                    {{ $row->total_selisih_pagi }}
                                </span>
                            </td>
                            <td class="text-right">{{ $row->total_nominal_pagi }}</td>
                        @endif
                        @if(($data['jenis_selisih'] ?? '1') == '1' || ($data['jenis_selisih'] ?? '1') == '3')
                            <td class="text-center">
                                <span class="{{ $row->total_selisih_malam != 0 ? 'badge-yellow' : 'badge-green' }}">
                                    {{ $row->total_selisih_malam }}
                                </span>
                            </td>
                            <td class="text-right">{{ $row->total_nominal_malam }}</td>
                        @endif
                        <td class="text-right"><b>{{ $row->tampil_total_nominal }}</b></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    @php
                        $colCount = 4;
                        if(($data['jenis_selisih'] ?? '1') == '1') $colCount = 8;
                        elseif(($data['jenis_selisih'] ?? '1') == '2' || ($data['jenis_selisih'] ?? '1') == '3') $colCount = 6;
                    @endphp
                    <td colspan="{{ $colCount }}" class="text-center">Tidak ada item yang memiliki selisih stok pada periode ini.</td>
                </tr>
            @endif
        </tbody>
        @if(!empty($data['data']) && count($data['data']) > 0)
        <tfoot>
            <tr style="background-color: #f9f9f9;">
                @php
                    $colSpanFoot = 3;
                    if(($data['jenis_selisih'] ?? '1') == '1') $colSpanFoot = 7;
                    elseif(($data['jenis_selisih'] ?? '1') == '2' || ($data['jenis_selisih'] ?? '1') == '3') $colSpanFoot = 5;
                @endphp
                <td colspan="{{ $colSpanFoot - 1 }}" class="text-right"><b>Grand Total :</b></td>
                <td class="text-right"><b>Rp. {{ $data['grandTotalNominal'] }}</b></td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>