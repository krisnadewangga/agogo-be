<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\TargetProduksi;
use App\Produksi;
use App\Item;   // ✅ Import model Item agar tidak error
use App\Opname; // ✅ Import model Opname

class SheetWebhookController extends Controller
{
    public function handleSheetAction(Request $request)
    {
        $action = $request->input('action') ?? $request->query('action');

        switch ($action) {
            case 'save_target':
                return $this->saveTargetData($request);

            case 'load_target':
                return $this->loadTargetData($request);

            case 'save_airmadidi':
                return $this->saveAirmadidiData($request);

            case 'load_airmadidi':
                return $this->loadAirmadidiData($request);

            default:
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Action tidak valid atau belum ditentukan!'
                ], 400);
        }
    }

    // --- 1. SIMPAN TARGET PRODUKSI --- 
    private function saveTargetData(Request $request)
    {
        try {
            $rows = $request->input('data', []);
            $processed = 0;

            foreach ($rows as $row) {
                $identifier = trim((string) ($row['code'] ?? $row['kode_item'] ?? $row['nama_item'] ?? ''));
                $targetProduksi = array_key_exists('target', $row) ? $row['target'] : ($row['target_produksi'] ?? null);
                $rawTargetDate = $row['target_date'] ?? $row['tanggal'] ?? $row['date'] ?? Carbon::today()->format('Y-m-d');

                if ($identifier === '' || $targetProduksi === null || $targetProduksi === '') {
                    continue;
                }

                $item = Item::where('nama_item', $identifier)
                    ->orWhere('code', $identifier)
                    ->first();

                if ($item) {
                    try {
                        $formattedTargetDate = Carbon::parse($rawTargetDate)->format('Y-m-d 00:00:00');
                    } catch (\Exception $e) {
                        $formattedTargetDate = Carbon::today()->format('Y-m-d 00:00:00');
                    }

                    TargetProduksi::updateOrCreate(
                        [
                            'item_id'     => $item->id,
                            'target_date' => $formattedTargetDate,
                        ],
                        [
                            'target_produksi' => (float) $targetProduksi,
                        ]
                    );

                    $processed++;
                }
            }

            return response()->json([
                'status'    => 'success',
                'processed' => $processed
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    // --- 2. AMBIL/LOAD TARGET PRODUKSI ---
    private function loadTargetData(Request $request)
    {
        $date = $request->input('date') ?? $request->query('date', Carbon::today()->format('Y-m-d'));

        $targetData = TargetProduksi::with('item')
            ->whereDate('target_date', $date)
            ->get();

        $result = [];
        foreach ($targetData as $t) {
            if ($t->item) {
                $result[] = [
                    'code'            => trim((string)($t->item->code ?? $t->item->kode_item)),
                    'nama_item'       => trim((string)$t->item->nama_item),
                    'target_produksi' => (float)$t->target_produksi,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'date'   => $date,
            'data'   => $result
        ], 200);
    }

    // --- 3. SIMPAN TOTAL PRODUKSI & AIRMADIDI ---
    private function saveAirmadidiData(Request $request)
    {
        try {
            $rows = $request->input('data', []);
            $processed = 0;
            $todayStr = Carbon::today()->format('Y-m-d');

            foreach ($rows as $row) {
                $identifier = trim((string) ($row['code'] ?? $row['kode_item'] ?? $row['nama_item'] ?? ''));
                $rawTargetDate = $row['target_date'] ?? $row['tanggal'] ?? $row['date'] ?? $todayStr;
                try {
                    $targetDate = Carbon::parse($rawTargetDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $targetDate = $todayStr;
                }

                // 1. Cek ketersediaan input dari payload Google Apps Script (Termasuk ket_rusak & aliases)
                $hasProduksiInput = (array_key_exists('realisasi', $row) && $row['realisasi'] !== null && $row['realisasi'] !== '')
                    || (array_key_exists('produksi1', $row) && $row['produksi1'] !== null && $row['produksi1'] !== '')
                    || (array_key_exists('produksi', $row) && $row['produksi'] !== null && $row['produksi'] !== '');

                $hasKetLainInput = (array_key_exists('ket_lain', $row) && $row['ket_lain'] !== null && $row['ket_lain'] !== '')
                    || (array_key_exists('airmadidi', $row) && $row['airmadidi'] !== null && $row['airmadidi'] !== '')
                    || (array_key_exists('lain', $row) && $row['lain'] !== null && $row['lain'] !== '');

                $hasKetRusakInput = (array_key_exists('ket_rusak', $row) && $row['ket_rusak'] !== null && $row['ket_rusak'] !== '')
                    || (array_key_exists('rusak', $row) && $row['rusak'] !== null && $row['rusak'] !== '');

                $hasFisikPagiInput = (array_key_exists('stock_fisik_pagi', $row) && $row['stock_fisik_pagi'] !== null && $row['stock_fisik_pagi'] !== '')
                    || (array_key_exists('stok_fisik_pagi', $row) && $row['stok_fisik_pagi'] !== null && $row['stok_fisik_pagi'] !== '')
                    || (array_key_exists('fisik_pagi', $row) && $row['fisik_pagi'] !== null && $row['fisik_pagi'] !== '');

                $hasFisikMalamInput = (array_key_exists('stock_fisik_malam', $row) && $row['stock_fisik_malam'] !== null && $row['stock_fisik_malam'] !== '')
                    || (array_key_exists('stok_fisik_malam', $row) && $row['stok_fisik_malam'] !== null && $row['stok_fisik_malam'] !== '')
                    || (array_key_exists('fisik_malam', $row) && $row['fisik_malam'] !== null && $row['fisik_malam'] !== '');

                $hasStockAwal = (array_key_exists('stock_awal', $row) && $row['stock_awal'] !== null && $row['stock_awal'] !== '')
                    || (array_key_exists('stok_awal', $row) && $row['stok_awal'] !== null && $row['stok_awal'] !== '');

                $hasStockToko = (array_key_exists('stock_toko', $row) && $row['stock_toko'] !== null && $row['stock_toko'] !== '')
                    || (array_key_exists('stok_toko', $row) && $row['stok_toko'] !== null && $row['stok_toko'] !== '');

                // Jika identifier kosong ATAU tidak ada satu pun field bernilai/diisi, skip
                if ($identifier === '' || (!$hasProduksiInput && !$hasKetLainInput && !$hasKetRusakInput && !$hasFisikPagiInput && !$hasFisikMalamInput && !$hasStockAwal && !$hasStockToko)) {
                    continue;
                }

                $item = Item::where('code', $identifier)
                    ->orWhere('nama_item', $identifier)
                    ->first();

                if ($item) {
                    // 2. Ambil data Produksi & Opname existing (ambil record transaksi TERAKHIR hari ini)
                    $produksi = Produksi::where('item_id', $item->id)
                        ->whereDate('created_at', $targetDate)
                        ->orderBy('id', 'desc')
                        ->first();

                    $opname = Opname::where('item_id', $item->id)
                        ->whereDate('tanggal', $targetDate)
                        ->orderBy('id', 'desc')
                        ->first();

                    // 3. Cari stock awal fallback jika belum ada data produksi pada tanggal terkait
                    if ($hasStockAwal) {
                        $rawStockAwal = $row['stock_awal'] ?? $row['stok_awal'];
                        $stockAwal = (float)$rawStockAwal;
                    } elseif ($produksi) {
                        $stockAwal = (float)$produksi->stock_awal;
                    } else {
                        $lastProduksi = Produksi::where('item_id', $item->id)
                            ->whereDate('created_at', '<', $targetDate)
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($lastProduksi) {
                            $stockAwal = (float)($lastProduksi->sisa_stock ?? $lastProduksi->stock_awal);
                        } else {
                            $stockAwal = (float)($item->stock ?? 0);
                        }
                    }

                    // 4. Kalkulasi Nilai Produksi, Ket Lain (Airmadidi), Ket Rusak
                    if ($hasProduksiInput) {
                        $rawProduksi = $row['realisasi'] ?? $row['produksi1'] ?? $row['produksi'] ?? 0;
                        $produksi1 = (float)$rawProduksi;
                    } else {
                        $produksi1 = $produksi ? (float)$produksi->produksi1 : 0;
                    }

                    if ($hasKetLainInput) {
                        $rawKetLain = $row['ket_lain'] ?? $row['airmadidi'] ?? $row['lain'] ?? 0;
                        $ketLain = (float)$rawKetLain;
                    } else {
                        $ketLain = $produksi ? (float)$produksi->ket_lain : 0;
                    }

                    if ($hasKetRusakInput) {
                        $rawKetRusak = $row['ket_rusak'] ?? $row['rusak'] ?? 0;
                        $ketRusak = (float)$rawKetRusak;
                    } else {
                        $ketRusak = $produksi ? (float)$produksi->ket_rusak : 0;
                    }

                    $produksi2     = $produksi ? (float)$produksi->produksi2 : 0;
                    $produksi3     = $produksi ? (float)$produksi->produksi3 : 0;
                    $totalProduksi = $produksi1 + $produksi2 + $produksi3;
                    $totalLain     = $ketLain;
                    $terjual       = $produksi ? (float)$produksi->total_penjualan : 0;

                    $sisaStock     = $stockAwal + $totalProduksi - $terjual - $ketLain - $ketRusak;
                    $stockAkhir    = $sisaStock; // Mengikuti formula sisa stok akhir

                    // 5. Save/Update Tabel Produksi
                    if ($produksi) {
                        $produksi->update([
                            'stock_awal'     => $stockAwal,
                            'produksi1'      => $produksi1,
                            'total_produksi' => $totalProduksi,
                            'ket_lain'       => $ketLain,
                            'ket_rusak'      => $ketRusak,
                            'total_lain'     => $totalLain,
                            'sisa_stock'     => $sisaStock,
                        ]);
                    } else {
                        $produksi = Produksi::create([
                            'item_id'             => $item->id,
                            'created_at'          => $targetDate . ' ' . Carbon::now()->format('H:i:s'),
                            'stock_awal'          => $stockAwal,
                            'produksi1'           => $produksi1,
                            'produksi2'           => 0,
                            'produksi3'           => 0,
                            'total_produksi'      => $totalProduksi,
                            'penjualan_toko'      => 0,
                            'penjualan_pemesanan' => 0,
                            'total_penjualan'     => 0,
                            'ket_rusak'           => $ketRusak,
                            'ket_lain'            => $ketLain,
                            'total_lain'          => $totalLain,
                            'catatan'             => 'tidak ada catatan',
                            'sisa_stock'          => $sisaStock,
                        ]);
                    }

                    // 6. Kalkulasi & Simpan Nilai Stok Fisik Opname (Pastikan tidak null untuk menghindari error NOT NULL MySQL)
                    $opnamePayload = [
                        'stock_masuk' => $totalProduksi,
                        'stock_akhir' => $stockAkhir,
                    ];

                    if ($hasFisikPagiInput) {
                        $opnamePayload['stock_fisik_pagi'] = (float)($row['stock_fisik_pagi'] ?? $row['stok_fisik_pagi'] ?? $row['fisik_pagi']);
                    } elseif ($opname && !is_null($opname->stock_fisik_pagi)) {
                        $opnamePayload['stock_fisik_pagi'] = $opname->stock_fisik_pagi;
                    } else {
                        $opnamePayload['stock_fisik_pagi'] = 0;
                    }

                    if ($hasFisikMalamInput) {
                        $opnamePayload['stock_fisik_malam'] = (float)($row['stock_fisik_malam'] ?? $row['stok_fisik_malam'] ?? $row['fisik_malam']);
                    } elseif ($opname && !is_null($opname->stock_fisik_malam)) {
                        $opnamePayload['stock_fisik_malam'] = $opname->stock_fisik_malam;
                    } else {
                        $opnamePayload['stock_fisik_malam'] = 0;
                    }

                    if ($hasStockToko) {
                        $opnamePayload['stock_toko'] = (float)($row['stock_toko'] ?? $row['stok_toko']);
                    } elseif ($opname && !is_null($opname->stock_toko)) {
                        $opnamePayload['stock_toko'] = $opname->stock_toko;
                    } else {
                        $opnamePayload['stock_toko'] = 0;
                    }

                    Opname::updateOrCreate(
                        [
                            'item_id' => $item->id,
                            'tanggal' => $targetDate,
                        ],
                        $opnamePayload
                    );

                    // 7. Update Master Stok jika tanggal transaksi hari ini
                    if ($targetDate === $todayStr) {
                        $item->update([
                            'stock' => $sisaStock
                        ]);
                    }

                    $processed++;
                }
            }

            return response()->json([
                'status'    => 'success',
                'processed' => $processed
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    // --- 4. AMBIL/LOAD TOTAL PRODUKSI & AIRMADIDI ---
    private function loadAirmadidiData(Request $request)
    {
        $date = $request->input('date') ?? $request->query('date', Carbon::today()->format('Y-m-d'));

        // 1. Ambil seluruh item yang aktif
        $items = Item::where('status_aktif', '1')->get();

        // 2. Ambil data opname pada tanggal terkait
        $opnameData = Opname::whereDate('tanggal', $date)
            ->get()
            ->keyBy('item_id');

        $result = [];

        foreach ($items as $item) {
            $itemId = $item->id;
            $opname = $opnameData->get($itemId);

            // Cari transaksi produksi PADA tanggal terpilih (ambil record transaksi TERAKHIR)
            $produksi = Produksi::where('item_id', $itemId)
                ->whereDate('created_at', $date)
                ->orderBy('id', 'desc')
                ->first();

            if ($produksi) {
                $stockAwal  = (float)$produksi->stock_awal;
                $produksi1  = (float)$produksi->produksi1;
                $terjual    = (float)$produksi->total_penjualan;
                $ketLain    = (float)$produksi->ket_lain;
                $ketRusak   = (float)$produksi->ket_rusak;
                $stockAkhir = (float)($stockAwal + $produksi1 - $terjual - $ketLain - $ketRusak);
            } else {
                // Jika belum ada transaksi hari ini, cari stok dari transaksi produksi terakhir
                $lastProduksi = Produksi::where('item_id', $itemId)
                    ->whereDate('created_at', '<', $date)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastProduksi) {
                    $stockAwal = (float)($lastProduksi->sisa_stock ?? $lastProduksi->stock_awal);
                } else {
                    $stockAwal = (float)($item->stock ?? 0);
                }

                $produksi1  = 0;
                $terjual    = 0;
                $ketLain    = 0;
                $ketRusak   = 0;
                $stockAkhir = $stockAwal;
            }

            // Ambil data stok fisik dari Opname
            $stockFisikPagi  = ($opname && !is_null($opname->stock_fisik_pagi))  ? (float)$opname->stock_fisik_pagi  : null;
            $stockFisikMalam = ($opname && !is_null($opname->stock_fisik_malam)) ? (float)$opname->stock_fisik_malam : null;
            $stockToko       = ($opname && !is_null($opname->stock_toko))        ? (float)$opname->stock_toko        : null;

            $selisihPagi  = !is_null($stockFisikPagi)  ? ($stockAwal - $stockFisikPagi)   : null;
            $selisihMalam = !is_null($stockFisikMalam) ? ($stockAkhir - $stockFisikMalam) : null;

            // ✅ FORMAT JSON LENGKAP MENCERMINKAN LAPORAN OPNAME
            $result[] = [
                'code'              => trim((string)($item->code ?? $item->kode_item)),
                'nama_item'         => trim((string)$item->nama_item),
                'stock_awal'        => $stockAwal,
                'stock_asli'        => $stockAkhir,
                'sisa_stock'        => $stockAkhir,
                'stock_fisik_pagi'  => $stockFisikPagi,
                'selisih_pagi'      => $selisihPagi,
                'produksi'          => $produksi1,
                'produksi1'         => $produksi1,
                'produksi_bitung'   => max(0, $produksi1 - $ketLain),
                'rusak'             => $ketRusak,
                'ket_rusak'         => $ketRusak,
                'airmadidi'         => $ketLain,
                'ket_lain'          => $ketLain,
                'terjual'           => $terjual,
                'penjualan_toko'    => $terjual,
                'stock_akhir'       => $stockAkhir,
                'stock_fisik_malam' => $stockFisikMalam,
                'selisih_malam'     => $selisihMalam,
                'stock_toko'        => $stockToko,
            ];
        }

        return response()->json([
            'status' => 'success',
            'date'   => $date,
            'data'   => $result
        ], 200);
    }
}