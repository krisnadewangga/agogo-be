<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SendNotif;
use App\Transaksi;
use App\BatalPesanan;
use App\User;
use App\Notifikasi;
use Carbon\Carbon;


class autoBatal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoBatal:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk Pembatalan Transaksi Yang Sudah Melewati Batas Waktu Tenggang';

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

        $transaksi = Transaksi::select('id','no_transaksi','waktu_kirim','metode_pembayaran', 'status','user_id')
                    ->where(function($q){
                        return $q->where([
                                            ['status', '=' , '6'],
                                            ['waktu_kirim', '<' , Carbon::now()->format('Y-m-d H:i:s')],
                                            ['metode_pembayaran','=','2']
                                        ]);
                    })->orWhere(function($a){
                        return $a->where([
                                            ['status', '=' , '1'],
                                            ['waktu_kirim', '<' , Carbon::now()->format('Y-m-d H:i:s')],
                                            ['metode_pembayaran','=','3'],
                                            ['jalur','=','1']
                                        ]);
                    })->get();
        $loadNot = 0;
        if($transaksi->count() > 0){
            foreach ($transaksi as $key ) {
                $update = Transaksi::where('id',$key->id)->update(['status' => '3']);
                $batal_pesanan = BatalPesanan::create(['transaksi_id' => $key->id, 'input_by' => 'Automatic System' ]);
                $this->setKunciTransaksi($key->user_id);

                $dnotif = [
                    'pengirim_id' => 1,
                    'penerima_id' => $key->user_id,
                    'judul_id' => $key->id,
                    'judul' => 'Pembatalan Pesanan Nomor Transaksi '.$key->no_transaksi,
                    'isi' => 'Pesanan Dengan Nomor Transaksi '.$key->no_transaksi.' Telah Dibatalkan',
                    'jenis_notif' => 6,
                    'dibaca' => '0'
                ];
                
                $notif = Notifikasi::create($dnotif);
                //NotifGCM
                SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

                \Log::info("Successfully Run Auto Batal No Transaksi ".$key->no_transaksi);
            }
            $loadNot = 1;
        }
        
        if($loadNot == 1){
            SendNotif::SendNotPesan('5',['jenisNotif' => '5']);
        }
        
        $this->info('autoBatal:cron Cummand Run successfully!');
    }

    public function setKunciTransaksi($user_id)
    {
        $sel_user = User::findOrFail($user_id);
        $transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
        $kunci_transaksi = $sel_user->DetailKonsumen->kunci_transaksi;

        if($transaksi_berlangsung < '3' && $kunci_transaksi == '1'){
            $sel_user->DetailKonsumen()->update(['kunci_transaksi' => '0']);
        }
        
        $success = 1;
        return $success;
    }
}
