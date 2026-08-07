<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use App\Item;
use App\TargetProduksi;
use App\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SheetWebhookController extends Controller
{
    public function store(Request $request)
    {
        $payload = $request->all();
        $rows = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $rows = $payload['data'];
        } elseif (isset($payload['rows']) && is_array($payload['rows'])) {
            $rows = $payload['rows'];
        } elseif ($request->has('payload') && is_array($request->input('payload'))) {
            $rows = $request->input('payload');
        } else {
            $rows = [$payload];
        }

        $processed = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $item = $this->resolveItem($row);
            if (!$item) {
                continue;
            }

            $targetValue = $this->resolveTargetValue($row);
            $realisasiValue = $this->extractValue($row, ['realisasi', 'realisasi_produksi', 'produksi1']);
            $targetDate  = $this->resolveTargetDate($row);

            // 1. OLAH DATA TARGET PRODUKSI
            if ($targetValue !== null) {
                $recordTarget = TargetProduksi::where('item_id', $item->id)
                    ->whereDate('target_date', $targetDate)
                    ->first();

                if ($recordTarget) {
                    $recordTarget->update([
                        'target_produksi' => $targetValue,
                    ]);
                } else {
                    TargetProduksi::create([
                        'item_id' => $item->id,
                        'target_produksi' => $targetValue,
                        'target_date' => $targetDate,
                    ]);
                }
            }

            // 2. OLAH DATA REALISASI PRODUKSI (Masuk ke tabel 'produksi' kolom 'produksi1')
            if ($realisasiValue !== null && $realisasiValue !== '') {
                $realisasiVal = (float) str_replace([',', ' '], '', (string) $realisasiValue);

                // Cari record produksi berdasarkan item_id dan tanggal
                $recordProduksi = Produksi::where('item_id', $item->id)
                    ->whereDate('created_at', $targetDate)
                    ->first();

                if ($recordProduksi) {
                    // --- 1. JIKA RECORD SUDAH ADA (UPDATE) ---
                    $produksi1 = $realisasiVal;
                    $totalProduksi = $produksi1 + ($recordProduksi->produksi2 ?? 0) + ($recordProduksi->produksi3 ?? 0);
                    $stockAwal = $recordProduksi->stock_awal ?? 0;
                    
                    // Rumus sisa stock: total_produksi + stock_awal
                    $sisaStock = $totalProduksi + $stockAwal;

                    $recordProduksi->update([
                        'produksi1'      => $produksi1,
                        'total_produksi' => $totalProduksi,
                        'sisa_stock'     => $sisaStock,
                    ]);
                } else {
                    // --- 2. JIKA RECORD BELUM ADA (CREATE NEW) ---
                    $produksi1 = $realisasiVal;
                    $produksi2 = 0;
                    $produksi3 = 0;
                    $totalProduksi = $produksi1 + $produksi2 + $produksi3;
                    
                    // Mengambil stock_awal dari data tabel `item` (atau 0 jika tidak ada)
                    $stockAwal = $item->stock ?? 0;
                    
                    // Rumus sisa stock: total_produksi + stock_awal
                    $sisaStock = $totalProduksi + $stockAwal;

                    $produksi = new Produksi();
                    $produksi->item_id             = $item->id;
                    $produksi->produksi1          = $produksi1;
                    $produksi->produksi2          = $produksi2;
                    $produksi->produksi3          = $produksi3;
                    $produksi->total_produksi     = $totalProduksi;
                    $produksi->penjualan_toko     = 0;
                    $produksi->penjualan_pemesanan= 0;
                    $produksi->total_penjualan    = 0;
                    $produksi->ket_rusak          = 0;
                    $produksi->ket_lain           = 0;
                    $produksi->total_lain          = 0;
                    $produksi->catatan            = 'tidak ada catatan';
                    $produksi->stock_awal         = $stockAwal;
                    $produksi->sisa_stock         = $sisaStock;
                    $produksi->created_at         = $targetDate . ' ' . date('H:i:s');
                    $produksi->save();
                }
            }

            $processed++;
        }

        return response()->json([
            'status' => 'success',
            'processed' => $processed,
            'message' => 'Target dan realisasi produksi berhasil diproses',
        ], 200);
    }

    protected function resolveItem($row)
    {
        // Ambil nilai yang dikirim dari Google Sheets
        $value = $this->extractValue($row, ['item_id', 'id', 'kode_item', 'kode', 'code', 'item_code', 'nama', 'nama_item']);

        if ($value === null || $value === '') {
            return null;
        }

        $cleanValue = trim((string) $value);

        // 1. Jika bernilai angka dan cocok dengan ID primary key
        if (is_numeric($cleanValue)) {
            $item = Item::find((int) $cleanValue);
            if ($item) return $item;
        }

        // 2. Cari berdasarkan kolom 'nama_item' ATAU kolom 'code'
        return Item::where('nama_item', $cleanValue)
            ->orWhere('code', $cleanValue)
            ->first();
    }

    protected function resolveTargetValue($row)
    {
        $value = $this->extractValue($row, [
            'target_produksi',
            'target',
            'target_produksi_value',
            'target_value',
            'target_product',
        ]);

        if ($value === null || $value === '') {
            return null;
        }

        return (float) str_replace([',', ' '], '', (string) $value);
    }

    protected function resolveTargetDate($row)
    {
        $value = $this->extractValue($row, ['tanggal', 'target_date', 'date', 'tanggal_target']);

        if ($value) {
            return Carbon::parse($value)->format('Y-m-d');
        }

        return Carbon::today()->format('Y-m-d');
    }

    protected function extractValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }

            $upperKey = strtoupper($key);
            if (array_key_exists($upperKey, $row)) {
                return $row[$upperKey];
            }

            $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if (array_key_exists($camelKey, $row)) {
                return $row[$camelKey];
            }
        }

        return null;
    }
}
