<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Transaksi;
use App\User;
use App\Item;
use App\ItemTransaksi;
use App\Preorders;
use App\Kas;
use App\Helpers\SendNotif;
use App\Produksi;
use App\TargetProduksi;
use App\Opname;
use Carbon\Carbon;
use App\Kategori;
use App\Role;
use App\Aproval;
use PDF;
use Auth;
use DB;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // Pendapatan
    public function LapPendapatan()
    {
      $transaksi = Transaksi::selectRaw('transaksi.*, 
                                          (SELECT sum(jumlah * margin) from item_transaksi Where transaksi_id =transaksi.id) as sub_total_bersih_item ')
                            ->where([ 
                                      ['metode_pembayaran','=','1'],
                                      ['status','>=','1'],
                                      ['status','!=','3'],
                                      ['top_up', '=','0']
                                   ])
                            ->orWhere([ 
                                      ['metode_pembayaran','>','1'],
                                      ['status','=','5'],
                                      ['top_up', '=','0']
                                   ])
                            ->whereDate('tgl_bayar','=',Carbon::now()->format('Y-m-d'))->orderBy('tgl_bayar','DESC')->get();
        
        $transaksi->map(function($transaksi){
          if($transaksi->jenis == "2"){
            $kasir_entri = User::findOrFail($transaksi->user_id);
            $kasir_checkout = User::findOrFail($transaksi->kasir_id);

            if($kasir_entri != $kasir_checkout){
              $transaksi['nama_tampil'] = $kasir_entri->name." - ".$kasir_checkout->name;
            }else{
              $transaksi['nama_tampil'] = $kasir_entri->name;
            }
          }else{
            $transaksi['nama_tampil'] = $transaksi->User->name;
          }
          
        });

        // return $transaksi;
        $kop = "Laporan Pendapatan Hari Ini ".Carbon::now()->format('d M Y');

        $input = ['mt' => Carbon::now()->format('d/m/Y'), 'st' => "" ];
        $total_pendapatan = $transaksi->sum('total_bayar');
        $total_bersih_item = $transaksi->sum('sub_total_bersih_item');
        $total_pengiriman = $transaksi->sum('total_biaya_pengiriman');

        $menu_active = "laporan|pendapatan|0";
      
        return view("laporan.lap_pendapatan",compact('transaksi','total_pendapatan','menu_active','kop','input','total_bersih_item','total_pengiriman'));
    }



    public function DetailTransaksi($id)
    {
      $transaksi = Transaksi::findOrFail($id);
      $transaksi->ItemTransaksi;

      $menu_active = "laporan|pendapatan|1";
      return view('laporan.detail_transaksi_react',compact('menu_active','transaksi'));
      
    }

    public function DetailPemesanan($id)
    {
      $transaksi = Transaksi::findOrFail($id);

      $kasir_entri = User::findOrFail($transaksi->user_id);
      $kasir_checkout = User::findOrFail($transaksi->kasir_id);

      if($kasir_entri != $kasir_checkout){
        $transaksi['nama_tampil'] = $kasir_entri->name." - ".$kasir_checkout->name;
        $transaksi['entri_by'] = $kasir_entri->name;
        $transaksi['waktu_entri'] = $transaksi->created_at->format('d/m/Y h:i A');
        $transaksi['checkout_by'] = $kasir_checkout->name;
        $transaksi['waktu_checkout'] = $transaksi->updated_at->format('d/m/Y h:i A');
        $transaksi['jum_kasir'] = 2;

      }else{
        $transaksi['nama_tampil'] = $kasir_entri->name." | TGL : ".$transaksi->created_at->format('d/m/Y h:i A') ;
         $transaksi['jum_kasir'] = 1;
      }
     
      $transaksi->ItemTransaksi;

      $menu_active = "laporan|pendapatan|1";
      return view('laporan.detail_pemesanan_react',compact('menu_active','transaksi'));
    }

    
    public function FilterLaporan(Request $request)
    {
        $req = $request->all();
        $input = ['mt' => $req['mt'], 'st' => $req['st'] ];

        $data = $this->SetDataPendapatan($req);
        // return $arr_bettwen;
        $transaksi = $data->transaksi;
        $total_pendapatan = $data->total_pendapatan;
        $kop = $data->kop;

        // $total_bersih_item = $transaksi->sum('sub_total_bersih_item');
        // $total_pengiriman = $transaksi->sum('total_biaya_pengiriman');
        $menu_active = "laporan|pendapatan|0";

        return view("laporan.lap_pendapatan",compact('transaksi','total_pendapatan','menu_active','kop','input'));
    }

    public function ExportPendapatan(Request $request)
    {
      $req = $request->all();

      $data = $this->SetDataPendapatan($req);
     
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.pendapatan', compact('data'));
      // return view('export.pendapatan', compact('data'));
      return $pdf->stream('laporan-pendapatan-'.$data->file_export.'.pdf');

    }

    public function SetDataPendapatan($req)
    {
       if(!empty($req['mt'])){
              $explode1 = explode("/", $req['mt']);
              $conv1 = $explode1[1]."/".$explode1[0]."/".$explode1[2];

              $mt = Carbon::parse($conv1)->toDateString();
              $kop_mt = Carbon::parse($conv1)->format('d M Y');
        }

        if(!empty($req['st'])){
              $explode2 = explode("/", $req['st']);
              $conv2 = $explode2[1]."/".$explode2[0]."/".$explode2[2];

              $st = Carbon::parse($conv2)->toDateString();
              $kop_st = Carbon::parse($conv2)->format('d M Y');
        }
        
        if( !empty($req['mt']) && empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','=',$mt)
                                    ->where('status','!=', '3')
                                    ->where('top_up','=','0')
                                    ->orderBy('tgl_bayar','DESC')->get();
            
            $kop = "Laporan Pendapatan Di Tanggal $kop_mt";
            $kop_export = "Tanggal : ".$req['mt'];
            $file_export = str_replace("/", "-", $req['mt']);

        }else if( empty($req['mt']) && !empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','<=',$st)
                                    ->where('top_up','=','0')
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
            $kop = "Laporan Pendapatan Sampai Tanggal $kop_st";
            $kop_export = "Sampai Tanggal : ".$req['st'];
            $file_export = str_replace("/", "-", $req['st']);
        }else if( !empty($req['mt']) && !empty($req['st']) ){
             $arr_bettwen = ["$mt","$st"];
             $transaksi = Transaksi::whereDate('tgl_bayar','>=',$mt)
                                    ->whereDate('tgl_bayar','<=',$st)
                                    ->where('top_up','=','0')
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
             $kop = "Laporan Pendapatan Mulai Tanggal $kop_mt S/D $kop_st";
             $kop_export = "Tanggal : ".$req['mt']." - ".$req['st'];
             $file_export = str_replace("/", "-", $req['mt'])."-".str_replace("/", "-", $req['st']);
        }else{
            return redirect()->route('lap_pendapatan');
        }

        $transaksi->map(function($transaksi){
          if($transaksi->jenis == "2"){
            $kasir_entri = User::findOrFail($transaksi->user_id);
            $kasir_checkout = User::findOrFail($transaksi->kasir_id);

            if($kasir_entri != $kasir_checkout){
              $transaksi['nama_user'] = $kasir_entri->name."-".$kasir_checkout->name;
            }else{
              $transaksi['nama_user'] = $kasir_entri->name;
            }
          }else{
            $transaksi['nama_user'] = $transaksi->User->name;
          }
          
        });

        $total_pendapatan = $transaksi->sum('total_bayar');

        $data = (object) ['transaksi' => $transaksi, 'kop' => $kop, 'total_pendapatan' => $total_pendapatan, 'kop_export' => $kop_export, 'file_export' => $file_export ];

        return $data;
    }

    // END Pendapatan 

    // User
    public function LapUser()
    {   
        $user = User::where('level_id','6')
                     ->selectRaw("users.*, 
                                 (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status != '3') as total_belanja,
                                 (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status = '3') as batal_belanja
                               ")
                     ->where('email_verified_at','!=','')
                     ->orderBy('users.id','DESC')
                     ->get();
               
        $total_user = $user->count();
        
        $total_member = User::join('detail_konsumen as a','a.user_id','=','users.id')
                             ->where('a.status_member','=','1')
                             ->where('email_verified_at','!=','')
                             ->count();
        $total_not_member = User::join('detail_konsumen as a','a.user_id','=','users.id')
                             ->where('a.status_member','=','0')
                             ->where('email_verified_at','!=','')
                             ->count();
        $total_blokir = User::where([ 
                                        ['email_verified_at','!=',''],
                                        ['status_aktif','=','0']
                                    ])->count();
      
        $menu_active = "user|user|0";
        return view("laporan.lap_user",compact('menu_active','user', 'total_member','total_not_member','total_user','total_blokir'));
    }

    public function Member()
    {
        if(Gate::allows('manage-konsu')){
          $user = User::where('level_id','6')
                       ->selectRaw("users.*, 
                                   (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status != '3') as total_belanja,
                                   (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status = '3') as batal_belanja
                                 ")
                       ->where('email_verified_at','!=','')
                       ->whereIn('id',function($q){
                          $q->from('detail_konsumen')
                            ->select('user_id')
                            ->where('status_member','1');

                          return $q;
                       })
                       ->orderBy('users.id','DESC')
                       ->get();

          $total_user = $user->count();
          $total_user_diblokir =  User::where([ 
                                                  ['email_verified_at','!=',''],
                                                  ['status_aktif','=','0']
                                              ])
                                        ->whereIn('id',function($q){
                                            $q->from('detail_konsumen')
                                              ->select('user_id')
                                              ->where('status_member','1');

                                            return $q;
                                         })->count();
          $total_user_aktif =  User::where([ 
                                                  ['email_verified_at','!=',''],
                                                  ['status_aktif','=','1']
                                              ])
                                        ->whereIn('id',function($q){
                                            $q->from('detail_konsumen')
                                              ->select('user_id')
                                              ->where('status_member','1');

                                            return $q;
                                         })->count();
        
          $menu_active = "user|member|0";
          return view("user.member",compact('menu_active','user', 'total_user','total_user_diblokir','total_user_aktif'));          
        }else{
          abort('404','Halaman Tidak Ditemukan');
        }

    }

    public function NotMember()
    {
        if(Gate::allows('manage-konsu')){
          $user = User::where('level_id','6')
                       ->selectRaw("users.*, 
                                   (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status != '3') as total_belanja,
                                   (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status = '3') as batal_belanja
                                 ")
                       
                       ->where(function($a){
                          return $a->where('email_verified_at','!=','')
                                   ->whereIn('id',function($q){
                                        $q->from('detail_konsumen')
                                          ->select('user_id')
                                          ->where('status_member','0');

                                        return $q;
                                    });
                       })
                       ->orWhere(function($b){
                          return $b->where('email_verified_at','=',NULL)
                                   ->whereIn('id',function($q){
                                        $q->from('detail_konsumen')
                                          ->select('user_id')
                                          ->where('status_member','0');

                                        return $q;
                                    });
                       })
                       
                       ->orderBy('users.id','DESC')
                       ->get();
          
          $total_user = $user->count();
          $total_user_diblokir =  User::where([ 
                                                  ['email_verified_at','!=',''],
                                                  ['status_aktif','=','0']
                                              ])
                                        ->whereIn('id',function($q){
                                            $q->from('detail_konsumen')
                                              ->select('user_id')
                                              ->where('status_member','0');

                                            return $q;
                                         })->count();

          $total_user_aktif =  User::where([ 
                                                  ['email_verified_at','!=',''],
                                                  ['status_aktif','=','1']
                                              ])
                                        ->whereIn('id',function($q){
                                            $q->from('detail_konsumen')
                                              ->select('user_id')
                                              ->where('status_member','0');

                                            return $q;
                                         })->count();

           $total_user_menunggu_aktifasi =  User::where([ 
                                                  ['email_verified_at','=',NULL],
                                                  ['status_aktif','=','0']
                                              ])
                                        ->whereIn('id',function($q){
                                            $q->from('detail_konsumen')
                                              ->select('user_id')
                                              ->where('status_member','0');

                                            return $q;
                                         })->count();
        
          $menu_active = "user|not_member|0";
          return view("user.not_member",compact('menu_active','user', 'total_user','total_user_diblokir','total_user_aktif','total_user_menunggu_aktifasi'));
        }else{
          abort('404','Halaman Tidak Ditemukan');
        }
    }

    public function DetailUser(Request $request)
    {
        if(Gate::allows('manage-konsu')){
          $req = $request->all();

          $user = User::findOrFail($req['id']);
          $transaksi = $user->Transaksi()->orderBy('id','DESC')->get();
          
          if($user->status_aktif=='0'){
             $logBan = $user->LogBan()->where('status_ban','0')->orderBy('id','DESC')->first();
          }else{
              $logBan = "";
          }
          
          if($req['status_member'] == "1"){
            $ma = "member"; 
          }else{
            $ma = "not_member";
          }

          $menu_active = "user|$ma|0";
          return view("laporan.detail_user",compact('user','menu_active','transaksi','logBan'));
        }else{
          abort('404','Halaman Tidak Ditemukan');
        }
      }

      public function BlokirUser($id){
         if(Gate::allows('manage-konsu')){
          $user = User::findOrFail($id);
          $input_by = Auth::user()->name;

          if($user->status_aktif == "1"){
              $new_status = "0";  
              $msg = "Blokir";
          }else if($user->status_aktif == "0"){
              $new_status = "1";
              $msg = "Buka Blokir";
          }
          $user->update(['status_aktif' => $new_status]);
          $user->LogBan()->create(['status_ban' => $new_status,'input_by' => $input_by ]);

          return redirect()->back()->with("success","Berhasil $msg User");
        }else{
          abort('404','Halaman Tidak Ditemukan');
        }

    }

    public function AktifasiManual($id)
    {


      $user = User::where('id',$id);
      $userBaru = $user->first();
     SendNotif::sendNotifWa($userBaru->no_hp,"Selamat ".$userBaru->name." user anda telah aktif. Silahkan login Kembali");

      $user->update(['status_aktif' => '1','email_verified_at' => date('Y-m-d H:i:s')]);
      return redirect()->back()->with("success","Berhasil Mengaktifkan User");
    }


    public function HapusUser($id,$stat = '1')
    {
       if(Gate::allows('manage-konsu')){
         $find = User::findOrFail($id);
         $find->delete();
         if($stat== "1"){
           $hapus = "Konsumen (Member)";
         }else{
           $hapus = "Konsumen (Not Member)";
         }
         return redirect()->back()->with('success','Berhasil Hapus '.$hapus);
        }else{
          abort('404','Halaman Tidak Ditemukan');
        }
    }
    //END User

    //Penjualan -> Grafik
    public function ShowPenjualan()
    {
        $tahun = Transaksi::selectRaw("MIN(YEAR(tgl_bayar)) as min_tahun,
                                            MAX(YEAR(tgl_bayar)) as max_tahun ")
                          ->where('top_up','=','0')->first();

        $tahunNow = date('Y');
        $tahun['max_tahun'] = $tahunNow;
        $cek = $tahun->count();
        if($cek == 0){
            $tahun = (object) ['min_tahun' => $tahunNow, 'max_tahun' => $tahunNow];
        }
        
        $item = Item::orderBy('nama_item','ASC')->get();
        $menu_active = "laporan|penjualan|0";
        return view('laporan.lap_penjualan',compact('menu_active','tahun','tahunNow','item'));
    }

    public function setGrafikPenjualan(Request $request)
    {
        $req = $request->all();
        
        $tahun = $req['tahun'];
        $bulan = (int) $req['bulan'];
        $item = $req['item'];
        $dataGrafik = [];
        $listBulan = ['1' => 'Januari',
                      '2' => 'Februari',
                      '3' => 'Maret',
                      '4' => 'April',
                      '5' => 'Mei',
                      '6' => 'Juni',
                      '7' => 'Juli',
                      '8' => 'Agustus',
                      '9' => 'September',
                      '10' => 'Oktober',
                      '11' => 'November',
                      '12' => 'Desember'];

        if(!empty($tahun) && empty($bulan) && empty($item) ){
            $dataGrafik1 = [
                                 ['Jan', 0],
                                 ['Feb',0],
                                 ['Mar',0],
                                 ['Apr',0],
                                 ['May',0],
                                 ['Jun',0],
                                 ['Jul',0],
                                 ['Agu',0],
                                 ['Sep',0],
                                 ['Oct',0],
                                 ['Nov',0],
                                 ['Dec',0]
                          ];

            $itemAll = Item::orderBy('nama_item','ASC')->select('id', 'nama_item')->get();
            $a=0;

            foreach ($itemAll as $key => $value) {
               $dataGrafik[] = ['name' => $value->nama_item,'data' => $dataGrafik1 ];
               $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                           ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                    return Carbon::parse($d->aa)->format('m');
                                });

                foreach ($dataItem as $key1 => $value1) {
                    $dataGrafik[$a]['data'][$key1-1][1] = $value1->sum('jumlah');
                }          
                
            $a++;
            }

            $response = ['grafik' => 1, 'data' => $dataGrafik , 'title' => 'Rekapitulasi Penjualan Tahun '.$tahun];
        }else if(!empty($tahun) && !empty($bulan) && empty($item)){
            $startTgl = (int) Carbon::now()->startOfMonth()->format('d');
            $endTgl = (int) Carbon::now()->endOfMonth()->format('d');

            for($i=$startTgl; $i<=$endTgl; $i++){
              $dataGrafik1[] = ["$i", 0];
            }
       
            $itemAll = Item::orderBy('nama_item','ASC')->select('id', 'nama_item')->get();          
            $a=0;
            foreach ($itemAll as $key => $value) {
               $dataGrafik[] = ['name' => $value->nama_item,'data' => $dataGrafik1 ];
               $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                           ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->whereMonth('b.tgl_bayar','=',$bulan)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                return Carbon::parse($d->aa)->format('d');
                          });

                foreach ($dataItem as $key1 => $value1) {
                   $index = $key1 - 1;
                   $dataGrafik[$a]['data'][$index][1] = $value1->sum('jumlah');
                  // echo $value1->sum('jumlah')." <br/>";
                }          
                
            $a++;
            }

            $response = ['grafik' => 2, 'data' => $dataGrafik, 'title' => 'Rekapitulasi Penjualan Dibulan '.$listBulan[$bulan].' Tahun '.$tahun ];

        }else if( !empty($item) && !empty($tahun) && empty($bulan) ){
            $arrItem = explode(",",$item);
            $item = Item::selectRaw("id,nama_item")->whereIn('id',$arrItem)->orderBy("id",'ASC')->get();
            $titleItem = Item::selectRaw("GROUP_CONCAT(nama_item) as items")->whereIn('id',$arrItem)->first();
            
            $a=0;
            foreach ($item as $key) {
                $dataGrafik[] = ['name' => $key->nama_item, 
                                 'data' => [['Jan', 0],
                                              ['Feb',0],
                                              ['Mar',0],
                                              ['Apr',0],
                                              ['May',0],
                                              ['Jun',0],
                                              ['Jul',0],
                                              ['Agu',0],
                                              ['Sep',0],
                                              ['Oct',0],
                                              ['Nov',0],
                                              ['Dec',0]]
                                ];
                    $jumBeli = Item::join('item_transaksi as a','a.item_id','=','item.id')
                                ->join('transaksi as b','a.transaksi_id','=','b.id')
                                ->selectRaw("item.id,item.nama_item,b.tgl_bayar,a.jumlah")
                                ->where('b.tgl_bayar','!=','')
                                 ->where('b.status','!=','3')
                                ->whereYear('tgl_bayar','=',$tahun)
                                ->where('item.id', $key->id)
                                ->get()
                                ->groupBy(function($d){
                                    return Carbon::parse($d->tgl_bayar)->format('m');
                                });
                    foreach ($jumBeli as $key => $value) {
                        $dataGrafik[$a]['data'][$key - 1][1] = $value->sum('jumlah');          
                    }
            $a++;
            }
            $response = ['grafik' => 1, 'data' => $dataGrafik, 'title' => 'Rekapitulasi Penjualan '.$titleItem->items.' Ditahun '.$tahun ];

        }else if(!empty($tahun) && !empty($bulan) && !empty($item)){
            $arrItem = explode(",",$item);
            $titleItem = Item::selectRaw("GROUP_CONCAT(nama_item) as items")->whereIn('id',$arrItem)->first();
            
            $startTgl = (int) Carbon::now()->startOfMonth()->format('d');
            $endTgl = (int) Carbon::now()->endOfMonth()->format('d');

            for($i=$startTgl; $i<=$endTgl; $i++){
              $dataGrafik1[] = ["$i", 0];
            }

            $itemAll = Item::orderBy('nama_item','ASC')->select('id', 'nama_item')->whereIn('id',$arrItem)->get();          
            $a=0;
            foreach ($itemAll as $key => $value) {
               $dataGrafik[] = ['name' => $value->nama_item,'data' => $dataGrafik1 ];
               $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                           ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->whereMonth('b.tgl_bayar','=',$bulan)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                return Carbon::parse($d->aa)->format('d');
                          });

                foreach ($dataItem as $key1 => $value1) {
                   $index = $key1 - 1;
                   $dataGrafik[$a]['data'][$index][1] = $value1->sum('jumlah');
                }          
                
            $a++;
            }

            $response = ['grafik' => 2, 'data' => $dataGrafik, 'title' => 'Rekapitulasi Penjualan '.$titleItem->items.' Dibulan '.$listBulan[$bulan].' Tahun '.$tahun ];
        }
        return response($response);
    }
    //END Penjualan

    //Penjualan Data
    public function showDataPenjualan()
    {
      

      $kop = "Data Penjualan Sampai Hari Ini / ".Carbon::now()->format('d M Y');
      $input = ['mt' => "", 'st' => "" ];

      $data = Item::join('item_transaksi as a','item.id','=','a.item_id');

      $menu_active = "laporan|penjualan|0";
      return view('laporan.lap_data_penjualan',compact('menu_active','input','kop'));
    }

    public function filterLapDataPenjualan(Request $request)
    {
       $req = $request->all();
       $input = ['mt' => $req['mt'], 'st' => $req['st'] ];

       if(!empty($req['mt'])){
            $explode1 = explode("/", $req['mt']);
            $conv1 = $explode1[1]."/".$explode1[0]."/".$explode1[2];

            $mt = Carbon::parse($conv1)->toDateString();
            $kop_mt = Carbon::parse($conv1)->format('d M Y');
       }

       if(!empty($req['st'])){
            $explode2 = explode("/", $req['st']);
            $conv2 = $explode2[1]."/".$explode2[0]."/".$explode2[2];

            $st = Carbon::parse($conv2)->toDateString();
            $kop_st = Carbon::parse($conv2)->format('d M Y');
       }

       if( !empty($req['mt']) && empty($req['st']) ){
            
            $kop = "Data Penjualan Di Tanggal $kop_mt";
       }else if( empty($req['mt']) && !empty($req['st']) ){
           
            $kop = "Data Penjualan Sampai Tanggal $kop_st";
       }else if( !empty($req['mt']) && !empty($req['st']) ){
             
             $kop = "Data Penjualan Mulai Tanggal $kop_mt S/D $kop_st";
       }else{
            return redirect()->route('lap_pendapatan');
       }


       $menu_active = "laporan|penjualan|0";
       return view('laporan.lap_data_penjualan',compact('menu_active','input','kop'));

    }

    public function ExportPenjualan(Request $request)
    {
      $req = $request->all();
      $data = $this->MasterDataPenjualan($req);

      // return $data;
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.penjualan', compact('data'))->setPaper('f4', 'landscape');
      // return view('export.penjualan', compact('data'));
      return $pdf->stream('laporan-penjualan-'.date('YmdHis').'.pdf');
    }

    public function setDataPenjualan(Request $request)
    {
       $req = $request->all();
       $response = $this->MasterDataPenjualan($req);
       return response($response);
    }

    public function MasterDataPenjualan($req)
    {
       $tahun = $req['tahun'];
       $bulan = $req['bulan'];
       $item = $req['item'];
       $dataTable = [];

       $listBulan = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Agu','Sep','Oct','Nov','Dec'];
       $listBulan1 = ['1' => 'Januari',
                      '2' => 'Februari',
                      '3' => 'Maret',
                      '4' => 'April',
                      '5' => 'Mei',
                      '6' => 'Juni',
                      '7' => 'Juli',
                      '8' => 'Agustus',
                      '9' => 'September',
                      '10' => 'Oktober',
                      '11' => 'November',
                      '12' => 'Desember'];
       $hari = [];

       if(!empty($tahun) && empty($bulan) && empty($item) ){
          $item = Item::orderBy('nama_item','ASC')->select('id','nama_item')->get();
          $a=0;

          foreach ($item as $key => $value) {
              $allItem[] = ['id' => $value->id, 'nama_item' => $value->nama_item,'jumlah' => [0,0,0,0,0,0,0,0,0,0,0,0], 'totalJ' => 0 ];
              $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                          ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                    return Carbon::parse($d->aa)->format('m');
                                });
                $totalA = 0;
                foreach ($dataItem as $key1 => $value1) {
                    $allItem[$a]['jumlah'][$key1-1] = $value1->sum('jumlah');
                    $allItem[$a]['totalJ'] = $totalA += $value1->sum('jumlah');
                }          
          $a++;
          }
          $statisticCollection = collect($allItem);
          $sorted = $statisticCollection->sortByDesc('totalJ');
          $fixItem =  $sorted->values()->all();
          

          $response = ['table' => 1, 'data' => $fixItem, 'columns' => $listBulan, 'kop' => 'Rekapitulasi Penjualan Selama Tahun '.$tahun, 'kopHeader' => 'Bulan'];
       }else if(!empty($tahun) && empty($bulan) && !empty($item) ){
            $arrItem = explode(",", $item);
            $item = Item::orderBy('nama_item','ASC')->select('id','nama_item')->whereIn('id',$arrItem)->get();
            $titleItem = Item::selectRaw("GROUP_CONCAT(nama_item) as items")->whereIn('id',$arrItem)->first();
            $a=0;

            foreach ($item as $key => $value) {
                $allItem[] = ['id' => $value->id, 'nama_item' => $value->nama_item,'jumlah' => [0,0,0,0,0,0,0,0,0,0,0,0], 'totalJ' => 0 ];
                $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                            ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                            ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                            ->where('item.id','=',$value->id)
                             ->where('b.status','!=','3')
                            ->whereYear('b.tgl_bayar','=',$tahun)
                            ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                            ->get()
                            ->groupBy(function($d){
                                      return Carbon::parse($d->aa)->format('m');
                                  });
                  $totalA = 0;
                  foreach ($dataItem as $key1 => $value1) {
                      $allItem[$a]['jumlah'][$key1-1] = $value1->sum('jumlah');
                      $allItem[$a]['totalJ'] = $totalA += $value1->sum('jumlah');
                  }          
            $a++;
            }
            $statisticCollection = collect($allItem);
            $sorted = $statisticCollection->sortByDesc('totalJ');
            $fixItem =  $sorted->values()->all();
            

            $response = ['table' => 1, 'data' => $fixItem, 'columns' => $listBulan, 'kop' => 'Rekapitulasi Penjualan '.$titleItem->items.' Ditahun '.$tahun , 'kopHeader' => 'Bulan'];
       }else if(!empty($tahun) && !empty($bulan) && empty($item)){
            $startTgl = (int) Carbon::now()->startOfMonth()->format('d');
            $endTgl = (int) Carbon::now()->endOfMonth()->format('d');

            for($i=$startTgl; $i<=$endTgl; $i++){
               $hari[] = 0;
               $columns[] = $i;
            }
            
            $item = Item::orderBy('nama_item','ASC')->select('id', 'nama_item')->get();          
            $a=0;
            foreach ($item as $key => $value) {
               $allItem[] = ['id' => $value->id,'nama_item' => $value->nama_item,'jumlah' => $hari, 'totalJ' => 0 ];
               $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                           ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->whereMonth('b.tgl_bayar','=',$bulan)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                return Carbon::parse($d->aa)->format('d');
                          });

                $totalA = 0;
                foreach ($dataItem as $key1 => $value1) {
                   $index = $key1 - 1;
                   $allItem[$a]['jumlah'][$index] = $value1->sum('jumlah');
                   $allItem[$a]['totalJ'] = $totalA += $value1->sum('jumlah');
                  // echo $value1->sum('jumlah')." <br/>";
                }          
                
            $a++;
            }
            $statisticCollection = collect($allItem);
            $sorted = $statisticCollection->sortByDesc('totalJ');
            $fixItem =  $sorted->values()->all();
            // $response = ['grafik' => 2, 'data' => $dataGrafik, 'title' => 'Rekapitulasi Penjualan Dibulan '.$listBulan[$bulan].' Tahun '.$tahun ];

            $response = ['table' => 2, 'data' => $fixItem, 'columns' => $columns, 'kop' => 'Rekapitulasi Penjualan Dibulan '.$listBulan1[$bulan].' Tahun '.$tahun, 'kopHeader' => 'Hari Di Bulan '.$listBulan1[$bulan]];
       }else if(!empty($tahun) && !empty($bulan) && !empty($item)){
            $arrItem = explode(",", $item);
            $startTgl = (int) Carbon::now()->startOfMonth()->format('d');
            $endTgl = (int) Carbon::now()->endOfMonth()->format('d');

            for($i=$startTgl; $i<=$endTgl; $i++){
               $hari[] = 0;
               $columns[] = $i;
            }
            
            $item = Item::orderBy('nama_item','ASC')->select('id','nama_item')->whereIn('id',$arrItem)->get();
            $titleItem = Item::selectRaw("GROUP_CONCAT(nama_item) as items")->whereIn('id',$arrItem)->first();       
            $a=0;
            foreach ($item as $key => $value) {
               $allItem[] = ['id' => $value->id,'nama_item' => $value->nama_item,'jumlah' => $hari, 'totalJ' => 0 ];
               $dataItem = Item::leftJoin('item_transaksi as a','a.item_id','=','item.id')
                          ->leftJoin('transaksi as b','b.id','=','a.transaksi_id')
                          ->selectRaw('item.id,item.nama_item,sum(a.jumlah) as jumlah,date(b.tgl_bayar) as aa')
                          ->where('item.id','=',$value->id)
                           ->where('b.status','!=','3')
                          ->whereYear('b.tgl_bayar','=',$tahun)
                          ->whereMonth('b.tgl_bayar','=',$bulan)
                          ->groupBy('item.id','item.nama_item','b.tgl_bayar')
                          ->get()
                          ->groupBy(function($d){
                                return Carbon::parse($d->aa)->format('d');
                          });

                $totalA = 0;
                foreach ($dataItem as $key1 => $value1) {
                   $index = $key1 - 1;
                   $allItem[$a]['jumlah'][$index] = $value1->sum('jumlah');
                   $allItem[$a]['totalJ'] = $totalA += $value1->sum('jumlah');
                  // echo $value1->sum('jumlah')." <br/>";
                }          
                
            $a++;
            }
            $statisticCollection = collect($allItem);
            $sorted = $statisticCollection->sortByDesc('totalJ');
            $fixItem =  $sorted->values()->all();
            // $response = ['grafik' => 2, 'data' => $dataGrafik, 'title' => 'Rekapitulasi Penjualan Dibulan '.$listBulan[$bulan].' Tahun '.$tahun ];

            $response = ['table' => 2, 'data' => $fixItem, 'columns' => $columns, 'kop' => 'Rekapitulasi Penjualan '.$titleItem->items.' Dibulan '.$listBulan1[$bulan].' Tahun '.$tahun, 'kopHeader' => 'Hari Di Bulan '.$listBulan1[$bulan]];
       }
       return $response;
    }

    // Lap Pemesanan
    public function LapPemesanan()
    {

      $dates = [ Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d'),'1','1'];

      $result = $this->SetDataPemesanan($dates);
  

      $input = ['mulai_tanggal' => Carbon::now()->format('d/m/Y'), 
                'sampai_tanggal' => Carbon::now()->format('d/m/Y'),
                'sort_by' => '1',
                'opsi_sort' => '1' ];
      $menu_active = "laporan|pemesanan|0";

      return view('laporan.lap_pemesanan',compact('menu_active','input','result') );
    }

    public function FilterLaporanPemesanan(Request $request)
    {
       $req = $request->all();
       $validator = \Validator::make($req,['mulai_tanggal' => 'required|date_format:d/m/Y', 
                                           'sampai_tanggal' => 'required|date_format:d/m/Y']);
       if($validator->fails()){
         return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
       }

       $explode = explode('/',$req['mulai_tanggal']);
       $explode1 = explode('/',$req['sampai_tanggal']);

       $mt = $explode[2]."-".$explode[1]."-".$explode[0];
       $st = $explode1[2]."-".$explode1[1]."-".$explode1[0];
       
       $dates = [$mt, $st, $req['sort_by'], $req['opsi_sort']];
       $result = $this->SetDataPemesanan($dates);
      
       
       $input = ['mulai_tanggal' => $req['mulai_tanggal'], 
                 'sampai_tanggal' => $req['sampai_tanggal'],
                 'opsi_sort' => $req['opsi_sort'],
                 'sort_by' => $req['sort_by']
                ];
       $menu_active = "laporan|pemesanan|0";

       return view('laporan.lap_pemesanan',compact('menu_active','input','result') );
    }

    public function ExportPemesanan(Request $request)
    {
       $req = $request->all();
        
       $dates = [$req['mulai_tanggal'], $req['sampai_tanggal'], $req['sort_by'], $req['opsi_sort']];
       $data = $this->SetDataPemesanan($dates);

       $explode = explode('-',$req['mulai_tanggal']);
       $explode1 = explode('-',$req['sampai_tanggal']);

       $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];
       $end_tanggal = $explode1[2]."/".$explode1[1]."/".$explode1[0];

       $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.pemesanan', compact('data', 'start_tanggal','end_tanggal'));
         return $pdf->stream('laporan-pemesanan-'.$start_tanggal.'-'.$end_tanggal.'.pdf');

      // return View('export.pemesanan', compact('data', 'start_tanggal','end_tanggal'));
     
    }

    public function SetDataPemesanan($data1)
    {

      $sort_by = ['1' => 'no_transaksi',
                  '2' => 'nama',
                  '3' => 'tgl_pesan',
                  '4' => 'tgl_selesai',
                  '5' => 'jam',
                  '6' => 'status_order',
                  '7' => 'tampil_metode_pembayaran',
                  '8' => 'pencatat',
                  '9' => 'pencatat_finish',
                  '10' => 'total',
                  '11' => 'uang_muka',
                  '12' => 'sisa_bayar'

                 ];
     
      if($data1[0] == $data1[1]){
          $select = Transaksi::leftJoin('preorders as a', 'transaksi.id','=','a.transaksi_id')
                          ->select('transaksi.*')
                          
                          ->where(function($q) use($data1){
                            return $q->where('transaksi.jenis','2')
                                     ->whereIn('transaksi.status',['1','5'])
                                     ->where('for_ps','1')
                                     ->whereDate('transaksi.updated_at',$data1[0]);
                          })
                          ->orWhere(function($b) use($data1){
                            return $b->where('transaksi.jenis','1')
                                     ->whereIn('transaksi.metode_pembayaran',['1','2'])
                                     ->whereIn('transaksi.status',['1','2','5'])
                                     ->where('for_ps','1')
                                     ->whereDate('transaksi.updated_at',$data1[0]);
                          })
                          ->orWhere(function($c) use($data1){
                            return $c->where('transaksi.jenis','1')
                                     ->whereIn('transaksi.metode_pembayaran',['3','4'] )
                                     ->whereIn('transaksi.status',['1','5'])
                                     ->where('for_ps','1')
                                     ->whereDate('transaksi.updated_at',$data1[0]);
                          });
           $data_cancel = Transaksi::where('status','3') ->whereDate('transaksi.updated_at',$data1[0])->get();
                          
                 
      }else{
          $select = Transaksi::leftJoin('preorders as a', 'transaksi.id','=','a.transaksi_id')
                          ->select('transaksi.*')
                          ->where(function($q) use($data1){
                            return $q->where('transaksi.jenis','2')
                                     ->whereIn('transaksi.status',['1','5'])
                                     ->where('for_ps','1')
                                     ->where('transaksi.updated_at','>=', $data1[0]." 00:00:00")
                                     ->where('transaksi.updated_at','<=', $data1[1]." 23:59:59");
                                     // ->whereBetween('transaksi.created_at',$data1);
                          })
                          ->orWhere(function($b) use($data1){
                            return $b->where('transaksi.jenis','1')
                                     ->whereIn('transaksi.metode_pembayaran',['1','2'])
                                     ->whereIn('transaksi.status',['1','2','5'])
                                     ->where('for_ps','1')
                                     // ->whereBetween('transaksi.created_at',$data1);
                                     ->where('transaksi.updated_at','>=',$data1[0]." 00:00:00")
                                     ->where('transaksi.updated_at','<=',$data1[1]." 23:59:59");
                          })
                          ->orWhere(function($c) use($data1){
                            return $c->where('transaksi.jenis','1')
                                     ->whereIn('transaksi.metode_pembayaran',['3','4'])
                                     ->whereIn('transaksi.status',['1','5'])
                                     ->where('for_ps','1')
                                     // ->whereBetween('transaksi.created_at',$data1);
                                     ->where('transaksi.updated_at','>=',$data1[0]." 00:00:00")
                                     ->where('transaksi.updated_at','<=',$data1[1]." 23:59:59");
                          });
           $data_cancel = Transaksi::where('status','3')->whereBetween('transaksi.created_at',$data1)->get();              
      }

      $data = $select->get();
      
     
      $data->map(function($data){
        if($data->jenis == "2"){
          $data['nama'] = $data->Preorder->nama;
          $data['tgl_pesan'] = $data->created_at->format('d/m/Y');
          $data['tgl_selesai'] = $data->Preorder->tgl_selesai->format('d/m/Y');
          $data['jam'] = $data->Preorder->waktu_selesai;
          $data['total'] = $data->Preorder->total;
          $data['uang_muka'] = $data->Preorder->uang_muka;
          $data['sisa_bayar'] = $data->Preorder->sisa_bayar;
          if($data->Preorder->pencatat_entri == '-'){
             $data['pencatat'] = $data->User->name;
           }else{
             $data['pencatat'] = $data->Preorder->KasirDp->name;
           }

          if($data->Preorder->pencatat_pengambilan == '-'){
            $data['pencatat_finish'] = '';
          }else{
            $data['pencatat_finish'] = $data->Preorder->KasirLunas->name;
          }
          
          $data['tampil_metode_pembayaran'] = 'Pemesanan';
        }else{
          $data['nama'] = $data->User->name;
          if($data->status == '5'){
            $data['tgl_selesai'] = $data->updated_at->format('d/m/Y');
            $data['jam'] = $data->updated_at->format('H:i');
          }else{
            $data['tgl_selesai'] = '';
            $data['jam'] = '';
          }

          $data['total'] = $data->total_bayar;
          $data['uang_muka'] = 0;
          $data['sisa_bayar'] = 0;
          $data['tgl_pesan'] = $data->created_at->format('d/m/Y');
         
          
          if($data->metode_pembayaran == '1'){
            $data['tampil_metode_pembayaran'] = 'TopUp';
            $data['pencatat'] = '-';
            $data['pencatat_finish'] = '-';
          }elseif($data->metode_pembayaran == '2'){
            $data['tampil_metode_pembayaran'] = 'Bank Transfer';
            $data['pencatat'] = '-';
            $data['pencatat_finish'] = '-';
          }elseif($data->metode_pembayaran == '3'){
            $data['tampil_metode_pembayaran'] = 'Bayar Ditoko';
            // $data['pencatat'] = $data->KasirM->name;
            $data['pencatat'] = '-';
            if($data->status == '5'){
              $data['pencatat_finish'] = $data->KasirM->name;
            }else{
              $data['pencatat_finish'] = '-';
            }
            
          }elseif($data->metode_pembayaran == '4'){
            $data['tampil_metode_pembayaran'] = 'COD';
            // $data['pencatat'] = $data->KasirM->name;
            $data['pencatat'] = '-';
            if($data->status == '5'){
              $data['pencatat_finish'] = '-';
            }else{
              $data['pencatat_finish'] = '-';
            }
          }

        }

        if($data->status == "5"){
          if($data->metode_pembayaran == "1" || $data->metode_pembayaran == "2" || $data->metode_pembayaran == "4"){
            $data['status_order'] = 'Sudah Diterima';
          }else{
            $data['status_order'] = 'Sudah Diambil';
          }
        }else{
          if($data->metode_pembayaran == "1" || $data->metode_pembayaran == "2" || $data->metode_pembayaran == "4"){
            $data['status_order'] = 'Belum Diterima';
          }else{
            $data['status_order'] = 'Belum Diambil';
          }
        }

      });

      $grand_total_th = number_format($data->sum('total'),'0','','.');
      $grand_total_dp = number_format($data->sum('uang_muka'),'0','','.');
      $grand_total_sisa = number_format($data->sum('sisa_bayar'),'0','','.');

     
      $data_cancel->map(function($data_cancel){
        if($data_cancel->jenis == "2"){
          $data_cancel['total'] = $data_cancel->Preorder->total;
          $data_cancel['uang_muka'] = $data_cancel->Preorder->uang_muka;
          $data_cancel['sisa_bayar'] = $data_cancel->Preorder->sisa_bayar;
        }else{
          $data_cancel['total'] = $data_cancel->total_bayar;
          $data_cancel['uang_muka'] = 0;
          $data_cancel['sisa_bayar'] = 0;
        }
      });
      
      $pembatalan_transaksi_th = number_format($data_cancel->sum('total'),'0','','.');
      $pembatalan_transaksi_dp = number_format($data_cancel->sum('uang_muka'),'0','','.');

      $total_transaksi_th = number_format($data->sum('total') - $data_cancel->sum('total'),'0','','.');
      $total_transaksi_dp = number_format($data->sum('uang_muka') - $data_cancel->sum('uang_muka'),'0','','.');
      
      $tfoot = (object)['grand_total_th' => $grand_total_th,
                        'grand_total_dp' => $grand_total_dp,
                        'grand_total_sisa' => $grand_total_sisa,
                        'pembatalan_transaksi_th' => $pembatalan_transaksi_th,
                        'pembatalan_transaksi_dp' => $pembatalan_transaksi_dp,
                        'total_transaksi_th' => $total_transaksi_th,
                        'total_transaksi_dp' => $total_transaksi_dp
                       ];


     if($data1[3] == '1'){
          return ['data' => $data->sortBy($sort_by[$data1[2]])->values()->all(), 
                    'tfoot' => $tfoot];

         // return $data->sortBy($sort_by[$data1[2]])->values()->all();
     }elseif($data1[3] == '2'){
          return ['data' => $data->sortByDesc($sort_by[$data1[2]])->values()->all(), 
                    'tfoot' => $tfoot];
         // return $data->sortByDesc($sort_by[$data1[2]])->values()->all();
     }
      
     
     
     // return $result;
    }
    // END Lap Pemesanan

    // Lap Kas
    public function LapKas()
    {
      $dates = [Carbon::now()->format('Y-m-d'),'1','1'];
      $data = $this->SetDataKas($dates);

      $input = ['tanggal' => Carbon::now()->format('d/m/Y'),'sort_by' => '1','opsi_sort' => '1' ];
      $menu_active = "laporan|kas|0";
      return view('laporan.lap_kas',compact('menu_active','input','data'));
    } 

    public function CariLaporanKas(Request $request)
    {
      $req = $request->all();
      $validator = \Validator::make($req,['tanggal' => 'required|date_format:d/m/Y']);
      if($validator->fails()){
       return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      $explode = explode('/',$req['tanggal']);

      $dates = [$explode[2]."-".$explode[1]."-".$explode[0], $req['sort_by'],$req['opsi_sort']];
      $data = $this->SetDataKas($dates);

      $input = ['tanggal' => $req['tanggal'], 'sort_by' => $req['sort_by'],'opsi_sort' => $req['opsi_sort']];
      $menu_active = "laporan|kas|0";

      // dd($data);
      return view('laporan.lap_kas',compact('menu_active','input','data'));
    }

    public function ExportKas(Request $request)
    {

      $dates = [$request->tanggal, $request->sort_by, $request->opsi_sort];
      $data = $this->SetDataKas($dates);

      $data->map(function($data){
        $data['total_pendapatan'] = $data->transaksi - $data->total_refund;
        $data['kas_tersedia'] = $data->saldo_awal +  $data->transaksi - $data->total_refund;
        
      });

    
      $explode = explode("-", $request->tanggal);
      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.kas', compact('data', 'start_tanggal'));

      return $pdf->stream('laporan-kas-'.$start_tanggal.'.pdf');

    }

    public function SetDataKas($data)
    { 
      $sort_by = ['1' => 'nama_kasir',
                  '2' => 'created_at',
                  '3' => 'saldo_awal',
                  '4' => 'transaksi',
                  '5' => 'total_refund',
                  '6' => 'saldo_akhir'
                 ];
      $opsi_sort = ['1' => 'ASC','2' => 'DESC'];

      $kas = Kas::selectRaw('kas.*,
                 (select name from users where id = kas.user_id) as nama_kasir,
                 (select sum(cash) from transaksi where kasir_id = kas.user_id and date(tgl_bayar) = date(kas.created_at)) as total_cash,
                 (select sum(transfer) from transaksi where kasir_id = kas.user_id and date(tgl_bayar) = date(kas.created_at)) as total_transfer,
                 (select sum(qris) from transaksi where kasir_id = kas.user_id and date(tgl_bayar) = date(kas.created_at)) as total_qris
                ')
             ->whereDate('created_at',$data[0])
             ->orderBy($sort_by[$data[1]], $opsi_sort[$data[2]])
             ->get();
      return $kas ; 
    }
    //END LAP KAS

    // START LAP REPRINT STRUK
    public function LapReprintStruk()
    {
      $dates = [Carbon::now()->format('Y-m-d'), '1', '1'];
      $data = $this->SetDataReprintStruk($dates);

      $input = ['tanggal' => Carbon::now()->format('d/m/Y'), 'sort_by' => '1', 'opsi_sort' => '1'];
      $menu_active = "laporan|struk|0";
      
      return view('laporan.lap_reprint_struk', compact('menu_active', 'input', 'data'));
    }

    public function CariReprintStruk(Request $request)
    {
      $req = $request->all();
      $validator = \Validator::make($req, ['tanggal' => 'required|date_format:d/m/Y']);
      
      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->with('gagal', 'simpan')->withInput();
      }

      $explode = explode('/', $req['tanggal']);
      $dates = [$explode[2] . "-" . $explode[1] . "-" . $explode[0], $req['sort_by'] ?? '1', $req['opsi_sort'] ?? '1'];
      $data = $this->SetDataReprintStruk($dates);

      $input = ['tanggal' => $req['tanggal'], 'sort_by' => $req['sort_by'] ?? '1', 'opsi_sort' => $req['opsi_sort'] ?? '1'];
      $menu_active = "laporan|struk|0";
      
      return view('laporan.lap_reprint_struk', compact('menu_active', 'input', 'data'));
    }

    public function SetDataReprintStruk($data)
    {
      $sort_by = ['1' => 'no_transaksi',
            '2' => 'nama',
            '3' => 'tgl_bayar',
            '4' => 'total_bayar',
            '5' => 'metode_pembayaran',
            '6' => 'nama_kasir'
             ];
      
      $opsi_sort = ['1' => 'ASC', '2' => 'DESC'];

      $transaksi = Transaksi::selectRaw("transaksi.*,
                        (SELECT name FROM users WHERE id = transaksi.kasir_id) as nama_kasir")
                  ->whereDate('tgl_bayar', $data[0])
                  ->where('status', '!=', '3')
                  ->orderBy($sort_by[$data[1]], $opsi_sort[$data[2]])
                  ->get();

      $transaksi->map(function($transaksi) {
        $transaksi['nama'] = $transaksi->User->name;
        $transaksi['tgl_bayar_format'] = $transaksi->tgl_bayar->format('d/m/Y H:i');
        $transaksi['total_bayar_format'] = number_format($transaksi->total_bayar, '0', '', '.');
        
        if ($transaksi->metode_pembayaran == '1') {
          $transaksi['metode_pembayaran_text'] = 'Top Up';
        } elseif ($transaksi->metode_pembayaran == '2') {
          $transaksi['metode_pembayaran_text'] = 'Bank Transfer';
        } elseif ($transaksi->metode_pembayaran == '3') {
          $transaksi['metode_pembayaran_text'] = 'Bayar Ditoko';
        } elseif ($transaksi->metode_pembayaran == '4') {
          $transaksi['metode_pembayaran_text'] = 'COD';
        }
      });

      return $transaksi;
    }

    public function ReprintStruk($id)
    {
      $transaksi = Transaksi::findOrFail($id);
      $transaksi->ItemTransaksi;
      
      $kasir = User::findOrFail($transaksi->kasir_id);

      $items = ItemTransaksi::join('item as a', 'a.id', '=', 'item_transaksi.item_id')
            ->select(
              'item_transaksi.id',
              'item_transaksi.transaksi_id',
              'item_transaksi.item_id as product_id',
              'a.nama_item as product_name',
              'item_transaksi.jumlah as qty',
              'item_transaksi.harga as price',
              'item_transaksi.total'
            )
            ->where('item_transaksi.transaksi_id', $id)
            ->where('item_transaksi.status', '1')
            ->get();
      
      $transaksi['nama_kasir'] = $kasir->name;
      $transaksi['items'] = $items;
      $transaksi['tgl_bayar_format'] = $transaksi->tgl_bayar->format('d/m/Y H:i');
      $total_uang_bayar = $transaksi->cash + $transaksi->transfer + $transaksi->qris;
      $transaksi['total_uang_bayar'] = number_format($total_uang_bayar, 0, '', '.');
      $transaksi['uang_kembali'] = number_format($total_uang_bayar - $transaksi->total_bayar, 0, '', '.');
      
      if ($transaksi->metode_pembayaran == '1') {
        $transaksi['metode_pembayaran_text'] = 'Top Up';
      } elseif ($transaksi->metode_pembayaran == '2') {
        $transaksi['metode_pembayaran_text'] = 'Bank Transfer';
      } elseif ($transaksi->metode_pembayaran == '3') {
        $transaksi['metode_pembayaran_text'] = 'Bayar Ditoko';
      } elseif ($transaksi->metode_pembayaran == '4') {
        $transaksi['metode_pembayaran_text'] = 'COD';
      }

      // dd($transaksi); 
      
      $menu_active = "laporan|struk|1";
      return view('laporan.detail_reprint_struk', compact('menu_active', 'transaksi'));
    }
    // END LAP REPRINT STRUK

    // START LAP TARGET PRODUKSI
    public function LapTargetProduksi(Request $request)
    {
      $req = $request->all();
      $dates = [Carbon::now()->format('Y-m-d'),'1','1'];
      $data = $this->SetDataTargetProduksi($dates);

      if(isset($req['tanggal'])){
        $tanggal = $req['tanggal'];
      }else{
        $tanggal = Carbon::now()->format('d/m/Y');
      }

      $input = ['tanggal' => $tanggal, 'sort_by' => '1', 'opsi_sort' => '1', 'kategori' => '0'];
      $menu_active = "laporan|target_produksi|0";
      $kategoris = Kategori::where('status_aktif', '1')
                 ->orderBy('kategori', 'ASC')
                 ->get();
      // dd($kategoris);
      $pisah_tanggal = explode('/', $tanggal);
      $tanggal_form = $pisah_tanggal[2]."-".$pisah_tanggal[1]."-".$pisah_tanggal[0];

      // $items = Item::where('status_aktif', '1')
      //       ->orderBy('nama_item', 'ASC')
      //       ->get();

      return view('laporan.lap_target_produksi', compact('menu_active', 'input', 'data', 'tanggal_form', 'kategoris'));
    }

    public function CariLaporanTargetProduksi(Request $request)
    {
      $req = $request->all();
      $validator = \Validator::make($req,['tanggal' => 'required|date_format:d/m/Y']);
      if($validator->fails()){
        return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      $explode = explode('/',$req['tanggal']);
      $dates = [$explode[2]."-".$explode[1]."-".$explode[0], $req['sort_by'], $req['opsi_sort'], $req['kategori'] ];
      $data = $this->SetDataTargetProduksi($dates);

      $items = Item::where('status_aktif', '1')
            ->orderBy('nama_item', 'ASC')
            ->get();

      $kategoris = Kategori::where('status_aktif', '1')
          ->orderBy('kategori', 'ASC')
          ->get();

      $tanggal_form = $explode[2]."-".$explode[1]."-".$explode[0];

      $input = ['tanggal' => $req['tanggal'], 'sort_by' => $req['sort_by'], 'opsi_sort' => $req['opsi_sort'], 'kategori' => $req['kategori'] ];
      $menu_active = "laporan|target_produksi|0";
      return view('laporan.lap_target_produksi',compact('menu_active','input','data','items','tanggal_form', 'kategoris') );
    }

    public function SetDataTargetProduksi($data)
    {
      $sort_by = ['1' => 'code',
            '2' => 'nama_item',
            '3' => 'target_produksi',
            '4' => 'realisasi_produksi',
           ];

      $query = Item::where('status_aktif','1');
      
      // Filter berdasarkan kategori jika ada
      if(isset($data[3]) && !empty($data[3]) && $data[3] != '0'){
      $query->where('kategori_id', $data[3]);
      }
      
      $item = $query->get();
      
      $item->map(function($item) use ($data) {
      $produksi = Produksi::where('item_id', $item->id)
        ->whereDate('created_at', $data[0])
        ->orderBy('id', 'DESC')
        ->first();

      $item['realisasi_produksi'] = $produksi ? (int) $produksi->produksi1 : "";
      });
      
      $item->map(function($item) use ($data){
      $target = TargetProduksi::where('item_id',$item->id)
              ->whereDate('target_date',$data[0])
              ->first();
             
      if(isset($target->id)){
        $item['target_produksi'] = $target->target_produksi;
      }else{
        $item['target_produksi'] = "";
      }
      });

      if($data[2] == '1'){
       return $item->sortBy($sort_by[$data[1]])->values()->all();
      }elseif($data[2] == '2'){
       return $item->sortByDesc($sort_by[$data[1]])->values()->all();
      }
    }

    public function PostTargetProduksi(Request $request)
    {
      $req = $request->all();
      
      if(!Auth::attempt(['name' => $req['username'], 'password' => $req['password'] ]))
      return redirect()->back()->with('gagal_modal','simpan')->with('error_auth','Username Atau Password Salah')->withInput();        

      $user = $request->user();
      $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2'])->count();
      // $role = Aproval::where('user_id',$user->id)->where('rule','4')->count();
   
      if($role == 0)
      return redirect()->back()->with('gagal_modal','simpan')->with('error_auth','User Tidak Punya Akses')->withInput();        

      $tanggalToday = Carbon::now()->format('Y-m-d H:i:s');
      $tanggalTarget = $req['tanggal'];
      
      $item = Item::where('status_aktif','1')->select('id')->get();
      foreach ($item as $key) {
        $targetValue = $req['target_produksi_'.$key->id] ?? 0;
        
        if(!empty($targetValue)) {
          $select = TargetProduksi::whereDate('target_date',$tanggalTarget)->where('item_id',$key->id)->first();

          if(isset($select->id)){
            $select->update(['target_produksi' => $targetValue]);
          }else{
            $entri = TargetProduksi::create(['item_id'=>$key->id,
                                      'target_produksi' => $targetValue,
                                      'target_date' => $tanggalTarget,
                                      'created_at' => $tanggalToday
                                    ]);
          }
        }
      }

      return redirect()->route('lap_target_produksi',['tanggal' => Carbon::parse($req['tanggal'])->format('d/m/Y'), 'sort_by' => '1', 'opsi_sort' => '1' ])->with('success','Berhasil Set Target Produksi ')->withInput();
    }

    public function ExportTargetProduksi(Request $request)
    {
      $req = $request->all();
      $dates = [$req['tanggal'], $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTargetProduksi($dates);

      $explode = explode("-", $request->tanggal);
      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.target_produksi', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-target-produksi-'.$start_tanggal.'.pdf');
      // return View('export.produksi', compact('data', 'start_tanggal'));
    } 
    // END LAP TARGET PRODUKSI

    // LAP PRODUKSI
    
    public function LapProduksi(Request $request)
    {
      $dates = [Carbon::now()->format('Y-m-d'),'1','1'];
      $data = $this->SetDataProduksi($dates);

      $input = ['tanggal' => Carbon::now()->format('d/m/Y'), 'sort_by' => '1', 'opsi_sort' => '1'];
      $menu_active = "laporan|produksi|0";
      
      return view('laporan.lap_produksi', compact('menu_active', 'input', 'data'));
    }

     public function CariLaporanProduksi(Request $request)
    {
      $req = $request->all();
      $validator = \Validator::make($req,['tanggal' => 'required|date_format:d/m/Y']);
      if($validator->fails()){
       return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      $explode = explode('/',$req['tanggal']);
      $dates = [$explode[2]."-".$explode[1]."-".$explode[0], $req['sort_by'], $req['opsi_sort'] ];
      $data = $this->SetDataProduksi($dates);

      $input = ['tanggal' => $req['tanggal'], 'sort_by' => $req['sort_by'], 'opsi_sort' => $req['opsi_sort'] ];
      $menu_active = "laporan|produksi|0";
      return view('laporan.lap_produksi',compact('menu_active','input','data'));
    }

    public function SetDataProduksi($data)
    {
      $sort_by = ['1' => 'code',
                  '2' => 'nama_item',
                  '3' => 'total_produksi',
                  '4' => 'catatan',
                ];

      $opsi_sort = ['1' => 'ASC', '2' => 'DESC'];

      $item = Produksi::selectRaw('produksi.*,
        (select code from item where id = produksi.item_id) as code,
        (select nama_item from item where id = produksi.item_id) as nama_item
      ')
      ->whereIn('id',function($q) use ($data){
          $q->from('produksi')
          ->selectRaw('max(produksi.id)')
          ->whereDate('produksi.created_at',$data[0])
          ->groupBy('produksi.item_id')
          ->where(function($query) {
          $query->where('produksi.produksi1', '>', 0)
            ->orWhere('produksi.produksi2', '>', 0)
            ->orWhere('produksi.produksi3', '>', 0);
          });
          return $q;
      })->get();

      // dump($item[0]);

      if($data[2] == '1'){
         return $item->sortBy($sort_by[$data[1]])->values()->all();
      }elseif($data[2] == '2'){
         return $item->sortByDesc($sort_by[$data[1]])->values()->all();
      }

    }

    // END LAP PRODUKSI

    //LAP PERGERAKAN STOCK

    public function LapPergerakanStock()
    {
      $dates = [Carbon::now()->format('Y-m-d'),'1','1'];
      $data = $this->SetDataPergerakanStock($dates);
      

      $input = ['tanggal' => Carbon::now()->format('d/m/Y'),'sort_by' => '1', 'opsi_sort' => '1' ];
      $menu_active = "laporan|pergerakan_stock|0";
      return view('laporan.lap_pergerakan_stock',compact('menu_active','input','data'));
    }

    public function CariPergerakanStock(Request $request)
    {
      $req = $request->all();
      $validator = \Validator::make($req,['tanggal' => 'required|date_format:d/m/Y']);
      if($validator->fails()){
       return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      $explode = explode('/',$req['tanggal']);
      $dates = [$explode[2]."-".$explode[1]."-".$explode[0], $req['sort_by'], $req['opsi_sort'] ];
      $data = $this->SetDataPergerakanStock($dates);

      $input = ['tanggal' => $req['tanggal'], 'sort_by' => $req['sort_by'], 'opsi_sort' => $req['opsi_sort'] ];
      $menu_active = "laporan|pergerakan_stock|0";
      return view('laporan.lap_pergerakan_stock',compact('menu_active','input','data'));
    }

    public function ExportProduksi(Request $request)
    {
      $req = $request->all();
      $dates = [$req['tanggal'], $req['sort_by'], $req['opsi_sort'] ];
      $data = $this->SetDataProduksi($dates);

      $explode = explode("-", $request->tanggal);
      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.produksi', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-produksi-'.$start_tanggal.'.pdf');
      // return View('export.produksi', compact('data', 'start_tanggal'));

    } 

    public function SetDataPergerakanStock($data)
    {
       $sort_by = ['1' => 'code',
                   '2' => 'nama_item',
                   '3' => 'produksi1',
                   '4' => 'penjualan_toko',
                   '5' => 'penjualan_pemesanan',
                   '6' => 'total_penjualan',
                   '7' => 'ket_rusak',
                   '8' => 'ket_lain',
                   '9' => 'sisa_stock',
                   '10' => 'stock_awal'
                  ];

       $opsi_sort = ['1' => 'ASC', '2' => 'DESC'];
       $item = Produksi::selectRaw('produksi.*,
                                   (select code from item where id = produksi.item_id) as code,
                                   (select stock from item where id = produksi.item_id) as stok,
                                   (select nama_item from item where id = produksi.item_id) as nama_item
                                  ')
                        ->whereIn('id',function($q) use ($data){
                           $q->from('produksi')
                           ->selectRaw('max(produksi.id)')
                           ->whereDate('produksi.created_at',$data[0])
                           ->groupBy('produksi.item_id');

                           return $q;
                        })->get();

       $item->map(function($item) use ($data){
        
       $opname = Opname::where('item_id',$item->item_id)
                        ->whereDate('tanggal',$data[0])
                        ->first();

       $stock_akhir = Produksi::where('item_id',$item->item_id)
                          ->whereDate('created_at',$data[0])
                          ->orderBy('id','DESC')
                          ->first();

       if(isset($opname->id)){
          $item['stock_awal'] = $opname->stock_toko;
       }else{
          $item['stock_awal'] = $stock_akhir->stock_awal;
       }

      });

      if($data[2] == '1'){
         return $item->sortBy($sort_by[$data[1]])->values()->all();
      }elseif($data[2] == '2'){
         return $item->sortByDesc($sort_by[$data[1]])->values()->all();
      }


    } 

    //penjualan per item
    public function LaporanPenjualanPerItem(Request $request)
    {
      $dates = [Carbon::now()->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
                '1','1','1',
               ];
      $data = $this->SetDataPenjualanPerItem($dates);

      $input = ['tanggal' => Carbon::now()->format('d/m/Y'), 
                'sampai_tanggal' =>  Carbon::now()->format('d/m/Y'),
                'sort_by' => '1',
                'opsi_sort' => '1',
                'opsi_kasir' => '1',
               ];
      $menu_active = "laporan|penjualan_item|0";

      return view('laporan.lap_penjualan_item',compact('menu_active','input','data'));    
    } 

    public function CariPenjualanPerItem(Request $request)
    {
      $req = $request->all();
      $req['mulai_tanggal'] = $req['tanggal'];
      $validator = \Validator::make($req,['mulai_tanggal' => 'required|date_format:d/m/Y', 
                                           'sampai_tanggal' => 'required|date_format:d/m/Y']);

      if($validator->fails()){
        return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      $explode = explode('/',$req['mulai_tanggal']);
      $explode1 = explode('/',$req['sampai_tanggal']);

      $mt = $explode[2]."-".$explode[1]."-".$explode[0];
      $st = $explode1[2]."-".$explode1[1]."-".$explode1[0];
       
      // dd($req);
      $dates = [$mt, $st, $req['sort_by'], $req['opsi_sort'], $req['opsi_kasir']];

      $data = $this->SetDataPenjualanPerItem($dates);
     
      $input = ['tanggal' => $req['mulai_tanggal'], 
                'sampai_tanggal' => $req['sampai_tanggal'],
                'sort_by' => $req['sort_by'],
                'opsi_sort' => $req['opsi_sort'],
                'opsi_kasir' => $req['opsi_kasir']
                ];

      $menu_active = "laporan|penjualan_item|0";
      return view('laporan.lap_penjualan_item',compact('menu_active','input','data')); 
    }

    public function SetDataPenjualanPerItem($data)
    {
      $sort = ['1' => 'kode_menu',
               '2' => 'nama_item',
               '3' => 'qty',
               '4' => 'total'
              ];
      $opsi = ['1' => 'ASC','2' => 'DESC'];
      //admin dan kasir
      $target_levels = [1, 2, 3]; 

      $kasir_db = User::whereIn('level_id', $target_levels)
                      ->orderBy('name', 'asc')
                      ->pluck('name', 'id')
                      ->toArray();

      $opsi_kasir = [1 => 'Penjualan Total'] + $kasir_db;
      
      // dump($nama_kasir);
      // xxxxxxxxxxxxx
      
      if($data[0] == $data[1]){
        $transaksi = ItemTransaksi::selectRaw("item_transaksi.item_id,
                                                (SELECT nama_item FROM item where id = item_transaksi.item_id ) as nama_item,
                                                (SELECT code FROM item where id = item_transaksi.item_id ) as kode_menu,
                                                sum(jumlah) as qty,
                                                sum(total) as total ")
                                      ->whereIn('transaksi_id',function($q) use ($data){
                                        return $q->from('transaksi')
                                                  ->select('id')
                                                  ->where('status','5')
                                                  ->when($data[4] != 1, function ($q) use ($data) {
                                                      return $q->where('kasir_id', $data[4]);
                                                  })
                                                  ->whereDate('updated_at',$data[0]);
                                      })->groupBy('item_transaksi.item_id')
                                      ->orderBy($sort[$data[2]], $opsi[$data[3]])
                                      ->get();
      }else{
         $transaksi = ItemTransaksi::selectRaw("item_transaksi.item_id,
                                                (SELECT nama_item FROM item where id = item_transaksi.item_id ) as nama_item,
                                                (SELECT code FROM item where id = item_transaksi.item_id ) as kode_menu,
                                                sum(jumlah) as qty,
                                                sum(total) as total ")
                                      ->whereIn('transaksi_id',function($q) use ($data){
                                        return $q->from('transaksi')
                                                  ->select('id')
                                                  ->where('status','5')
                                                  ->when($data[4] != 1, function ($q) use ($data) {
                                                      return $q->where('kasir_id', $data[4]);
                                                  })
                                                  ->where('updated_at','>=', $data[0]." 00:00:00")
                                                  ->where('updated_at','<=', $data[1]." 23:59:59");
                                      })->groupBy('item_transaksi.item_id')
                                      ->orderBy($sort[$data[2]], $opsi[$data[3]])
                                      ->get();
       
      }
      // dump($data);


      $transaksi->map(function($transaksi){
        $transaksi['tampil_total'] = number_format($transaksi->total,'0','','.');
      });
      
      $grandTotal = number_format($transaksi->sum('total'),'0','','.');

      $array = ['data' => $transaksi, 'grandTotal' => $grandTotal, 'opsi_kasir' => $opsi_kasir, 'selected_kasir' => $data[4]];
      return $array;

      // return $data;
      // $data[0] = '2020-12-01';
      // $data[1] = '2020-12-14';
      // if($data[0] == $data[1]){
      //   $transaksi = Produksi::selectRaw('produksi.total_penjualan as qty,
      //                                (select harga from item where id = produksi.item_id) as harga,
      //                                (select code from item where id = produksi.item_id) as kode_menu,
      //                                (select nama_item from item where id = produksi.item_id) as nama_item,
      //                                produksi.total_penjualan * (select harga from item where id = produksi.item_id) as total
      //                               ')
      //                     ->whereIn('id',function($q) use ($data){
      //                        $q->from('produksi')
      //                        ->where('total_penjualan','>',0)
      //                        ->selectRaw('max(produksi.id)')
      //                        ->whereDate('produksi.created_at',$data[0])
      //                        ->groupBy('produksi.item_id');

      //                        return $q;
      //                     })->orderBy($sort[$data[2]], $opsi[$data[3]])
      //                       ->get();
      // }else{
      //    $transaksi = Produksi::selectRaw("max(id),item_id ,total_penjualan,DATE_FORMAT(created_at,'%Y-%m-%d') as tgl")
                         
      //                    ->where('created_at','>=', $data[0]." 00:00:00")
      //                    ->where('created_at','<=', $data[1]." 23:59:59")
                         
      //                    ->get();
      //    return $transaksi;
      //    // $transaksi = Produksi::selectRaw('produksi.total_penjualan as qty,
      //    //                             (select harga from item where id = produksi.item_id) as harga,
      //    //                             (select code from item where id = produksi.item_id) as kode_menu,
      //    //                             (select nama_item from item where id = produksi.item_id) as nama_item,
      //    //                             produksi.total_penjualan * (select harga from item where id = produksi.item_id) as total
      //    //                            ')
      //    //                  ->whereIn('id',function($q) use ($data){
      //    //                     $q->from('produksi')
      //    //                     ->where('total_penjualan','>',0)
      //    //                     ->selectRaw('max(produksi.id)')
      //    //                     ->where('produksi.created_at','>=', $data[0]." 00:00:00")
      //    //                     ->where('produksi.created_at','<=', $data[1]." 23:59:59")
      //    //                     ->groupBy(DB::raw("DATE_FORMAT(produksi.created_at, 'Y-m-d' )"));

      //    //                     return $q;
      //    //                  })->orderBy($sort[$data[2]], $opsi[$data[3]])
      //    //                    ->get();
      // }
      // $transaksi->map(function($transaksi){
      //   $transaksi['tampil_total'] = number_format($transaksi->total,'0','','.');
      // });
      // $grandTotal = number_format($transaksi->sum('total'),'0','','.');
      
      

      // $array = ['data' => $transaksi, 'grandTotal' => $grandTotal];
      // return $array;
    } 

    public function ExportPenjualanPerItem(Request $request)
    {
      $req = $request->all();

      $dates = [$req['tanggal'], $req['sampai_tanggal'], $req['sort_by'], $req['opsi_sort'], $req['opsi_kasir'] ];
      $data = $this->SetDataPenjualanPerItem($dates);
      
      if ($data['selected_kasir'] == 1) {
        $nama_kasir = "Penjualan Total";
      } else {
        $nama_kasir = User::find($data['selected_kasir'])->name;
      }

      $explode = explode('-',$req['tanggal']);
      $explode1 = explode('-',$req['sampai_tanggal']);

      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];
      $end_tanggal = $explode1[2]."/".$explode1[1]."/".$explode1[0];
      // $selectedKasir = $req['opsi_kasir'];


      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.penjualan_per_item', compact('data', 'start_tanggal','end_tanggal', 'nama_kasir'));
         return $pdf->stream('laporan-pemesanan-'.$start_tanggal.'-'.$end_tanggal.'.pdf');

      // $dates = [$req['tanggal']];
      // $data = $this->SetDataPenjualanPerItem($dates);
      
      // $explode = explode("-", $request->tanggal);
      // $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      // $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.penjualan_per_item', compact('data', 'start_tanggal'));
      // return $pdf->stream('laporan-penjualan-per-item'.$start_tanggal.'.pdf');
      // return "Oke";
    } 

    public function LaporanPendapatanHarian()
    {
   
      $dates = [Carbon::now()->startOfMonth()->format('Y-m-d'), 
                Carbon::now()->endOfMonth()->format('Y-m-d'),
                '1','1'];

      $data = $this->SetDataPendapatanHarian($dates);
     
      $input = ['mt' => Carbon::now()->startOfMonth()->format('d/m/Y') , 
                'st' => Carbon::now()->endOfMonth()->format('d/m/Y'), 
                'sort_by' => '1', 'opsi_sort' => '1'];

      $menu_active = "laporan|pendapatan_harian|0";
      return view('laporan.lap_pendapatan_harian',compact('menu_active','input','data'));   
    }

    public function LaporanTaxHarianKasir()
    {
   
      $dates = [Carbon::now()->startOfMonth()->format('Y-m-d'), 
                Carbon::now()->endOfMonth()->format('Y-m-d'),
                '1','1'];

      $data = $this->SetDataTaxHarianKasir($dates);
     

            



      $input = ['mt' => Carbon::now()->startOfMonth()->format('d/m/Y') , 
                'st' => Carbon::now()->endOfMonth()->format('d/m/Y'), 
                'sort_by' => '1', 'opsi_sort' => '1'];

      $menu_active = "laporan_tax|tax_harian_kasir|0";
      return view('laporan.lap_tax_harian_kasir',compact('menu_active','input','data'));   
    }

    public function LaporanTaxHarianPesanan()
    {
   
      $dates = [Carbon::now()->startOfMonth()->format('Y-m-d'), 
                Carbon::now()->endOfMonth()->format('Y-m-d'),
                '1','1'];

      $data = $this->SetDataTaxHarianPesanan($dates);
     

            



      $input = ['mt' => Carbon::now()->startOfMonth()->format('d/m/Y') , 
                'st' => Carbon::now()->endOfMonth()->format('d/m/Y'), 
                'sort_by' => '1', 'opsi_sort' => '1'];

      $menu_active = "laporan_tax|tax_harian_pesanan|0";
      return view('laporan.lap_tax_harian_pesanan',compact('menu_active','input','data'));   
    }

    public function LaporanTaxHarianWeb()
    {
   
      $dates = [Carbon::now()->startOfMonth()->format('Y-m-d'), 
                Carbon::now()->endOfMonth()->format('Y-m-d'),
                '1','1'];




      $data = $this->SetDataTaxHarianWeb($dates);
     

     // return response()->json(['success' => 1, 'msg' => $data], 200);
    

      $input = ['mt' => Carbon::now()->startOfMonth()->format('d/m/Y') , 
                'st' => Carbon::now()->endOfMonth()->format('d/m/Y'), 
                'sort_by' => '1', 'opsi_sort' => '1'];

      $menu_active = "laporan_tax|tax_harian_web|0";
      return view('laporan.lap_tax_harian_web',compact('menu_active','input','data'));   
    }




    public function CariPendapatanHarian(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataPendapatanHarian($dates);

      $input = ['mt' => Carbon::parse($mt)->format('d/m/Y') , 
                'st' => Carbon::parse($st)->format('d/m/Y'),
                'sort_by' => $req['sort_by'],
                'opsi_sort' => $req['opsi_sort']
               ];

      $menu_active = "laporan|pendapatan_harian|0";

      return view('laporan.lap_pendapatan_harian',compact('menu_active','input','data'));  
    }

    public function CariTaxHarianKasir(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianKasir($dates);

      $input = ['mt' => Carbon::parse($mt)->format('d/m/Y') , 
                'st' => Carbon::parse($st)->format('d/m/Y'),
                'sort_by' => $req['sort_by'],
                'opsi_sort' => $req['opsi_sort']
               ];

      $menu_active = "laporan_tax|tax_harian_kasir|0";

      return view('laporan.lap_tax_harian_kasir',compact('menu_active','input','data'));  
    }

    public function CariTaxHarianPesanan(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianPesanan($dates);

      $input = ['mt' => Carbon::parse($mt)->format('d/m/Y') , 
                'st' => Carbon::parse($st)->format('d/m/Y'),
                'sort_by' => $req['sort_by'],
                'opsi_sort' => $req['opsi_sort']
               ];

      $menu_active = "laporan_tax|tax_harian_pesanan|0";

      return view('laporan.lap_tax_harian_pesanan',compact('menu_active','input','data'));  
    }

    public function CariTaxHarianWeb(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianWeb($dates);

      $input = ['mt' => Carbon::parse($mt)->format('d/m/Y') , 
                'st' => Carbon::parse($st)->format('d/m/Y'),
                'sort_by' => $req['sort_by'],
                'opsi_sort' => $req['opsi_sort']
               ];

      $menu_active = "laporan_tax|tax_harian_web|0";

      return view('laporan.lap_tax_harian_web',compact('menu_active','input','data'));  
    }

    public function SetDataPendapatanHarian($data)
    {
      $sort = ['1' => 'tgl',
               '2' => 'total_transaksi',
               '3' => 'total_diskon',
               '4' => 'transaksi'
              ];
      $opsi = ['1' => 'ASC','2' => 'DESC'];

      $transaksi = Kas::selectRaw("date(updated_at) as tgl, sum(transaksi) as total_transaksi, sum(diskon) as total_diskon, 
                                   (sum(transaksi) + sum(diskon) ) as transaksi ")
                        ->whereBetween('updated_at',$data)          
                        ->groupBy(DB::raw('date(updated_at)'))
                        ->orderBy($sort[$data[2]], $opsi[$data[3]])
                        ->get();


      $grandTotalTransaksi = $transaksi->sum('transaksi');
      $grandTotaldiskon = $transaksi->sum('total_diskon');
      $grandTotal = $transaksi->sum('total_transaksi');

      $data = ['data' => $transaksi, 
               'grandTotalTransaksi' => number_format($grandTotalTransaksi),
               'grandTotalDiskon' =>  number_format($grandTotaldiskon),
               'grandTotal' => number_format($grandTotal) ];
      return $data;
    }


    public function SetDataTaxHarianKasir($data)
    {
      $sort = ['1' => 'tgl',
               '2' => 'total_transaksi',
               '3' => 'total_tax',
               '4' => 'transaksi'
              ];
      $opsi = ['1' => 'ASC','2' => 'DESC'];

      $transaksi = Transaksi::selectRaw("date(tgl_bayar) as tgl,sum(total_transaksi) as total_transaksi, sum(tax) as total_tax, 
                                   (sum(total_transaksi) + sum(potongan)) as transaksi")
                                   ->where('jalur','2')
                                   ->where('jenis','1')
                        ->whereBetween('tgl_bayar',$data)          
                        ->groupBy(DB::raw('date(tgl_bayar)'))
                        ->orderBy($sort[$data[2]], $opsi[$data[3]])
                        ->get();

 


      $grandTotalTransaksi = $transaksi->sum('transaksi');
      $grandTotalTax = $transaksi->sum('total_tax');
      $grandTotal = $transaksi->sum('total_transaksi');

      $data = ['data' => $transaksi, 
               'grandTotalTransaksi' => number_format($grandTotalTransaksi),
               'grandTotalTax' =>  number_format($grandTotalTax),
               'grandTotal' => number_format($grandTotal) ];
      return $data;
    }

    public function SetDataTaxHarianWeb($data)
    {
      $sort = ['1' => 'tgl',
               '2' => 'total_transaksi',
               '3' => 'total_tax',
               '4' => 'transaksi'
              ];
      $opsi = ['1' => 'ASC','2' => 'DESC'];

      $transaksi = Transaksi::selectRaw("date(tgl_bayar) as tgl,sum(total_transaksi) as total_transaksi, sum(tax) as total_tax, 
                                   (sum(total_transaksi) + sum(potongan)) as transaksi")
                                   ->where('jalur','1')
                                   ->where('jenis','1')
                                   ->where('status','5')

                        ->whereBetween('tgl_bayar',$data)          
                        ->groupBy(DB::raw('date(tgl_bayar)'))
                        ->orderBy($sort[$data[2]], $opsi[$data[3]])
                        ->get();

 


      $grandTotalTransaksi = $transaksi->sum('transaksi');
      $grandTotalTax = $transaksi->sum('total_tax');
      $grandTotal = $transaksi->sum('total_transaksi');

      $data = ['data' => $transaksi, 
               'grandTotalTransaksi' => number_format($grandTotalTransaksi),
               'grandTotalTax' =>  number_format($grandTotalTax),
               'grandTotal' => number_format($grandTotal) ];
      return $data;
    }

    public function SetDataTaxHarianPesanan($data)
    {
      $sort = ['1' => 'tgl',
               '2' => 'total_transaksi',
               '3' => 'total_tax',
               '4' => 'transaksi'
              ];
      $opsi = ['1' => 'ASC','2' => 'DESC'];

      $transaksi = Transaksi::selectRaw("date(updated_at) as tgl,sum(total_transaksi) as total_transaksi, sum(tax) as total_tax, 
                                   (sum(total_transaksi) + sum(potongan)) as transaksi")
                                   ->where('jalur','2')
                                   ->where('jenis','2')
                                   ->where('for_ps','1')
                        ->whereBetween('updated_at',$data)          
                        ->groupBy(DB::raw('date(updated_at)'))
                        ->orderBy($sort[$data[2]], $opsi[$data[3]])
                        ->get();

 


      $grandTotalTransaksi = $transaksi->sum('transaksi');
      $grandTotalTax = $transaksi->sum('total_tax');
      $grandTotal = $transaksi->sum('total_transaksi');

      $data = ['data' => $transaksi, 
               'grandTotalTransaksi' => number_format($grandTotalTransaksi),
               'grandTotalTax' =>  number_format($grandTotalTax),
               'grandTotal' => number_format($grandTotal) ];
      return $data;
    }




    public function ExportPendapatanHarian(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataPendapatanHarian($dates);

      $start_tanggal = Carbon::parse($mt)->format('d/m/Y')." - ".Carbon::parse($st)->format('d/m/Y');
      
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.pendapatan_harian', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-pendapatan-harian-'.$start_tanggal.'.pdf');

    }

    public function ExportTaxHarianKasir(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianKasir($dates);

      $start_tanggal = Carbon::parse($mt)->format('d/m/Y')." - ".Carbon::parse($st)->format('d/m/Y');
      
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.tax_harian_kasir', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-tax-harian-kasir'.$start_tanggal.'.pdf');

    }

    
  


    
    public function ExportTaxHarianPesanan(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianPesanan($dates);

      $start_tanggal = Carbon::parse($mt)->format('d/m/Y')." - ".Carbon::parse($st)->format('d/m/Y');
      
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.tax_harian_kasir', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-tax-harian-pesanan'.$start_tanggal.'.pdf');

    }

    
    public function ExportTaxHarianWeb(Request $request)
    {
      $req = $request->all();
      if(empty($req['mt'])){
        $mt = Carbon::now()->startOfMonth()->format('Y-m-d');
      }else{
        $explode = explode('/', $req['mt']);
        $mt = $explode[2].'-'.$explode[1].'-'.$explode[0];
      }

      if(empty($req['st'])){
        $st =  Carbon::now()->endOfMonth()->format('Y-m-d');
      }else{
        $explode1 = explode('/', $req['st']);
        $st = $explode1[2].'-'.$explode1[1].'-'.$explode1[0];
      }

      $dates = [$mt." 00:00:00", $st." 23:59:00", $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataTaxHarianWeb($dates);

      $start_tanggal = Carbon::parse($mt)->format('d/m/Y')." - ".Carbon::parse($st)->format('d/m/Y');
      
      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.tax_harian_kasir', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-tax-harian-web'.$start_tanggal.'.pdf');

    }

    // OPNAME
    public function Opname(Request $request)
    {
      $req = $request->all();

      if(isset($req['tanggal'])){
        $tanggal = $req['tanggal'];
      }else{
        $tanggal = Carbon::now()->format('d/m/Y');
      }

      $pisah_tanggal = explode('/', $tanggal);
      $tanggal_form = $pisah_tanggal[2]."-".$pisah_tanggal[1]."-".$pisah_tanggal[0];

      if(isset($req['sort_by'])){
        $sort_by = $req['sort_by'];
      }else{
        $sort_by = '1';
      }

      if(isset($req['opsi_sort'])){
        $opsi_sort = $req['opsi_sort'];
      }else{
        $opsi_sort = '1';
      }

      $input = ['tanggal' => $tanggal ,'sort_by' => $sort_by, 'opsi_sort' => $opsi_sort];
      $menu_active = "laporan|opname|0";

      $explode = explode('/',$tanggal);
      $dates = [$explode[2]."-".$explode[1]."-".$explode[0],$sort_by,$opsi_sort];

      $item = $this->SetDataOpname($dates);
      // return $item;

      return view('laporan.lap_opname', compact('menu_active','input','item','tanggal_form'));
    }

    public function CariOpname(Request $request)
    {
      $req = $request->all();

      $validator = \Validator::make($req,['tanggal' => 'required|date_format:d/m/Y']);
      if($validator->fails()){
        return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
      }

      return redirect()->route('opname', ['tanggal' => $req['tanggal'],'sort_by' => $req['sort_by'], 'opsi_sort' => $req['opsi_sort']]);
    }


    public function ExportOpname(Request $request)
    {
      $req = $request->all();
      $dates = [$req['tanggal'], $req['sort_by'], $req['opsi_sort']];
      $data = $this->SetDataOpname($dates);

      $explode = explode("-", $request->tanggal);
      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.opname', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-opname-'.$start_tanggal.'.pdf');
      // return View('export.produksi', compact('data', 'start_tanggal'));
    } 

    public function PostOpname(Request $request)
    {
      $req = $request->all();

      if(empty($req['username']) || empty($req['password']))
        return redirect()->back()->with('gagal_modal','simpan')->with('error_auth','Username dan Password harus diisi')->withInput();

      if(!Auth::attempt(['name' => $req['username'], 'password' => $req['password']]))
        return redirect()->back()->with('gagal_modal','simpan')->with('error_auth','Username Atau Password Salah')->withInput();

      $user = $request->user();
      $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2'])->count();
      // $role = Aproval::where('user_id',$user->id)->where('rule','4')->count();

      if($role == 0)
        return redirect()->back()->with('gagal_modal','simpan')->with('error_auth','User Tidak Punya Akses')->withInput();

      $tanggal = $req['tanggal'];
      $item = Item::where('status_aktif','1')->select('id')->get();

      foreach ($item as $key) {
        $stock_fisik_pagi = $req['stock_fisik_pagi_'.$key->id] ?? NULL;
        $stock_fisik_malam = $req['stock_fisik_malam_'.$key->id] ?? NULL;
        $stock_toko = $req['stock_toko_'.$key->id] ?? NULL;

        if($stock_fisik_pagi !== NULL || $stock_fisik_malam !== NULL || $stock_toko !== NULL) {
          $opname = Opname::where('item_id',$key->id)->whereDate('tanggal',$tanggal)->first();

          $updateData = [];
          if($stock_fisik_pagi !== NULL) {
            $updateData['stock_fisik_pagi'] = $stock_fisik_pagi;
          }
          if($stock_fisik_malam !== NULL) {
            $updateData['stock_fisik_malam'] = $stock_fisik_malam;
          }
          if($stock_toko !== NULL) {
            $updateData['stock_toko'] = $stock_toko;
          }

          if(isset($opname->id)){
            $opname->update($updateData);
          }else{
            $createData = [
              'item_id' => $key->id,
              'tanggal' => $tanggal,
            ] + $updateData;
            Opname::create($createData);
          }

          // Update sisa_stock di item berdasarkan stock_toko
          if($stock_toko !== NULL) {
            Item::where('id', $key->id)->update(['stock' => $stock_toko]);
          }
        }
      }

      return redirect()->route('opname',['tanggal' => Carbon::parse($tanggal)->format('d/m/Y')])->with('success','Berhasil Simpan Data Opname');
    }

    public function SetDataOpname($data)
    {
      $sort_by = ['1' => 'code',
                  '2' => 'nama_item',
                  '3' => 'stock_masuk',
                  '4' => 'stock_akhir',
                  '5' => 'stock_toko'
                 ];

      $item = Item::where('status_aktif','1')->get();
      $item->map(function($item) use ($data){
        $query = Produksi::where('item_id',$item->id)
                          ->whereDate('created_at',$data[0])
                          ->orderBy('id','DESC')
                          ->first();

        $opname = Opname::where('item_id',$item->id)
                        ->whereDate('tanggal',$data[0])
                        ->first();

                 

        if(isset($query->id)){
           $item['stock_masuk'] = $query->produksi1;
           $item['stock_awal'] = $query->stock_awal;
           $item['produksi'] = $query->produksi1;
           $item['rusak'] = $query->ket_rusak + $query->ket_lain;
           $item['selisih_pagi'] = $query->stock_awal - (isset($opname->stock_fisik_pagi) ? $opname->stock_fisik_pagi : 0);
           $item['selisih_malam'] = $query->sisa_stock + $query->produksi1 - $query->total_penjualan - $item['rusak'] - (isset($opname->stock_fisik_malam) ? $opname->stock_fisik_malam : 0);
           $item['terjual'] = $query->total_penjualan;
        }else{
           $item['stock_masuk'] = 0;
           $item['stock_awal'] = 0;
           $item['produksi'] = 0;
           $item['selisih_pagi'] = 0;
           $item['selisih_malam'] = 0;
           $item['terjual'] = 0;
           $item['rusak'] = 0;
        }
        
        if(isset($opname->id)){
          $item['stock_toko'] = $opname->stock_toko;
          $item['stock_fisik_pagi'] = isset($opname->stock_fisik_pagi) ? $opname->stock_fisik_pagi : "";
          $item['stock_fisik_malam'] = isset($opname->stock_fisik_malam) ? $opname->stock_fisik_malam : "";
          $item['stock_akhir'] = isset($query->id) ? $query->sisa_stock + $query->produksi1 - $query->total_penjualan - $item['rusak'] : 0;
        }else{
          $item['stock_toko'] = '';
          $item['stock_fisik_pagi'] = "";
          $item['stock_fisik_malam'] = "";
          $item['stock_akhir'] = isset($query->id) ? $query->sisa_stock + $query->produksi1 - $query->total_penjualan - $item['rusak'] : 0;
        }

      });

      

      if($data[2] == '1'){
         return $item->sortBy($sort_by[$data[1]])->values()->all();
      }elseif($data[2] == '2'){
         return $item->sortByDesc($sort_by[$data[1]])->values()->all();
      }

    } 
    
}
