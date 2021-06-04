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
use App\NotifExpired;
use DB;
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

        if($transaksi_berlangsung < '3' && $kunci_transaksi == '1'){
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

    public function Maps(Request $request)
    {

      $req = $request->all();

      $jenis_transaksi = $req['jenis_transaksi'];
      $status_transaksi = $req['status_transaksi'];

      $transaksi = $this->setFilter($jenis_transaksi,$status_transaksi);  
      $infoMarker = $transaksi->map(function ($transaksi) {
        return collect($transaksi->toArray())
            // ->pluck('nama','lat')
            ->only(['no_transaksi','nama','lat','long','detail_alamat','marker_jt','marker_status'])
            ->all();
      });

      if($status_transaksi == '0'){
        $tampil_status = 'Aktif';
      }elseif($status_transaksi == '1'){
        $tampil_status = 'Dikemas';
      }else if($status_transaksi == '2'){
        $tampil_status = 'Dikirim';
      }else if($status_transaksi == '3'){
        $tampil_status = 'Dibatalkan';
      }else if($status_transaksi == '4'){
        $tampil_status = 'Pengajuan Pembatalan';
      }else if($status_transaksi == '5'){
        $tampil_status = 'Diterima';
      }else if($status_transaksi == '6'){
        $tampil_status = 'Menunggu Transfer';
      }else if($status_transaksi == '8'){
        $tampil_status = 'Menunggu Pengambilan';
      }
      

      $menu_active = "transaksi|transaksi|0";
      return view('transaksi.maps', compact('menu_active','infoMarker','tampil_status'));
    }

    public function PengajuanBatalPesanan()
    {   
        $transaksi = Transaksi::where('status','4')->orderBy('updated_at','desc')->get();
        $menu_active = "transaksi|ap|0";
        return view('transaksi.pengajuan_batal_pesanan',compact('transaksi','menu_active'));
    }

    public function KonfirmasiPembayaran()
    {
        $transaksi = Transaksi::where('status','6')->where('waktu_kirim','>',date('Y-m-d H:i:s'))
                     ->orderBy('updated_at','desc')->get();
        $menu_active = "transaksi|kp|0";
        return view('transaksi.konfirmasi_pembayaran',compact('transaksi','menu_active'));
    }

    public function setFilter($jenis_transaksi,$status_transaksi)
    {
      if($jenis_transaksi == "0"){
          $queryWhere = ['1','2','3','4'];
      }else{
          $queryWhere = $jenis_transaksi;
      }

      $waktu_sekarang = Carbon::now()->format('Y-m-d H:i:s');
      if($jenis_transaksi == "0" && $status_transaksi == "0"){
          // $transaksi = Transaksi::whereNotIn('status',['5','3'])
          //                         ->where('waktu_kirim','>',$waktu_sekarang)
          //                         ->where('jenis','1')
          //                         ->where('jalur','1')
          //                         ->orderBy('updated_at','desc')->get();

           $transaksi = Transaksi::where(function($q) use ($waktu_sekarang){
                                    return $q->whereNotIn('status',['5','3']) // pembayaran selain saldo,cod expire
                                              ->whereNotIn('metode_pembayaran',['1','4'])
                                              ->where('waktu_kirim','>',$waktu_sekarang) 
                                              ->where('jenis','1')
                                              ->where('jalur','1');
                                  })->orWhere(function($a) {
                                    return $a->whereNotIn('status',['5','3'])
                                             ->where('metode_pembayaran','1') // pembayaran saldo tidak expire
                                             ->where('jenis','1')
                                             ->where('jalur','1');
                                  })->orWhere(function($b){ // pembayaran cod tidak expire
                                    return $b->whereNotIn('status',['5','3'])
                                             ->where('metode_pembayaran','4')
                                             ->where('jenis','1')
                                             ->where('jalur','1');
                                  })
                                  ->orderBy('updated_at','desc')->get();

      // }else if($jenis_transaksi == "0" && $status_transaksi == "8"){
      //     $transaksi = Transaksi::where([
      //                                       ['metode_pembayaran','=','3'],
      //                                       ['status','=','1'],
      //                                       ['waktu_kirim','>', $waktu_sekarang ],
      //                                       ['jenis','=','1'],
      //                                       ['jalur','=','1']
      //                                     ])
      //                             ->orderBy('updated_at','desc')->get();
      }else if($jenis_transaksi == "0" && $status_transaksi == "1"){
          // menunggu pengiriman dan pengambilan
          $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3','4'])
                                  ->where([
                                            ['status','=','1'],
                                            ['jenis','=','1'],
                                            ['jalur','=','1']
                                          ])
                                  ->orderBy('updated_at','desc')->get();
      }else if($jenis_transaksi == "0" && $status_transaksi == "7"){
              // status pesanan yang status transaksi expired nda ta pake
              $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                       ->whereNotIn('status',['5','3','2','4'])
                                       ->where('waktu_kirim','>', $waktu_sekarang)
                                        ->where('jenis','1')
                                        ->where('jalur','1')
                                       ->orderBy('updated_at','desc')->get();
      }else if($jenis_transaksi == "0" && ($status_transaksi == "5" || $status_transaksi == "3") ){
              // status pesanan yang terima dan di batalkan
              $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3','4'])
                                       ->where('status',$status_transaksi)
                                        ->where('jenis','1')
                                        ->where('jalur','1')
                                       ->orderBy('updated_at','desc')->get();
                                      
      }else if($jenis_transaksi == "0" && $status_transaksi == "4"){
              // status pesanan yang pengajuan pembatalan
              $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3','4'])
                                       ->where([ 
                                                ['status','=',$status_transaksi],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                               ])
                                       ->orderBy('updated_at','desc')->get();
      }else if($jenis_transaksi == "0"  && $status_transaksi == "2" ){
              // status pesanan yang dikirim
              $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3','4'])
                                       ->where([ 
                                                ['status','=',$status_transaksi],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                            ])
                                       ->orderBy('updated_at','desc')->get();

      }else if($jenis_transaksi == "0"  && $status_transaksi == "6" ){
              // status menunggu transfer
              $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                       ->where([ 
                                                ['status','=',$status_transaksi],
                                                ['waktu_kirim','>', $waktu_sekarang ],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                            ])
                                       ->orderBy('updated_at','desc')->get();

      }else if($jenis_transaksi != "0" && $status_transaksi == "0"){
              
              if($jenis_transaksi !== "4" && $jenis_transaksi !== "1"){
               
                // jenis transaksi != topup & cod dan status != terima dan dibatalkan
                $transaksi = Transaksi::whereNotIn('status',['5','3'])
                                    ->where('metode_pembayaran',$jenis_transaksi)
                                    ->where('jenis','1')
                                    ->where('jalur','1')
                                    ->where('waktu_kirim','>', $waktu_sekarang)
                                    ->orderBy('updated_at','desc')->get();
              }else{
                // transaksi selain status terima dan dibatalkan dan selai mp topup
                $transaksi = Transaksi::whereNotIn('status',['5','3'])
                                    ->where('metode_pembayaran',$jenis_transaksi)
                                    ->where('jenis','1')
                                    ->where('jalur','1')
                                    ->orderBy('updated_at','desc')->get();

              }



      }else if($jenis_transaksi != "0" &&  ($status_transaksi == "5" || $status_transaksi == "3") ){
              //status pesanan diterima dan dibatalkan
              $transaksi = Transaksi::where('metode_pembayaran',$jenis_transaksi)
                                 ->where('status',$status_transaksi)
                                 ->where('jenis','1')
                                 ->where('jalur','1')
                                 ->orderBy('updated_at','desc')->get();
      }else if( $jenis_transaksi != "0" && $status_transaksi == "4") {
              // status pengajuan pesanan untuk dibatalkan
              $transaksi = Transaksi::where([ 
                                                ['metode_pembayaran','=',$jenis_transaksi],
                                                ['status','=',$status_transaksi],
                                                ['jenis','=','1'],
                                                ['jalur','=','1'],
                                                // ['waktu_kirim','>', $waktu_sekarang ]
                                            ])
                                       ->orderBy('updated_at','desc')->get();
      }else if( $jenis_transaksi != "0" &&  $status_transaksi == "1" ){
              // status pesanan yang smntra mempersiapkan pesanan 
              $transaksi = Transaksi::where([ 
                                                ['metode_pembayaran','=',$jenis_transaksi],
                                                ['status','=',$status_transaksi],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                            ])
                                       ->orderBy('updated_at','desc')->get();
      }else if( $jenis_transaksi != "0" &&  $status_transaksi == "6" ){
              // menunggu transfer pembayaran
              $transaksi = Transaksi::where([ 
                                                ['metode_pembayaran','=',$jenis_transaksi],
                                                ['status','=',$status_transaksi],
                                                ['waktu_kirim','>', $waktu_sekarang ],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                            ])
                                       ->orderBy('updated_at','desc')->get();

      }else if($jenis_transaksi != "0" && $status_transaksi == "7"){
              //untuk melihat yang tidak kepake / expired

              $transaksi = Transaksi::whereNotIn('status',['5','3','2','4'])
                                       ->where('metode_pembayaran',$jenis_transaksi)
                                       ->where('waktu_kirim','<', $waktu_sekarang)
                                       ->where('jenis','1')
                                       ->where('jalur','1')
                                       ->orderBy('updated_at','desc')->get();
      }else if($jenis_transaksi != "0" && $status_transaksi == "2"){
              // status pesanan yang smntra pengiriman
              $transaksi = Transaksi::where([ 
                                                ['metode_pembayaran','=',$jenis_transaksi],
                                                ['status','=',$status_transaksi],
                                                ['jenis','=','1'],
                                                ['jalur','=','1']
                                            ])
                                       ->orderBy('updated_at','desc')->get();
      }
      

      $transaksi->map(function($transaksi){
          $transaksi['nama'] = $transaksi->User->name;
          $transaksi['jum_pesanan'] = $transaksi->ItemTransaksi()->count();
          $transaksi['total_bayar'] = number_format($transaksi->total_bayar,'0','','.');
          $transaksi['waktu_tampil'] = $transaksi->created_at->format("d M Y h:i A");
          if($transaksi['metode_pembayaran'] == "1"){
              $tampil_jt = "<span class='label label-warning '>TopUp</span>";
              $marker_jt = "TopUp";
              $waktu_kirim = $transaksi->waktu_kirim->format("d M Y h:i A");
          }else if($transaksi['metode_pembayaran'] == "2"){
              $tampil_jt = "<span class='label label-info'>Bank Transfer</span>";
              $marker_jt = "Bank Transfer";
              $waktu_kirim = $transaksi->waktu_kirim->format("d M Y h:i A");
          }else if($transaksi['metode_pembayaran'] == "3"){
              $tampil_jt = "<span class='label label-success'>Bayar Di Toko</span>";
              $marker_jt = "Bayar Di Toko";
              $waktu_kirim = $transaksi->waktu_kirim->format("d M Y h:i A");
          }else if($transaksi['metode_pembayaran'] == "4"){
              $tampil_jt = "<span class='label bg-purple text-white '>COD</span>";
              $marker_jt = "COD";
              $waktu_kirim = $transaksi->waktu_kirim->format("d M Y h:i A");
          }

          if( $transaksi['status'] == "1" || $transaksi['status'] == "6"){
              $waktu_skrang = strtotime(date('Y-m-d H:i:s'));
              $batas_ambe = strtotime($transaksi['waktu_kirim']);
             
              if( ($waktu_skrang < $batas_ambe ) && $transaksi['metode_pembayaran'] == "1"){
                  $status = '<label class="label label-info">Dikemas</label>';
                  $marker_status = 'Dikemas';
              }else if(($waktu_skrang > $batas_ambe ) && ($transaksi['metode_pembayaran'] == "1" || $transaksi['metode_pembayaran'] == "4") ){ //pembayaran transfer deng cod expire
                $status = '<label class="label label-info">Dikemas</label><br/><label class="label label-danger">Lewat Batas Pengiriman</label>';
                $marker_status = 'Dikemas (Lewat Batas Pengiriman)';
              }else if( ($waktu_skrang < $batas_ambe ) && ( $transaksi['metode_pembayaran'] == "3" || $transaksi['metode_pembayaran'] == "4" ) ){
                  $status = '<label class="label label-info">Dikemas</label>';
                  $marker_status = 'Dikemas';
              }else if(  ($waktu_skrang < $batas_ambe )  && $transaksi['metode_pembayaran'] == "2"){
                  if($transaksi['status'] == "6"){
                      $status = '<label class="label bg-yellow">Menunggu Transfer</label>';
                      $marker_status = 'Menunggu Transfer';
                  }else{
                      $status = '<label class="label label-info">Dikemas</label>';
                      $marker_status = 'Dikemas';
                  }     
                  
              }else if(  ($waktu_skrang > $batas_ambe )  && $transaksi['metode_pembayaran'] == "2"){
                  if($transaksi['status'] == "6"){
                      $status = '<label class="label label-danger">Pesanan Dibatalkan </label>';
                      $marker_status = 'Pesanan Dibatalkan';
                  }else{
                      $status = '<label class="label label-info">Dikemas</label>';
                      $marker_status = 'Dikemas';
                  }     
              }else{
                  $status = '<label class="label label-danger>Pesanan Dibatalkan </label>';
                  $marker_status = 'Pesanan Dibatalkan';
              } 
              
          }else if($transaksi['status'] == "2"){
              $status = "<label class='label bg-purple'>Dikirim</label>";
              $marker_status = 'Dikirim';
          }else if($transaksi['status'] == "3"){
              $status = "<label class='label label-danger'>Pesanan Dibatalkan</label>";
              $marker_status = 'Pesanan Dibatalkan';
          }else if($transaksi['status'] == "4"){
              $status = "<label class='label label-warning'>Ajukan Pembatalan</label>";
              $marker_status = 'Ajukan Pembatalan';
          }else if($transaksi['status'] == "5"){
              $status = "<label class='label label-success'>Terima</label>";
              $marker_status = 'Terima';
          }else if($transaksi['status'] == "7"){
              $status = "<label class='label label-warning'>Pesanan Disimpan</label>";
              $marker_status = 'Pesanan Disimpan';
          }

          $transaksi['tampil_jt'] = $tampil_jt;
          $transaksi['tampil_status'] = $status;
          $transaksi['marker_status'] = $marker_status;
          $transaksi['marker_jt'] = $marker_jt;
          $transaksi['tampil_waktu_kirim'] = $waktu_kirim;
          return $transaksi;
      });

      return $transaksi;
    }

    public function filterTransaksi(Request $request)
    {
        $req = $request->all();
        // return $req;

        $jenis_transaksi = $req['jenis_transaksi'];
        $status_transaksi = $req['status_transaksi'];
       

        $transaksi = $this->setFilter($jenis_transaksi,$status_transaksi);

        return response()->json($transaksi);

    }

    public function show($id)
    {
    	$transaksi = Transaksi::findOrFail($id);
      
        // $kurir = Kurir::join('users','users.id','=','kurir.user_id')
        //                ->where('users.status_aktif','1')
        //                ->whereNull('deleted_at')
        //                ->select("kurir.id","users.name as nama")
        //                ->whereNotIn('kurir.id',function ($query) {
        //                     $query->select('kurir_id')
        //                           ->from('pengiriman')
        //                           ->where('pengiriman.status','0')
        //                           ->distinct();
        //                })
        //                ->get();


        $kurir = Kurir::join('users','users.id','=','kurir.user_id')
                    
        ->whereNull('deleted_at')
        ->select("kurir.id","users.name as nama")
        ->get();

    
    	
        $findNot = Notifikasi::where('judul_id',$id)->where('dibaca','0')->count();

        if($findNot > 0){
            $update = Notifikasi::where('judul_id',$id)->where('dibaca','0')->Update(['dibaca' => '1']);
            // $findNot->Update(['dibaca' => '1']);
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
        SendNotif::SendNotPesan('5',['jenisNotif' => '2']);
        // Kirim Notif Ke Web User
        SendNotif::SendNotPesan('1','',[$transaksi->user_id]);

        $userWa = User::findOrfail($transaksi->user_id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);
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
       
        
        // $itemTransaksi = $find->ItemTransaksi;
        // foreach ($itemTransaksi as $key ) {
        //     $find = Item::findOrFail($key['item_id']);
        //     $newStock = $find->stock + $key['jumlah'];
        //     $update = $find->update(['stock' => $newStock]);

        //     DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('penjualan_toko', $key['jumlah']);
                
        //     DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('total_penjualan', $key['jumlah']);
                
        //     DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('sisa_stock', $key['jumlah']);
        // }
        
        $this->setKunciTransaksi($penerima_id);
        SendNotif::SendNotPesan('5',['jenisNotif' => '3']);
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

          $userWa = User::findOrfail($find->user_id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);
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
        $waktu_skrang = strtotime(date('Y-m-d H:i:s'));
        $batas_ambe = strtotime($find->waktu_kirim);

        if($waktu_skrang > $batas_ambe){
          return redirect()->back()->with('error','Transaksi Telah Expire, Silahkan Lakukan Pemesanan Kembali');
        }

        $penerima_id = $find->user_id;
        $find->update(['status' => '5', 'tgl_bayar' => Carbon::now() ]);
        
        $req['input_by'] = 'Admin - '.Auth::User()->name;
        $find->AmbilPesanan()->create($req);
        $min_stock_item = $this->UpdateStock($find->no_transaksi);

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

        $userWa = User::findOrfail($find->user_id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);
         //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

        $this->setKunciTransaksi($penerima_id);
        return redirect()->back()->with("success","Berhasil Menyelesaikan Transaksi");
    }


    public function UpdateStock($no_transaksi)
    {
     
     $sel = Transaksi::where('no_transaksi',$no_transaksi)->first();
     $itemTransaksi = $sel->ItemTransaksi;

     foreach ($itemTransaksi as $key ) {
       $find = Item::findOrFail($key['item_id']);
       $newStock = $find->stock - $key['jumlah'];
       $update = $find->update(['stock' => $newStock]);

       DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['jumlah']);
                
       DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['jumlah']);
                
       DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['jumlah']);

     }
    }

    public function konfirBayar($id)
    {
        $find = Transaksi::findOrFail($id);
        $waktu_skrang = strtotime(date('Y-m-d H:i:s'));
        $batas_ambe = strtotime($find->waktu_kirim);

        if($waktu_skrang > $batas_ambe){
          return redirect()->back()->with('error','Transaksi Telah Expire, Silahkan Lakukan Pemesanan Kembali');
        }

        $find->update(['status' => '1', 'tgl_bayar' => Carbon::now() ]);
        $find->LogKonfirBayar()->create(['input_by' => Auth::User()->name ]);

        NotifExpired::where('transaksi_id',$find->id)->update(['status' => '1']);

        SendNotif::SendNotPesan('5',['jenisNotif' => '4']);
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
        $userWa = User::findOrfail($find->user_id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);
         //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);
        
        return redirect()->back()->with("success","Berhasil Konfir Pembayaran");


    }

    public function destroy($id)
    {
      $find = Transaksi::findOrFail($id);
      $hapus = $find->delete();

      return response()->json(['success' => '1']);
    }
}
