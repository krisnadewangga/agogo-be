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
                    // $email_body = "<div style='padding:10px;'>
                    //                     <div>
                    //                         Anda Belum Melakukan Pembayaran Untuk Pemesanan 
                    //                         Dengan No Transaksi <b class='fg-red'>".$key->Transaksi->no_transaksi."</b> Dan List Pemesanan Sebagai Berikut
                    //                     </div>

                    //                     <table class='blueTable' style='margin-top:10px; margin-bottom:10px;'>
                    //                         <thead>
                    //                             <th style='width:10px;'>No</th>
                    //                             <th>Item</th>
                    //                             <th>Jumlah</th>
                    //                             <th>Harga</th>
                    //                             <th>Total</th>
                    //                         </thead>
                    //                         <tbody>
                    //                             ".$key->item."
                    //                         </tbody>
                    //                     </table>
                    //                     <div>
                    //                         Segera Lakukan Pembayaran <br/>
                    //                         Dengan Mentransfer Ke Salah Satu Rekening Dibawah Ini :
                    //                     </div>

                    //                     <div style='padding:10px; border:1px solid #ededed; text-align:center; margin-top:10px; background:#ededed'>
                    //                         <h2 style='margin:0px'>BRI</h2>
                    //                         <h2 style='margin:0px'>An: Fulan Bin Fulan</h2>
                    //                         <h2 style='margin:0px'>0168 01 0000 2222 2</h2>
                    //                     </div>
                    //                     <div style='padding:10px; border:1px solid #ededed; text-align:center; margin-top:10px; background:#ededed;'>
                    //                         <h2 style='margin:0px'>BNI</h2>
                    //                         <h2 style='margin:0px'>An: Fulan Bin Fulan</h2>
                    //                         <h2 style='margin:0px'>0168 01 0000 2222 2</h2>
                    //                     </div>
                    //                     <div style='padding:10px; border:1px solid #ededed; text-align:center; margin-top:10px; background:#ededed;'>
                    //                         <h2 style='margin:0px'>BCA</h2>
                    //                         <h2 style='margin:0px'>An: Fulan Bin Fulan</h2>
                    //                         <h2 style='margin:0px'>0168 01 0000 2222 2</h2>
                    //                     </div>
                    //                     <div style='padding:10px; border:1px solid #ededed; text-align:center; margin-top:10px; background:#ededed'>
                    //                         <h2 style='margin:0px'>MANDIRI</h2>
                    //                         <h2 style='margin:0px'>An: Fulan Bin Fulan</h2>
                    //                         <h2 style='margin:0px'>0168 01 0000 2222 2</h2>
                    //                     </div>
                    //                     <div style='padding:10px; border:1px solid #ededed; text-align:center; margin-top:10px; margin-bottom:20px;  background:#ededed;'>
                    //                         <h2 style='margin:0px'>SULUTGO</h2>
                    //                         <h2 style='margin:0px'>An: Fulan Bin Fulan</h2>
                    //                         <h2 style='margin:0px'>0168 01 0000 2222 2</h2>
                    //                     </div>

                    //                     <div syle='margin-top:10px; '>
                    //                         Batas Transfer Pembayaran Sampai : 
                    //                         <h2 style='margin-top:3px; margin-bottom:3px;'>".$key->Transaksi->waktu_kirim->format('d/m/Y h:i A')."</h2>
                    //                         <i style='font-size:12px;'>*) Pesanan Akan Dibatalkan Apabila Sampai Dengan Batas Waktu Yang Telah Ditentukan Anda Belum Melakukan Transfer Pembayaran </i>
                    //                     </div>

                    //                     <div style='margin-top:20px; margin-bottom:20px; text-align:center'>
                    //                         <a href='https://api.whatsapp.com/send?phone=6282343965747&text=Halo Admin, Saya Mau Konfirmasi Pembayaran, Untuk Pesanan Nomor Transaksi ".$key->Transaksi->no_transaksi."'><button class='myButton'>Konfirmasi Pembayaran</button></a>
                    //                     </div>

                    //                     <hr />
                    //                    </div>
                    // //                   ";
                    // $subject = "Invoice Pembayaran";
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
                    // $email_body = "<div style='padding:10px;'>
                    //                     <div>
                    //                         Anda Baru Saja Melakukan Pemesanan Di Agogobakery.com <br/>
                    //                         Dengan No Transaksi <b class='fg-red'>".$key->Transaksi->no_transaksi."</b> Dan List Pemesanan Sebagai Berikut
                    //                     </div>

                    //                     <table class='blueTable' style='margin-top:10px; margin-bottom:10px;'>
                    //                         <thead>
                    //                             <th style='width:10px;'>No</th>
                    //                             <th>Item</th>
                    //                             <th>Jumlah</th>
                    //                             <th>Harga</th>
                    //                             <th>Total</th>
                    //                         </thead>
                    //                         <tbody>
                    //                             ".$key->item."
                    //                         </tbody>
                    //                     </table>
                                    
                    //                     <div syle='margin-top:10px; '>
                    //                         Batas Pengambilan Pesanan Sampai : 
                    //                         <h2 style='margin-top:3px; margin-bottom:3px;'>".$key->Transaksi->waktu_kirim->format('d/m/Y h:i A')."</h2>
                    //                         <i style='font-size:12px;'>*) Pesanan Akan Dibatalkan Apabila Sampai Dengan Batas Waktu Yang Telah Ditentukan Anda Belum Mengambil Pesanan </i>
                    //                     </div>
                                    
                    //                     <hr />
                    //                    </div>
                    //                   ";
                    // $subject = "Pesanan Agogobakery.com";
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
                // $data = ['name' => $key->Transaksi->User->name, 
                //          'email_body' => $email_body
                //         ];

                
                $notif = Notifikasi::create($dnotif);
                $sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pembayaran Pesanan', $notif->judul_id);
                $a = SendNotif::sendNotifWa($noHp,$pesanWa);                                                                             
                //$kirim_email = SendNotif::kirimEmail($email,$data,$subject);

                $update = NotifExpired::where('id', $key->id)->update(['status' => '1']);
            

                \Log::info("Successfully Send Email No Transaksi ".$key->Transaksi->no_transaksi);
            }
        }
        // \Log::info("Successfully Send Email No Transaksi ");
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}