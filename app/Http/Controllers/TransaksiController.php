<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\Kurir;
use App\Item;
use App\User;
use App\Notifikasi;
use Carbon\Carbon;
use App\Helpers\SendNotif;

use Auth;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function setKunciTransaksi($user_id)
    {
        $sel_user = User::findOrFail($user_id);
        $transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
        $kunci_transaksi = $sel_user->DetailKonsumen->kunci_transaksi;
        if($transaksi_berlangsung == '0' && $kunci_transaksi == '1'){
            $sel_user->DetailKonsumen()->update(['kunci_transaksi' => '0']);
        }
        
        $success = 1;
        return $success;
    }

    public function index()
    {
    	$transaksi = Transaksi::whereIn('status',['1','6'])->orderBy('created_at','desc')->get();
        $menu_active = "transaksi|transaksi|0";
    	return view('transaksi.index',compact('transaksi','menu_active'));
    }

    public function show($id)
    {
    	$transaksi = Transaksi::findOrFail($id);
      
        $kurir = Kurir::where('status_aktif','1')
                       ->whereNotIn('kurir.id',function ($query) {
                            $query->select('kurir_id')
                                  ->from('pengiriman')
                                  ->where('pengiriman.status','0')
                                  ->distinct();
                       })
                       ->get();
    	
        $findNot = Notifikasi::where('judul_id',$id)->where('dibaca','0')->first();

        if(isset($findNot->id)){
            $findNot->Update(['dibaca' => '1']);
        }

        $menu_active = "transaksi|transaksi|1";
    	return view('transaksi.detail',compact('transaksi','kurir','menu_active'));
    }

    public function pesananDiterima($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => '5','tgl_bayar' => Carbon::now() ]);

        $transaksi->Pengiriman()->update(['diterima' => Carbon::now(), 
                                          'diterima_oleh' => 'Admin - '.Auth::user()->name,
                                          'status' => '1']);

        //Insert Notifikasi
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $transaksi->user_id,
        'judul_id' => $transaksi->id,
        'judul' => 'Terima Pesanan Nomor Transaksi '.$transaksi->no_transaksi,
        'isi' => 'Pesanan Dengan Nomor Transaksi '.$transaksi->no_transaksi.' Telah Diterima, Terimakasih Telah Berbelanja Di AgogoBakery',
        'jenis_notif' => 3,
        'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);


        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pengiriman', $notif->judul_id);
        
        $this->setKunciTransaksi($transaksi->user_id);
        return redirect()->back()->with("success","Berhasil Menyelesaikan Transaksi");
    }

    public function BatalTransaksi(Request $request)
    {
        $req = $request->all();
        $find = Transaksi::findOrFail($req['transaksi_id']);
        $penerima_id = $find->user_id;
        $no_transaksi = $find->no_transaksi;
        $metode_pembayaran = $find->metode_pembayaran;


        if($metode_pembayaran == "1"){
            $saldo_saat_ini = $find->User->DetailKonsumen->saldo;
            $total_bayar = $find->total_bayar;
            $new_saldo = $saldo_saat_ini + $total_bayar;
            $update_saldo = $find->User->DetailKonsumen()->update(['saldo' => $new_saldo]);
            $sambunganNotif = " ,Saldo Yang Dipakai, Telah Di Pulihkan Ke Akun Anda";
        }else{
            $sambunganNotif = "";
        }


        $find->update(['status' => '3']);
        if(isset($find->AjukanBatalPesanan->id)){
            $find->AjukanBatalPesanan()->update(['disetujui_oleh' => Auth::user()->name, 'status' => '1']);
            $isiNotif = 'Ajuan Pembatalan Pesanan Dengan Nomor Transaksi '.$find->no_transaksi.' Telah Disetujui'.$sambunganNotif;
        }else{
            $find->BatalPesanan()->create(['input_by' => 'Admin - '.Auth::user()->name]);
            $isiNotif = 'Pesanan Dengan Nomor Transaksi '.$find->no_transaksi.' Telah Dibatalkan'.$sambunganNotif;
        }
       
        
        $itemTransaksi = $find->ItemTransaksi;
        foreach ($itemTransaksi as $key ) {
            $find = Item::findOrFail($key['item_id']);
            $newStock = $find->stock + $key['jumlah'];
            $update = $find->update(['stock' => $newStock]);
        }

        $this->setKunciTransaksi($penerima_id);
        
        $dnotif =
        [
            'pengirim_id' => Auth::User()->id,
            'penerima_id' => $penerima_id,
            'judul_id' => $find->id,
            'judul' => 'Pembatalan Pesanan Nomor Transaksi '.$no_transaksi,
            'isi' => $isiNotif,
            'jenis_notif' => 6,
            'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);
        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);
        return redirect()->back()->with("success","Berhasil Membatalkan Transaksi");
    }

    public function AmbilPesanan(Request $request)
    {
        $req = $request->all();
        $validator = \Validator::make($req,['diambil_oleh' => 'required']);

        if($validator->fails()){
          return redirect()->back()->withErrors($validator)->with('gagal','simpan_ambil_pesanan');
        }

        $find = Transaksi::findOrFail($req['transaksi_id']);
        $penerima_id = $find->user_id;
        $find->update(['status' => '5', 'tgl_bayar' => Carbon::now() ]);
        
        $req['input_by'] = 'Admin - '.Auth::User()->name;
        $find->AmbilPesanan()->create($req);

        $dnotif =
        [
            'pengirim_id' => Auth::User()->id,
            'penerima_id' => $penerima_id,
            'judul_id' => $find->id,
            'judul' => 'Pengambilan Pesanan Nomor Transaksi '.$find->no_transaksi,
            'isi' => 'Terima Kasih Telah Belanja Di Agogo Bakery, Pesanan Dengan Nomor Transaksi '.$find->no_transaksi.' Telah Diambil Oleh '.$req['diambil_oleh'],
            'jenis_notif' => 8,
            'dibaca' => '0'
        ];
    
        $notif = Notifikasi::create($dnotif);
         //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

        $this->setKunciTransaksi($penerima_id);
        return redirect()->back()->with("success","Berhasil Menyelesaikan Transaksi");
    }

    public function konfirBayar($id)
    {
        $find = Transaksi::findOrFail($id);
        $find->update(['status' => '1', 'tgl_bayar' => Carbon::now() ]);

        $find->LogKonfirBayar()->create(['input_by' => Auth::User()->name ]);

        $dnotif =
        [
            'pengirim_id' => Auth::User()->id,
            'penerima_id' => $find->user_id,
            'judul_id' => $find->id,
            'judul' => 'Konfirmasi Pembayaran No. Transaksi '.$find->no_transaksi,
            'isi' => 'Terima Kasih Telah Melakukan Transfer Pembayaran Untuk Pesanan Nomor Transaksi '.$find->no_transaksi.' Pesanan Anda Akan Kami Proses Untuk Pengantaran Ke Rumah Anda',
            'jenis_notif' => 7,
            'dibaca' => '0'
        ];
    
        $notif = Notifikasi::create($dnotif);
         //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

        return redirect()->back()->with("success","Berhasil Konfir Pembayaran");


    }
}
