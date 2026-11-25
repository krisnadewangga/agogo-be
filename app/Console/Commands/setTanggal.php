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
        $cek = Produksi::whereDate('created_at', $tgl_skrang)->count();
            
        if ($cek == 0) {
            \App\Helpers\TanggalHelper::executeSetTanggal($tgl_skrang);
            \Log::info("Successfully Set Tanggal ".$tgl_skrang);
        } else {
            \Log::info("Sudah Set Tanggal ".$tgl_skrang);
        }
    }
}
