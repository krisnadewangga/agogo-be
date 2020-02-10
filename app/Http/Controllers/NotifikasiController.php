<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifikasi;
use App\User;
use App\Transaksi;
use App\Pengiriman;
use Auth;

class NotifikasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function loadNotif(Request $request)
    {
    	$req = $request->all();
    	$user_id = $req['user_id'];

    	$jumNot = Notifikasi::where(['penerima_id' => $user_id, 'dibaca' => '0'])->orderBy('created_at','DESC')->count();
    	$TampilNotif = Notifikasi::where(['penerima_id' => $user_id])->orderBy('created_at','DESC')->limit('5')->get();
        
    	return response()->json(['status' => '1', 'listNotif' => $TampilNotif, 'jumNot' => $jumNot]);
    }

    public function bacaNotif(Request $request)
    {
    	$req = $request->all();
    	$user_id = $req['user_id'];

        $update = Notifikasi::where(['penerima_id' => $user_id, 'dibaca' => '0'])->update(['dibaca' => '1']);
        if($update)
        {
            $status = 1;
        }else{
            $status = 0;
        }
        return response()->json(['status' => $status]);
    }

    public function GetJumPesanan()
    {
       $transaksi = Transaksi::whereNotIn('status',['5','3'])->count();
       return $transaksi;
    }
    
    public function GetJumPengiriman()
    {
       $pengiriman = Pengiriman::where('status','0')->count();
       return $pengiriman;
    }

    public function GetJumAP()
    {
       $transaksi = Transaksi::where('status','4')->count();
       return $transaksi;
    }

    public function GetJumKP()
    {
      $transaksi = Transaksi::where('status','6')->count();
      return $transaksi;
    }
    
    public function index()
    {
        $notifikasi = Notifikasi::join('users as a','a.id','=','notifikasi.pengirim_id')
                                  ->select('notifikasi.*','a.name')
                                  ->where('notifikasi.penerima_id', Auth::User()->id )
                                  ->orderBy('id','desc')->get();
        $jumNotBelumDibaca = Notifikasi::where([ 
                                                 ['dibaca','=','0'],
                                                 ['penerima_id','=', Auth::User()->id]

                                               ])->count();
        if($jumNotBelumDibaca > 0){
            Notifikasi::where([ 
                                 ['dibaca','=','0'],
                                 ['penerima_id','=', Auth::User()->id]
                               ])->update(['dibaca' => '1']);
        }
        
        $menu_active = "||0";
        return view('list_notifikasi',compact('menu_active','notifikasi'));
    }
}
