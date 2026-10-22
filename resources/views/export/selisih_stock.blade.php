<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Selisih Stok Barang</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header {
            margin-bottom: 20px;
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
            padding: 6px;
            font-size: 11px;
        }
        td {
            padding: 5px;
            font-size: 10px;
        }
        .badge-red {
            color: #d9534f;
            font-weight: bold;
        }
        .badge-green {
            color: #5cb85c;
            font-weight: bold;
        }
        .badge-yellow{
            color: #F39C24;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h3>LAPORAN SELISIH STOK BARANG</h3>
        <p>Periode: {{ $start_tanggal }} S/D {{ $end_tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Nama Item</th>
                <th width="15%" class="text-right">Harga Item</th>
                <th width="15%" class="text-center">Total Selisih Pagi</th>
                <th width="15%" class="text-center">Total Selisih Malam</th>
                <th width="12%" class="text-center">Total Selisih</th>
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
                        <td class="text-center">
                            <span class="{{ $row->total_selisih_pagi != 0 ? 'badge-yellow' : 'badge-green' }}">
                                {{ $row->total_selisih_pagi }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $row->total_selisih_malam != 0 ? 'badge-yellow' : 'badge-green' }}">
                                {{ $row->total_selisih_malam }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $row->total_selisih != 0 ? 'badge-red' : 'badge-green' }}">
                                {{ $row->total_selisih }}
                            </span>
                        </td>
                        <td class="text-right"><b>{{ $row->tampil_total_nominal }}</b></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Tidak ada item yang memiliki selisih stok pada periode ini.</td>
                </tr>
            @endif
        </tbody>
        @if(!empty($data['data']) && count($data['data']) > 0)
        <tfoot>
            <tr style="background-color: #f9f9f9;">
                <td colspan="6" class="text-right"><b>Grand Total Harga Selisih :</b></td>
                <td class="text-right"><b>Rp. {{ $data['grandTotalNominal'] }}</b></td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>