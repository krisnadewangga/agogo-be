<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\User;
use App\Item;
use App\ItemTransaksi;
use Carbon\Carbon;
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
    								  ])->orderBy('tgl_bayar','DESC')->get();
        
        // return $transaksi;
        $kop = "Laporan Pendapatan Sampai Hari Ini / ".Carbon::now()->format('d M Y');

        $input = ['mt' => "", 'st' => "" ];
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

    public function FilterLaporan(Request $request)
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
            $transaksi = Transaksi::whereDate('tgl_bayar','=',$mt)
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
            
            $kop = "Laporan Pendapatan Di Tanggal $kop_mt";
        }else if( empty($req['mt']) && !empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','<=',$st)
                                    
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
            $kop = "Laporan Pendapatan Sampai Tanggal $kop_st";
        }else if( !empty($req['mt']) && !empty($req['st']) ){
             $arr_bettwen = ["$mt","$st"];
             $transaksi = Transaksi::whereDate('tgl_bayar','>=',$mt)
                                    ->whereDate('tgl_bayar','<=',$st)
                                    
                                    ->where('status','!=', '3')
                                    ->orderBy('tgl_bayar','DESC')->get();
             $kop = "Laporan Pendapatan Mulai Tanggal $kop_mt S/D $kop_st";
        }else{
            return redirect()->route('lap_pendapatan');
        }
        // return $arr_bettwen;

        $total_pendapatan = $transaksi->sum('total_bayar');
        // $total_bersih_item = $transaksi->sum('sub_total_bersih_item');
        // $total_pengiriman = $transaksi->sum('total_biaya_pengiriman');
        $menu_active = "laporan|pendapatan|0";

        return view("laporan.lap_pendapatan",compact('transaksi','total_pendapatan','menu_active','kop','input'));
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

    public function DetailUser($id)
    {
        $user = User::findOrFail($id);
        $transaksi = $user->Transaksi()->orderBy('id','DESC')->get();
        
        if($user->status_aktif=='0'){
           $logBan = $user->LogBan()->where('status_ban','0')->orderBy('id','DESC')->first();
        }else{
            $logBan = "";
        }
      
        $menu_active = "laporan|user|1";

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
    //END User

    //Penjualan -> Grafik
    public function ShowPenjualan()
    {
        $tahun = Transaksi::selectRaw("MIN(YEAR(tgl_bayar)) as min_tahun,
                                            MAX(YEAR(tgl_bayar)) as max_tahun ")->first();

        $tahunNow = date('Y');
        $tahun['max_tahun'] = $tahunNow;
        if(!isset($tahun->min_tahun) ){
            $tahun = ['min_tahun' => $tahunNow, 'max_tahun' => $tahunNow];
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

    public function setDataPenjualan(Request $request)
    {
       $req = $request->all();
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

       return response($response);
    }
    
}
