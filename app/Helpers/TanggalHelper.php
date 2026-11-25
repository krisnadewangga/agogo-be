<?php

namespace App\Helpers;

use App\Produksi;
use App\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TanggalHelper
{
    /**
     * Otomatis cek dan set tanggal produksi hari ini jika belum ada.
     * Menggunakan cache harian agar tidak membebani database pada setiap request.
     *
     * @return bool
     */
    public static function autoSetTanggal()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $cacheKey = 'tanggal_produksi_set_' . $today;

            // Jika cache hari ini sudah ada, skip (0 query database)
            if (Cache::has($cacheKey)) {
                return false;
            }

            // Cek apakah tanggal hari ini sudah diset di tabel produksi
            $cek = Produksi::whereDate('created_at', $today)->count();
            if ($cek > 0) {
                Cache::put($cacheKey, true, Carbon::now()->endOfDay());
                return false;
            }

            // Jalankan inisialisasi tanggal baru
            $result = self::executeSetTanggal($today);

            Cache::put($cacheKey, true, Carbon::now()->endOfDay());
            return $result;
        } catch (\Exception $e) {
            Log::error("Gagal auto set tanggal: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eksekusi inisialisasi record produksi untuk tanggal tertentu.
     * Stok awal diambil dari stok terkini tabel Item (hasil opname / transaksi kemarin).
     *
     * @param string|null $today
     * @return bool
     */
    public static function executeSetTanggal($today = null)
    {
        $today = $today ?? Carbon::now()->format('Y-m-d');

        // Pastikan tidak dobel
        $cek = Produksi::whereDate('created_at', $today)->count();
        if ($cek > 0) {
            return false;
        }

        $items = Item::select('id', 'stock as sisa_stock')
            ->where('status_aktif', '1')
            ->get();

        foreach ($items as $key) {
            if ($key['sisa_stock'] < 0) {
                $sisa_stock = 0;
                Item::where('id', $key['id'])->update(['stock' => '0']);
            } else {
                $sisa_stock = $key['sisa_stock'];
            }

            $array = [
                'item_id'             => $key['id'],
                'produksi1'           => 0,
                'produksi2'           => 0,
                'produksi3'           => 0,
                'total_produksi'      => 0,
                'penjualan_toko'      => 0,
                'penjualan_pemesanan' => 0,
                'total_penjualan'     => 0,
                'ket_rusak'           => 0,
                'ket_lain'            => 0,
                'total_lain'          => 0,
                'catatan'             => 'tidak ada catatan',
                'stock_awal'          => $sisa_stock,
                'sisa_stock'          => $sisa_stock,
                'created_at'          => $today . ' 00:00:00',
                'updated_at'          => Carbon::now(),
            ];

            Produksi::create($array);
        }

        Log::info("Successfully Auto Set Tanggal Produksi: " . $today);
        return true;
    }
}
