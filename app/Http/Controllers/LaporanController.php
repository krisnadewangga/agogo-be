<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\User;
use App\Item;
use App\ItemTransaksi;
use App\Preorders;
use App\Kas;
use App\Produksi;
use Carbon\Carbon;
use PDF;
use Auth;

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
                      ['status','!=','3']

    								  ])
    						->orWhere([ 
    									['metode_pembayaran','>','1'],
    									['status','=','5']
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
                                    ->orderBy('tgl_bayar','DESC')->get();
            
            $kop = "Laporan Pendapatan Di Tanggal $kop_mt";
            $kop_export = "Tanggal : ".$req['mt'];
            $file_export = str_replace("/", "-", $req['mt']);

        }else if( empty($req['mt']) && !empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','<=',$st)
                                    
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
            $kop = "Laporan Pendapatan Sampai Tanggal $kop_st";
            $kop_export = "Sampai Tanggal : ".$req['st'];
            $file_export = str_replace("/", "-", $req['st']);
        }else if( !empty($req['mt']) && !empty($req['st']) ){
             $arr_bettwen = ["$mt","$st"];
             $transaksi = Transaksi::whereDate('tgl_bayar','>=',$mt)
                                    ->whereDate('tgl_bayar','<=',$st)
                                    
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
    }

    public function NotMember()
    {
        $user = User::where('level_id','6')
                     ->selectRaw("users.*, 
                                 (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status != '3') as total_belanja,
                                 (SELECT count(transaksi.id) from transaksi where transaksi.user_id=users.id and transaksi.status = '3') as batal_belanja
                               ")
                     ->where('email_verified_at','!=','')
                     ->whereIn('id',function($q){
                        $q->from('detail_konsumen')
                          ->select('user_id')
                          ->where('status_member','0');

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
      
        $menu_active = "user|not_member|0";
        return view("user.not_member",compact('menu_active','user', 'total_user','total_user_diblokir','total_user_aktif'));
    }

    public function DetailUser(Request $request)
    {
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
    }

    public function BlokirUser($id){
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

    }

    public function HapusUser($id,$stat = '1')
    {
       $find = User::findOrFail($id);
       $find->delete();
       if($stat== "1"){
         $hapus = "Konsumen (Member)";
       }else{
         $hapus = "Konsumen (Not Member)";
       }
       return redirect()->back()->with('success','Berhasil Hapus '.$hapus);
    }
    //END User

    //Penjualan -> Grafik
    public function ShowPenjualan()
    {
        $tahun = Transaksi::selectRaw("MIN(YEAR(tgl_bayar)) as min_tahun,
                                            MAX(YEAR(tgl_bayar)) as max_tahun ")->first();

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
      // return "konek";

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

      $dates = [ Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d')];

      $result = $this->SetDataPemesanan($dates);
     
      $input = ['mulai_tanggal' => Carbon::now()->format('d/m/Y'), 'sampai_tanggal' => Carbon::now()->format('d/m/Y') ];
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
       
       $dates = [$mt, $st];
       $result = $this->SetDataPemesanan($dates);
       
       $input = ['mulai_tanggal' => $req['mulai_tanggal'], 'sampai_tanggal' => $req['sampai_tanggal'] ];
       $menu_active = "laporan|pemesanan|0";

       return view('laporan.lap_pemesanan',compact('menu_active','input','result') );
    }

    public function ExportPemesanan(Request $request)
    {
       $req = $request->all();
        
       
       
       $dates = [$req['mulai_tanggal'], $req['sampai_tanggal']];
       $data = $this->SetDataPemesanan($dates);


       $explode = explode('-',$req['mulai_tanggal']);
       $explode1 = explode('-',$req['sampai_tanggal']);

       $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];
       $end_tanggal = $explode1[2]."/".$explode1[1]."/".$explode1[0];

       $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.pemesanan', compact('data', 'start_tanggal','end_tanggal'));
         return $pdf->stream('laporan-pemesanan-'.$start_tanggal.'-'.$end_tanggal.'.pdf');
       
       // return View('export.pemesanan', compact('data', 'start_tanggal','end_tanggal'));
     
    }

    public function SetDataPemesanan($dates)
    {

      if($dates[0] == $dates[1]){
            $select = Preorders::join('transaksi as a','a.id','=','preorders.transaksi_id')
                        ->join('users as b','b.id','=','a.user_id')
                        ->select('preorders.*','a.status','b.name as pencatat','a.no_transaksi')
                        ->whereDate('tgl_pesan',$dates[0])
                        ;
                 
      }else{
         $select = Preorders::join('transaksi as a','a.id','=','preorders.transaksi_id')
                        ->join('users as b','b.id','=','a.user_id')
                        ->select('preorders.*','a.status','b.name as pencatat','a.no_transaksi')
                        ->whereBetween('tgl_selesai',$dates);
                        ;
        
      }
    
      $data = $select->where('status','1')->get();
      $data_cancel = $select->where('status','3')->get();

      $grand_total_th = number_format($data->sum('total'),'0','','.');
      $grand_total_dp = number_format($data->sum('uang_muka'),'0','','.');
      $grand_total_sisa = number_format($data->sum('sisa_bayar'),'0','','.');

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

     
     $result = ['data' => $data, 'tfoot' => $tfoot];
     return $result;
    }
    // END Lap Pemesanan

    // Lap Kas
    public function LapKas()
    {
      
      $dates = [Carbon::now()->format('Y-m-d')];
      $data = $this->SetDataKas($dates);


      $input = ['tanggal' => Carbon::now()->format('d/m/Y') ];
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

      $dates = [$explode[2]."-".$explode[1]."-".$explode[0]];
      $data = $this->SetDataKas($dates);

      $input = ['tanggal' => $req['tanggal']];
      $menu_active = "laporan|kas|0";
      return view('laporan.lap_kas',compact('menu_active','input','data'));

    }

    public function ExportKas(Request $request)
    {
 
      $dates = [$request->tanggal];
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

    public function SetDataKas($dates)
    { 
      $kas = Kas::whereDate('created_at',$dates[0])->get();
      return $kas ; 
    }
    //END LAP KAS

    //LAP PERGERAKAN STOCK

    public function LapPergerakanStock()
    {
      $dates = [Carbon::now()->format('Y-m-d')];
      $data = $this->SetDataPergerakanStock($dates);

      $input = ['tanggal' => Carbon::now()->format('d/m/Y') ];
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
      $dates = [$explode[2]."-".$explode[1]."-".$explode[0]];
      $data = $this->SetDataPergerakanStock($dates);

      $input = ['tanggal' => $req['tanggal']];
      $menu_active = "laporan|pergerakan_stock|0";
      return view('laporan.lap_pergerakan_stock',compact('menu_active','input','data'));
    }

    public function ExportProduksi(Request $request)
    {
      $req = $request->all();
      $dates = [$req['tanggal']];
      $data = $this->SetDataPergerakanStock($dates);

      $explode = explode("-", $request->tanggal);
      $start_tanggal = $explode[2]."/".$explode[1]."/".$explode[0];

      $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('export.produksi', compact('data', 'start_tanggal'));
      return $pdf->stream('laporan-pergerakan-stock-'.$start_tanggal.'.pdf');
      // return View('export.produksi', compact('data', 'start_tanggal'));
    

    } 

    public function SetDataPergerakanStock($dates)
    {
       $data = Produksi::whereIn('id',function($q) use ($dates){
                                     $q->from('produksi')
                                     ->selectRaw('max(produksi.id)')
                                     ->whereDate('produksi.created_at',$dates[0])
                                     ->groupBy('produksi.item_id');

                                     return $q;
                                  })->get();

        return $data;
    }    
}
