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
        $rows = $request->input('data', []);
        $processed = 0;

        foreach ($rows as $row) {
            // Logika simpan/update Produksi (produksi1 & ket_lain)
            $processed++;
        }

        return response()->json([
            'status' => 'success',
            'processed' => $processed
        ], 200);
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