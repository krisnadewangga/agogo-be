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
                $identifier = trim((string) ($row['code'] ?? ''));
                $targetProduksi = array_key_exists('target', $row) ? $row['target'] : ($row['target_produksi'] ?? null);
                $targetDate = $row['target_date'] ?? Carbon::today()->format('Y-m-d');

                if ($identifier === '' || $targetProduksi === null || $targetProduksi === '') {
                    continue;
                }

                $item = Item::where('nama_item', $identifier)
                    ->orWhere('code', $identifier)
                    ->first();

                if ($item) {
                    $formattedTargetDate = Carbon::parse($targetDate)->format('Y-m-d 00:00:00');

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
                $code = trim((string) ($row['code'] ?? ''));
                $targetDate = $row['target_date'] ?? $todayStr;

                // 1. Cek ketersediaan input dari payload Google Apps Script (Termasuk ket_rusak)
                $hasProduksiInput   = array_key_exists('realisasi', $row) && $row['realisasi'] !== null && $row['realisasi'] !== '';
                $hasKetLainInput    = array_key_exists('ket_lain', $row) && $row['ket_lain'] !== null && $row['ket_lain'] !== '';
                $hasKetRusakInput   = array_key_exists('ket_rusak', $row) && $row['ket_rusak'] !== null && $row['ket_rusak'] !== '';
                $hasFisikPagiInput  = array_key_exists('stock_fisik_pagi', $row) && $row['stock_fisik_pagi'] !== null && $row['stock_fisik_pagi'] !== '';
                $hasFisikMalamInput = array_key_exists('stock_fisik_malam', $row) && $row['stock_fisik_malam'] !== null && $row['stock_fisik_malam'] !== '';
                $hasStockAwal  = array_key_exists('stock_awal', $row) && $row['stock_awal'] !== null && $row['stock_awal'] !== '';

                // Jika kode kosong ATAU tidak ada satu pun field bernilai/diisi, skip
                if ($code === '' || (!$hasProduksiInput && !$hasKetLainInput && !$hasKetRusakInput && !$hasFisikPagiInput && !$hasFisikMalamInput && !$hasStockAwal)) {
                    continue;
                }

                $item = Item::where('code', $code)->first();

                if ($item) {
                    // 2. Ambil data Produksi & Opname existing
                    $produksi = Produksi::where('item_id', $item->id)
                        ->whereDate('created_at', $targetDate)
                        ->first();

                    $opname = Opname::where('item_id', $item->id)
                        ->whereDate('tanggal', $targetDate)
                        ->first();

                    // 3. Kalkulasi Nilai Produksi & Stok Rusak
                    $stockAwal   = $hasStockAwal  ? (float)$row['stock_awal'] : ($produksi ? (float)$produksi->stock_awal : 0); // ✅ Fix Ambil Input
                    $produksi1  = $hasProduksiInput  ? (float)$row['realisasi'] : ($produksi ? (float)$produksi->produksi1 : 0);
                    $ketLain    = $hasKetLainInput   ? (float)$row['ket_lain']  : ($produksi ? (float)$produksi->ket_lain  : 0);
                    $ketRusak   = $hasKetRusakInput  ? (float)$row['ket_rusak'] : ($produksi ? (float)$produksi->ket_rusak : 0); // ✅ Fix Ambil Input
                    
                    $terjual    = $produksi ? (float)$produksi->total_penjualan : 0;

                    $sisaStock     = $stockAwal + $produksi1 - $terjual - $ketLain - $ketRusak;
                    $stockAkhir    = $sisaStock; // Mengikuti formula sisa stok akhir
                    $totalProduksi = $produksi1;
                    $totalLain     = $ketLain;

                    // 4. Save/Update Tabel Produksi
                    if ($produksi) {
                        $produksi->update([
                            'stock_awal'     => $stockAwal,
                            'produksi1'      => $produksi1,
                            'total_produksi' => $totalProduksi,
                            'ket_lain'       => $ketLain,
                            'ket_rusak'      => $ketRusak, // ✅ Fix Update Data
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
                            'ket_rusak'           => $ketRusak, // ✅ Fix Simpan Data Baru
                            'ket_lain'            => $ketLain,
                            'total_lain'          => $totalLain,
                            'catatan'             => 'tidak ada catatan',
                            'sisa_stock'          => $sisaStock,
                        ]);
                    }

                    // 5. Kalkulasi & Simpan Nilai Stok Fisik Opname
                    $stockFisikPagi = $hasFisikPagiInput 
                        ? (float)$row['stock_fisik_pagi'] 
                        : ($opname ? $opname->stock_fisik_pagi : $stockAwal);

                    $stockFisikMalam = $hasFisikMalamInput 
                        ? (float)$row['stock_fisik_malam'] 
                        : ($opname ? $opname->stock_fisik_malam : $sisaStock);

                    Opname::updateOrCreate(
                        [
                            'item_id' => $item->id,
                            'tanggal' => $targetDate,
                        ],
                        [
                            'stock_masuk'       => $produksi1,
                            'stock_akhir'       => $stockAkhir,
                            'stock_toko'        => $sisaStock,
                            'stock_fisik_pagi'  => $stockFisikPagi,
                            'stock_fisik_malam' => $stockFisikMalam,
                        ]
                    );

                    // 6. Update Master Stok jika tanggal transaksi hari ini
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

            // Cari transaksi produksi PADA tanggal terpilih
            $produksi = Produksi::where('item_id', $itemId)
                ->whereDate('created_at', $date)
                ->first();

            if ($produksi) {
                $stockAwal  = (float)$produksi->stock_awal;
                $produksi1  = (float)$produksi->produksi1;
                $terjual    = (float)$produksi->total_penjualan;
                $ketLain    = (float)$produksi->ket_lain;
                $ketRusak   = (float)$produksi->ket_rusak;
                $stockAkhir = (float)($produksi->sisa_stock ?? ($stockAwal + $produksi1 - $terjual - $ketLain - $ketRusak));
            } else {
                // Jika belum ada transaksi hari ini, cari stok dari transaksi produksi terakhir
                $lastProduksi = Produksi::where('item_id', $itemId)
                    ->whereDate('created_at', '<', $date)
                    ->orderBy('created_at', 'desc')
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
            $stockFisikPagi  = ($opname && !is_null($opname->stock_fisik_pagi))  ? (float)$opname->stock_fisik_pagi  : $stockAwal;
            $stockFisikMalam = ($opname && !is_null($opname->stock_fisik_malam)) ? (float)$opname->stock_fisik_malam : null;

            // ✅ FORMAT JSON LENGKAP UNTUK GOOGLE SHEETS
            $result[] = [
                'code'              => trim((string)($item->code ?? $item->kode_item)),
                'nama_item'         => trim((string)$item->nama_item),
                'stock_awal'        => $stockAwal,
                'produksi1'         => $produksi1,
                'penjualan_toko'    => $terjual,
                'ket_lain'          => $ketLain,
                'ket_rusak'         => $ketRusak,
                'stock_akhir'       => $stockAkhir,
                'sisa_stock'        => $stockAkhir,
                'stock_fisik_pagi'  => $stockFisikPagi,
                'stock_fisik_malam' => $stockFisikMalam,
            ];
        }

        return response()->json([
            'status' => 'success',
            'date'   => $date,
            'data'   => $result
        ], 200);
    }
}