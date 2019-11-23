<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifikasi;
use App\User;
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
}
