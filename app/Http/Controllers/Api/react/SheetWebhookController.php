<?php

namespace App\Http\Controllers\Api\react; // ✅ Sesuaikan namespace dengan lokasi folder

use App\Http\Controllers\Controller; // Jangan lupa import Controller utama
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\TargetProduksi;
use App\Produksi;

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
                    'status' => 'error',
                    'message' => 'Action tidak valid atau belum ditentukan!'
                ], 400);
        }
    }

    // --- 1. SIMPAN TARGET PRODUKSI ---
    private function saveTargetData(Request $request)
    {
        $rows = $request->input('data', []);
        $processed = 0;

        foreach ($rows as $row) {
            // Logika simpan/update TargetProduksi
            // Example:
            // TargetProduksi::updateOrCreate([...]);
            $processed++;
        }

        return response()->json([
            'status' => 'success',
            'processed' => $processed
        ], 200);
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
            $todayStr = Carbon::today()->format('Y-m-d'); // Tanggal hari ini (YYYY-MM-DD)

            foreach ($rows as $row) {
                $code = trim((string) ($row['code'] ?? ''));
                $targetDate = $row['target_date'] ?? $todayStr;

                // Ambil raw input dari payload Apps Script
                $hasProduksiInput = array_key_exists('realisasi', $row) && $row['realisasi'] !== '' && $row['realisasi'] !== null;
                $hasKetLainInput  = array_key_exists('ket_lain', $row) && $row['ket_lain'] !== '' && $row['ket_lain'] !== null;

                // Lewati jika kedua kolom tidak diisi di sheet
                if ($code === '' || (!$hasProduksiInput && !$hasKetLainInput)) {
                    continue;
                }

                $item = \App\Item::where('code', $code)
                    ->first();

                if ($item) {
                    // 1. Cari record produksi pada tanggal tersebut
                    $produksi = Produksi::where('item_id', $item->id)
                        ->whereDate('created_at', $targetDate)
                        ->first();

                    // 2. Tentukan nilai dasar
                    $stockAwal = $produksi ? (float)$produksi->stock_awal : (float)($item->stock ?? 0);
                    $produksi1 = $hasProduksiInput ? (float)$row['realisasi'] : ($produksi ? (float)$produksi->produksi1 : 0);
                    $ketLain    = $hasKetLainInput  ? (float)$row['ket_lain']  : ($produksi ? (float)$produksi->ket_lain  : 0);

                    // 3. Hitung sisa_stock & total
                    $sisaStock     = $stockAwal + $produksi1 - $ketLain;
                    $totalProduksi = $produksi1;
                    $totalLain     = $ketLain;

                    // 4. Simpan / Update ke tabel produksi
                    Produksi::updateOrCreate(
                        [
                            'item_id'    => $item->id,
                            'created_at' => $targetDate . ' ' . Carbon::now()->format('H:i:s'),
                        ],
                        [
                            'stock_awal'          => $stockAwal,
                            'produksi1'           => $produksi1,
                            'produksi2'           => $produksi ? $produksi->produksi2 : 0,
                            'produksi3'           => $produksi ? $produksi->produksi3 : 0,
                            'total_produksi'      => $totalProduksi,
                            'penjualan_toko'      => $produksi ? $produksi->penjualan_toko : 0,
                            'penjualan_pemesanan' => $produksi ? $produksi->penjualan_pemesanan : 0,
                            'total_penjualan'     => $produksi ? $produksi->total_penjualan : 0,
                            'ket_rusak'           => $produksi ? $produksi->ket_rusak : 0,
                            'ket_lain'            => $ketLain,
                            'total_lain'          => $totalLain,
                            'catatan'             => $produksi ? $produksi->catatan : 'tidak ada catatan',
                            'sisa_stock'          => $sisaStock,
                        ]
                    );

                    // 5. ✅ UPDATE STOK DI TABEL ITEM HANYA JIKA TANGGAL LAPORAN ADALAH HARI INI
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

        $produksiData = Produksi::with('item')
            ->whereDate('created_at', $date)
            ->get();

        $result = [];
        foreach ($produksiData as $p) {
            if ($p->item) {
                $result[] = [
                    'code'      => trim((string)($p->item->code ?? $p->item->kode_item)),
                    'nama_item' => trim((string)$p->item->nama_item),
                    'produksi1' => (float)$p->produksi1,
                    'ket_lain'  => (float)$p->ket_lain,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'date'   => $date,
            'data'   => $result
        ], 200);
    }
}