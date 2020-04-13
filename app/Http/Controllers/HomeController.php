<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Transaksi;
use App\Pengiriman;
use Carbon\Carbon;

class HomeController extends Controller
{
 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function getBulan(Request $request)
    {
        $req = $request->all();
        $tahunNow = date("Y");
        $listBulan = ['1' => 'Jan',
                      '2' => 'Feb',
                      '3' => 'Mar',
                      '4' => 'Apr',
                      '5' => 'May',
                      '6' => 'Jun',
                      '7' => 'Jul',
                      '8' => 'Agu',
                      '9' => 'Sep',
                      '10' => 'Oct',
                      '11' => 'Nov',
                      '12' => 'Dec'];
        
        $bulan = [];
        if($tahunNow == $req['tahun']){
            $carbonNow = Carbon::now()->format('m');
            for($i=1; $i<=$carbonNow; $i++){
                $bulan[$i] = $listBulan[$i];
            } 
        }else{
            $bulan = $listBulan;
        }

        return response($bulan);
    }

    public function setGrafik(Request $request)
    {
        $tahun = $request->tahun;
        $pendapatan = [0,0,0,0,0,0,0,0,0,0,0,0];
        $pengiriman = [0,0,0,0,0,0,0,0,0,0,0,0];
        $margin_bersih = [0,0,0,0,0,0,0,0,0,0,0,0];

        $transaksi = Transaksi::selectRaw("transaksi.*,
                                          (SELECT sum(jumlah * margin) from item_transaksi Where transaksi_id =transaksi.id) as sub_total_bersih_item 
                                         ")
                                ->whereYear('tgl_bayar','=',$tahun)
                                ->get()
                                ->groupBy(function($d){
                                    return Carbon::parse($d->tgl_bayar)->format('m');
                                });
        
        foreach ($transaksi as $key => $value) {
            $pendapatan[$key-1] = $value->sum('total_bayar');
            $pengiriman[$key-1] = $value->sum('total_biaya_pengiriman');
            $margin_bersih[$key-1] = $value->sum('sub_total_bersih_item');
        }
        
        $dataGrafik = [ 
                        ['name' => 'Pendapatan',
                        'data' =>  $pendapatan ],
                        ['name' => 'Margin Bersih',
                        'data' => $margin_bersih ],
                        ['name' => 'Pengiriman',
                         'data' => $pengiriman ]
                      ];

        return response($dataGrafik);
    }

    public function getTopTen(Request $request)
    {
        $req = $request->all();
        if(!empty($req['bulan'])){
            $sq = "and MONTH(b.tgl_bayar) = ".$req['bulan'];
        }else{
            $sq = "";
        }
      

        $top_ten = Item::selectRaw("item.*,
                                   (select sum(a.jumlah) from item_transaksi as a,transaksi as b
                                    where  a.transaksi_id=b.id and a.item_id=item.id and YEAR(b.tgl_bayar) = ".$req['tahun']." and b.status != '3' $sq  ) as total_belanja
                           ")
                        // ->where([
                        //           ['item.status_aktif','=','1']
                        //         ])
                        ->orderBy('total_belanja','DESC')
                        ->limit('10')
                        ->get();
        return response($top_ten);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tahun = Transaksi::selectRaw("MIN(YEAR(tgl_bayar)) as min_tahun,
                                            MAX(YEAR(tgl_bayar)) as max_tahun ")->first();
        

        $tahunNow = date('Y');
        $tahun['max_tahun'] = $tahunNow;
        $cek = $tahun->count();
        if($cek == 0){

            $tahun = (object) ['min_tahun' => $tahunNow, 'max_tahun' => $tahunNow];
        }

        $top_ten = Item::selectRaw("item.*,
                                   (select sum(a.jumlah) from item_transaksi as a,transaksi as b
                                    where  a.transaksi_id=b.id and a.item_id=item.id and YEAR(b.tgl_bayar) = $tahunNow and MONTH(b.tgl_bayar) = ".date('m')." and b.status != '3' ) as total_belanja
                           ")
                        // ->where([
                        //           ['item.status_aktif','=','1']
                        //         ])
                        ->orderBy('total_belanja','DESC')
                        ->limit('10')
                        ->get();

        $pesanan = Transaksi::where('status','1')->orWhere('status','2')->orWhere('status','6')->count();
        $pengiriman = Pengiriman::where('status','0')->count();
        $total_p = Transaksi::where('status','5')->count();
        $dashboard_k = ['pesanan' => $pesanan, 'pengiriman' => $pengiriman, 'total_p' => $total_p];
        

        $menu_active = "dashboard||0";
        
        return view('home',compact('menu_active', 'top_ten','tahun','tahunNow','dashboard_k'));
    }

}
