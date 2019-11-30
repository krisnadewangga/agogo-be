<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaksi;
use App\User;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function LapPendapatan()
    {
    	$transaksi = Transaksi::where([ 
    									['metode_pembayaran','=','1'],
    									['status','>=','1']
    								  ])
    						->orWhere([ 
    									['metode_pembayaran','>','1'],
    									['status','=','5']
    								  ])->orderBy('tgl_bayar','DESC')->get();

        $kop = "Laporan Pendapatan Sampai Hari Ini / ".Carbon::now()->format('d M Y');

        $input = ['mt' => "", 'st' => "" ];
    	$total_pendapatan = $transaksi->sum('total_bayar');
        $menu_active = "laporan|pendapatan";
    	return view("laporan.lap_pendapatan",compact('transaksi','total_pendapatan','menu_active','kop','input'));
    }

    public function FilterLaporan(Request $request)
    {
        $req = $request->all();
        $input = ['mt' => $req['mt'], 'st' => $req['st'] ];

        if(!empty($req['mt'])){
            $mt = Carbon::parse($req['mt'])->toDateString();
            $kop_mt = Carbon::parse($req['mt'])->format('d M Y');
        }

        if(!empty($req['st'])){
            $st = Carbon::parse($req['st'])->toDateString();
            $kop_st = Carbon::parse($req['st'])->format('d M Y');
        }
        
        if( !empty($req['mt']) && empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','=',$mt)
                            ->orderBy('tgl_bayar','DESC')->get();
            $kop = "Laporan Pendapatan Di Tanggal $kop_mt";
        }else if( empty($req['mt']) && !empty($req['st']) ){
            $transaksi = Transaksi::whereDate('tgl_bayar','<=',$st)
                            ->orderBy('tgl_bayar','DESC')->get();
            $kop = "Laporan Pendapatan Sampai Tanggal $kop_st";
        }else if( !empty($req['mt']) && !empty($req['st']) ){
             $transaksi = Transaksi::whereBetween('tgl_bayar',[$mt,$st])
                            ->orderBy('tgl_bayar','DESC')->get();
             $kop = "Laporan Pendapatan Mulai Tanggal $kop_mt S/D $kop_st";
        }else{
            return redirect()->route('lap_pendapatan');
        }
        
        $total_pendapatan = $transaksi->sum('total_bayar');
        $menu_active = "laporan|pendapatan";

        return view("laporan.lap_pendapatan",compact('transaksi','total_pendapatan','menu_active','kop','input'));
    }

    public function LapUser()
    {   
        $user = User::where('level_id','3')->where('status_aktif','1')->get();
        $total_user = $user->count();
        
        $total_member = User::join('detail_konsumen as a','a.user_id','=','users.id')
                             ->where('a.status_member','=','1')
                             ->where('users.status_aktif','=','1')->count();
        $total_not_member = User::join('detail_konsumen as a','a.user_id','=','users.id')
                             ->where('a.status_member','=','0')
                             ->where('users.status_aktif','=','1')->count();

        $menu_active = "laporan|user";
        return view("laporan.lap_user",compact('menu_active','user', 'total_member','total_not_member','total_user'));
    }
}
