<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Transaksi;

class HapusTransaksi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hapusTransaksi:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $tanggal_sekarang = Carbon::now()->format('Y-m-d');
        $tanggal_akhirbulan = Carbon::now()->endOfMonth()->format('Y-m-d');

        if($tanggal_sekarang == $tanggal_akhirbulan){
            $transaksi = Transaksi::where('status','=','3')->delete();

            \Log::info("Successfully Run Hapus Transaksi Cron");
        }
        
    }
}
