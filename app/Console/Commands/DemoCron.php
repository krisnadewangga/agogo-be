<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SendNotif;
use App\NotifExpired;
use App\Notifikasi;
use Carbon\Carbon;
use Auth;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

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

        // $select = NotifExpired::where('waktu_kirim', '<', Carbon::now()->format('Y-m-d H:i:s') )->where('status','=','0')->get();
        
        
	$select = NotifExpired::join('transaksi','notif_expired.transaksi_id','=','transaksi.id')
	->whereNotIn('transaksi.status',['5','3'] )
	->where('notif_expired.waktu_kirim', '<', Carbon::now()->format('Y-m-d H:i:s') )
	->where('notif_expired.status','=','0')->get();

        $count = $select->count();
        //dd($count);

        if($count > 0){
            foreach($select as $key){
                if($key->Transaksi->metode_pembayaran == '2'){
                   
                    $pesanWa = "Anda Belum Melakukan Pembayaran Untuk Pesanan Nomor Transaksi ".$key->Transaksi->no_transaksi." Segera Lakukan Pembayaran. Batas Waktu Pembayaran ".$key->Transaksi->waktu_kirim->format('d/m/Y H:i A')." Pesanan Akan Dibatalkan Apabila Sampai Dengan Batas Waktu Yang Telah Ditentukan Anda Belum Melakukan Transfer Pembayaran";
                    $dnotif = ['pengirim_id' => '1',
                                'penerima_id' => $key->Transaksi->user_id,
                                'judul_id' => $key->transaksi_id,
                                'judul' => 'Pembayaran Pesanan '.$key->Transaksi->no_transaksi,
                                'isi' => $pesanWa,
                                'jenis_notif' => 9,
                                'dibaca' => '0'
                               ];
                               \Log::info("status awal");
                }else{
                               
                    $pesanWa = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi ".$key->Transaksi->no_transaksi." Dengan Metode Pembayaran Bayar Di Toko . Batas Waktu Pengambilan Pesanan ".$key->Transaksi->waktu_kirim->format('d/m/Y H:i A')." Pesanan Akan Dibatalkan Apabila Sampai Dengan Batas Waktu Yang Telah Ditentukan Anda Belum Mengambil Pesanan";
                    $dnotif =[
                                'pengirim_id' => '1',
                                'penerima_id' => $key->Transaksi->user_id,
                                'judul_id' => $key->transaksi_id,
                                'judul' => 'Pesanan No '.$key->Transaksi->no_transaksi,
                                'isi' => $pesanWa,
                                'jenis_notif' => 1,
                                'dibaca' => '0'
                                ];
                                \Log::info("status awal");
                }
                
                $noHp = $key->email;
                
                $notif = Notifikasi::create($dnotif);
                $sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pembayaran Pesanan', $notif->judul_id);
                $a = SendNotif::sendNotifWa($noHp,$pesanWa);   
                $update = NotifExpired::where('transaksi_id', $key->id)->update(['status' => '1']);
            

                \Log::info("Successfully Send Email No Transaksi ".$key->Transaksi->no_transaksi);
            }
        }
        // \Log::info("Successfully Send Email No Transaksi ");
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}