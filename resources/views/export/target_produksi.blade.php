<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LAPORAN TARGET REALISASI DAN PRODUKSI</title>
    <style>
        /* Memastikan border dihitung di dalam lebar elemen */
        * { box-sizing: border-box; }

        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }

        /* Container utama menggunakan tabel agar kolom kiri & kanan tidak terpisah */
        .wrapper-table {
            width: 100%;
            border: none;
            table-layout: fixed;
        }

        .wrapper-table td {
            vertical-align: top;
            border: none;
            padding: 0 5px; /* Jarak antar kolom */
        }

        /* Styling Tabel Data (Roti) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid black; /* Border luar lebih tebal sesuai foto */
        }

        .data-table th, .data-table td {
            border: 1px solid black;
            padding: 4px;
            font-size: 14px;
            height: 22px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .header-info {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
            display: block;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }

        /* Lebar kolom dalam tabel */
        .col-nama { width: 50%; }
        .col-qty { width: 15%; }
        .col-ket { width: 20%; }

        .bold { 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    @php
        $filtered = collect($data)->filter(function ($item) {
            return $item->target_produksi != '' || $item->realisasi_produksi != 0;
        });

        $rowsPerColumn = 45;
        $chunks = $filtered->chunk($rowsPerColumn * 2);
    @endphp

    @forelse($chunks as $chunk)
        <table class="wrapper-table">
            <tr>
                @php
                    $leftChunk = $chunk->slice(0, $rowsPerColumn);
                    $rightChunk = $chunk->slice($rowsPerColumn, $rowsPerColumn);

                    $explode = explode("/", $start_tanggal );
                    $tanggal_cetak = $explode[1]."/".$explode[0]."/".$explode[2];

                    $setTanggalCetak = \Carbon\Carbon::parse($tanggal_cetak)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                @endphp

                <td>
                    <div class="header-info">
                        Target Produksi Hari : <u>{{ $setTanggalCetak }}</u>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="col-nama">NAMA BARANG/KUE</th>
                                <th class="col-qty">TARGET</th>
                                <th class="col-qty">REALISASI</th>
                                <th class="col-ket">KET</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leftChunk as $item)
                                <tr>
                                    <td class="text-left">{{ $item->nama_item }}</td>
                                    <td class="text-center bold">{{ $item->target_produksi }}</td>
                                    <td class="text-center bold">{{ $item->realisasi_produksi ?: '' }}</td>
                                    <td class="text-center">{{ $item->keterangan ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>

                <td>
                    <div class="header-info">
                        Target Produksi Hari : <u>{{ $setTanggalCetak }}</u>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="col-nama">NAMA BARANG/KUE</th>
                                <th class="col-qty">TARGET</th>
                                <th class="col-qty">REALISASI</th>
                                <th class="col-ket">KET</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rightChunk as $item)
                                <tr>
                                    <td class="text-left">{{ $item->nama_item }}</td>
                                    <td class="text-center bold">{{ $item->target_produksi }}</td>
                                    <td class="text-center bold">{{ $item->realisasi_produksi ?: '' }}</td>
                                    <td class="text-center">{{ $item->keterangan ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <!-- <div class="page-break"></div> -->
    @empty
        <p>Data tidak tersedia.</p>
    @endforelse
</body>
</html>