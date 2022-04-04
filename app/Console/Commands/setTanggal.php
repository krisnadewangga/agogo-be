<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Produksi;
use App\Kas;
use App\Item;
use Carbon\Carbon;

class setTanggal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setTanggal:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'untuk set tanggal otomatis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $tgl_skrang = Carbon::now()->format('Y-m-d');
        $cek = Produksi::whereDate('created_at',$tgl_skrang)->count();
            
        if($cek == 0){
            $cek_kas = Kas::where('status','0')->get();
            $belum_tutup_kas = $cek_kas->count();

            if($belum_tutup_kas > 0){
                $nama_kasir = "<ul>";
                foreach($cek_kas as $key){
                  $nama_kasir .= '<li>'.$key->User->name.'</li>';
                }
                $nama_kasir .= "</ul>";
                
                 \Log::info($nama_kasir);
            }else{
                $item = Item::select('id','stock as sisa_stock')->where('status_aktif','1')->get();
                
            
                  
                foreach ($item as $key ) {
                  if($key['sisa_stock'] < 0){
                    $sisa_stock = 0;
                    Item::where('id',$key['id'])->update(['stock' => '0']);
                    
                  }else{
                    $sisa_stock = $key['sisa_stock'];
                  }

                  $array = ['item_id' => $key['id'],
                             'produksi1' => 0,
                             'produksi2' => 0,
                             'produksi3' => 0,
                             'total_produksi' => 0,
                             'penjualan_toko' => 0,
                             'penjualan_pemesanan' => 0,
                             'total_penjualan' => 0,
                             'ket_rusak' => 0,
                             'ket_lain' => 0,
                             'total_lain' => 0,
                             'catatan' => 'tidak ada catatan',
                             'stock_awal' => $sisa_stock,
                             'sisa_stock' => $sisa_stock
                           ];

                  Produksi::create($array);
                }
                \Log::info("Successfully Set Tanggal ".$tgl_skrang);
            }
        }else{
            \Log::info("Sudah Set Tanggal ".$tgl_skrang);
        }
    }
}
