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

    public function PengajuanBatalPesanan()
    {   
        $transaksi = Transaksi::where('status','4')->orderBy('updated_at','desc')->get();
        $menu_active = "transaksi|ap|0";
        return view('transaksi.pengajuan_batal_pesanan',compact('transaksi','menu_active'));
    }

    public function KonfirmasiPembayaran()
    {
        $transaksi = Transaksi::where('status','6')->orderBy('updated_at','desc')->get();
        $menu_active = "transaksi|kp|0";
        return view('transaksi.konfirmasi_pembayaran',compact('transaksi','menu_active'));
    }

    public function filterTransaksi(Request $request)
    {
        $req = $request->all();

        $jenis_transaksi = $req['jenis_transaksi'];
        $status_transaksi = $req['status_transaksi'];

        if($jenis_transaksi == "0"){
            $queryWhere = ['1','2','3'];
        }else{
            $queryWhere = $jenis_transaksi;
        }

       
        $waktu_sekarang = Carbon::now()->format('Y-m-d H:i:s');
        if($jenis_transaksi == "0" && $status_transaksi == "0"){
            $transaksi = Transaksi::whereNotIn('status',['5','3'])
                                    ->where('jenis','1')
                                    ->where('jalur','1')
                                    ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi == "0" && $status_transaksi == "8"){
            $transaksi = Transaksi::where([
                                              ['metode_pembayaran','=','3'],
                                              ['status','=','1'],
                                              ['waktu_kirim','>', $waktu_sekarang ],
                                              ['jenis','=','1'],
                                              ['jalur','=','1']
                                            ])
                                    ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi == "0" && $status_transaksi == "1"){
            $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2'])
                                    ->where([
                                              ['status','=','1'],
                                              ['waktu_kirim','>', $waktu_sekarang ],
                                              ['jenis','=','1'],
                                              ['jalur','=','1']
                                            ])
                                    ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi == "0" && $status_transaksi == "7"){
                $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                         ->whereNotIn('status',['5','3','2','4'])
                                         ->where('waktu_kirim','<', $waktu_sekarang)
                                          ->where('jenis','1')
                                          ->where('jalur','1')
                                         ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi == "0" && ($status_transaksi == "5" || $status_transaksi == "3") ){
                $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                         ->where('status',$status_transaksi)
                                          ->where('jenis','1')
                                          ->where('jalur','1')
                                         ->orderBy('updated_at','desc')->get();
                                        
        }else if($jenis_transaksi == "0" && $status_transaksi == "4"){
                $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                         ->where([ 
                                                  ['status','=',$status_transaksi],
                                                  ['jenis','=','1'],
                                                  ['jalur','=','1']
                                                 ])
                                         ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi == "0"  && $status_transaksi == "2" ){
                $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                         ->where([ 
                                                  ['status','=',$status_transaksi],
                                                  ['jenis','=','1'],
                                                  ['jalur','=','1']
                                              ])
                                         ->orderBy('updated_at','desc')->get();

        }else if($jenis_transaksi == "0"  && $status_transaksi == "6" ){
                $transaksi = Transaksi::whereIn('metode_pembayaran',['1','2','3'])
                                         ->where([ 
                                                  ['status','=',$status_transaksi],
                                                  ['waktu_kirim','>', $waktu_sekarang ],
                                                  ['jenis','=','1'],
                                                  ['jalur','=','1']
                                              ])
                                         ->orderBy('updated_at','desc')->get();

        }else if($jenis_transaksi != "0" && $status_transaksi == "0"){
                $transaksi = Transaksi::whereNotIn('status',['5','3'])
                                    ->where('metode_pembayaran',$jenis_transaksi)
                                    ->where('jenis','1')
                                    ->where('jalur','1')
                                    ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi != "0" &&  ($status_transaksi == "5" || $status_transaksi == "3") ){
                $transaksi = Transaksi::where('metode_pembayaran',$jenis_transaksi)
                                   ->where('status',$status_transaksi)
                                   ->where('jenis','1')
                                   ->where('jalur','1')
                                   ->orderBy('updated_at','desc')->get();
        }else if( $jenis_transaksi != "0" && $status_transaksi == "4") {
                $transaksi = Transaksi::where([ 
                                                  ['metode_pembayaran','=',$jenis_transaksi],
                                                  ['status','=',$status_transaksi],
                                                  ['jenis','=','1'],
                                                  ['jalur','=','1']
                                              ])
                                         ->orderBy('updated_at','desc')->get();
        }else if( $jenis_transaksi != "0" && ($status_transaksi == "1" || $status_transaksi == "6" ) ){
                $transaksi = Transaksi::where([ 
                                                  ['metode_pembayaran','=',$jenis_transaksi],
                                                  ['status','=',$status_transaksi],
                                                  ['waktu_kirim','>', $waktu_sekarang ],
                                                  ['jenis','=','1'],
                                                  ['jalur','=','1']
                                              ])
                                         ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi != "0" && $status_transaksi == "7"){
                $transaksi = Transaksi::whereNotIn('status',['5','3','2','4'])
                                         ->where('metode_pembayaran',$jenis_transaksi)
                                         ->where('waktu_kirim','<', $waktu_sekarang)
                                         ->where('jenis','1')
                                         ->where('jalur','1')
                                         ->orderBy('updated_at','desc')->get();
        }else if($jenis_transaksi != "0" && $status_transaksi == "2"){
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
            $transaksi['waktu_tampil'] = $transaksi->updated_at->format("d M Y h:i A");
            if($transaksi['metode_pembayaran'] == "1"){
                $tampil_jt = "<span class='label label-warning '>TopUp</span>";
            }else if($transaksi['metode_pembayaran'] == "2"){
                $tampil_jt = "<span class='label label-info'>Bank Transfer</span>";
            }else if($transaksi['metode_pembayaran'] == "3"){
                $tampil_jt = "<span class='label label-success'>Bayar Di Toko</span>";
            }

            if( $transaksi['status'] == "1" || $transaksi['status'] == "6"){
                $waktu_skrang = strtotime(date('Y-m-d H:i:s'));
                $batas_ambe = strtotime($transaksi['waktu_kirim']);
               
                if( ($waktu_skrang < $batas_ambe ) && $transaksi['metode_pembayaran'] == "1"){
                    $status = '<label class="label label-info">Menunggu Pengiriman</label>';
                }else if( ($waktu_skrang < $batas_ambe ) && $transaksi['metode_pembayaran'] == "3"){
                    $status = '<label class="label label-info">Menunggu Pengambilan</label>';
                }else if(  ($waktu_skrang < $batas_ambe )  && $transaksi['metode_pembayaran'] == "2"){
                    if($transaksi['status'] == "6"){
                        $status = '<label class="label label-info">Menunggu Transfer</label>';
                    }else{
                        $status = '<label class="label label-info">Menunggu Pengiriman</label>';
                    }     
                    
                }else if(  ($waktu_skrang > $batas_ambe )  && $transaksi['metode_pembayaran'] == "2"){
                    if($transaksi['status'] == "6"){
                        $status = '<label class="label label-danger">Expired</label>';
                    }else{
                        $status = '<label class="label label-info">Menunggu Pengiriman</label>';
                    }     
                    
                }else{
                    $status = '<label class="label label-danger">Expired</label>';
                } 
               
                
            }else if($transaksi['status'] == "2"){
                $status = "<label class='label label-info'>Sementara Pengiriman</label>";
            }else if($transaksi['status'] == "3"){
                $status = "<label class='label label-danger'>Pesanan Dibatalkan</label>";
            }else if($transaksi['status'] == "4"){
                $status = "<label class='label label-warning'>Ajukan Pembatalan</label>";
            }else if($transaksi['status'] == "5"){
                $status = "<label class='label label-success'>Pesanan Diterima</label>";
            }else if($transaksi['status'] == "7"){
                $status = "<label class='label label-warning'>Pesanan Disimpan</label>";
            }

            $transaksi['tampil_jt'] = $tampil_jt;
            $transaksi['tampil_status'] = $status;
            return $transaksi;
        });

        return response()->json($transaksi);

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

        SendNotif::SendNotPesan('5',['jenisNotif' => '2']);

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

            DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('penjualan_toko', $key['jumlah']);
                
            DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('total_penjualan', $key['jumlah']);
                
            DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('sisa_stock', $key['jumlah']);
        }

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
         //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

        return redirect()->back()->with("success","Berhasil Konfir Pembayaran");


    }
}
