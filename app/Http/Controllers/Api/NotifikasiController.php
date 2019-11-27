<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Notifikasi;

class NotifikasiController extends Controller
{
     public function tampilNotifikasi(Request $request)
    {
    	$req = $request->all();
        $messsages = ['user_id.required' => 'user_id Tidak Bisa Kosong', 'page.required' => 'page Tidak Bisa Kosong', 'dataPerpage.required' => 'dataPerpage Tidak Bisa Kosong'];
        $rules = ['user_id' => 'required', 'page' => 'required', 'dataPerpage' => 'required'];

        $validator = Validator::make($request->all(), $rules,$messsages);
       
       
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
            $pageSaatIni = 0;
            $tampilPS = 0;
        }else{
        	 $id_user = $req['user_id'];
            $page = $req['page'];
            $dataPerpage = $req['dataPerpage'];
            $offset = ($page - 1) * $dataPerpage;

          	$notifikasi = Notifikasi::join('users as a','a.id','=','notifikasi.pengirim_id')
                                ->select('notifikasi.*','a.name as nama_pengirim')
                                ->where( function ($query) use ($id_user) {
                                    $query->where('notifikasi.penerima_id',$id_user)
                                          ->orWhere('notifikasi.penerima_id','0')
                                          ->where('notifikasi.pengirim_id','!=',$id_user);
                                })->orderBy('id','DESC')->limit($dataPerpage)->offset($offset)->get();


            $jumdat = Notifikasi::where( function ($query) use ($id_user) {
                                    $query->where('notifikasi.penerima_id',$id_user)
                                          ->orWhere('notifikasi.penerima_id','0')
                                          ->where('notifikasi.pengirim_id','!=',$id_user);
                            })->count();

            $jumHal = ceil($jumdat / $dataPerpage);
            $pageSaatIni = (int) $page;
            $pageSelanjutnya = $page+1;
            if($pageSaatIni == $jumHal){
                 $tampilPS = 0;
            }else{
                 $tampilPS = $pageSelanjutnya;
            }

          	$success = 1;
            $msg = $notifikasi;
            $kr = 200;
        }

    	  return response()->json(['success' => $success, 'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
    }


    public function readNotifikasi(Request $request){
    	$req = $request->all();
    	$rules = ['notif_id' => 'required'];
    	$messsages = ['notif_id.required' => 'notif_id Tidak Bisa Kosong' ];

    	$validator = Validator::make($req,$rules,$messsages);
    	if($validator->fails()){
    		$success = 0;
    		$msg = $validator->messages()->all();
        $kr = 400;
    	}else{
    		$find = Notifikasi::findOrFail($req['notif_id']);
    		$find->update(['dibaca' => '1']);
    		$success = 1;
    		$msg = "Berhasil Update Notifikiasi";
        $kr = 200;
    	}

    	return response()->json(['success' => $success, 'msg' => $msg], $kr);
    }
}
