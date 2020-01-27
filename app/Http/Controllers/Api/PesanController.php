<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\SendNotif;
use App\Pesan;
use App\User;
use Validator;

class PesanController extends Controller
{
   public function kirimPesan(Request $request)
   {
	    $req = $request->all();
	    $rules = ['user_id' => 'required','pesan' => 'required'];
	    $messsages = ['user_id.required' => 'user_id Tidak Bisa Kosong','pesan.required' => 'pesan Tidak Bisa Kosong'  ];
	     
	    $validator = Validator::make($req, $rules,$messsages);
	    if($validator->fails()){
	        $success = 0;
	        $msg = $validator->messages()->all();
	        $kr = 400;
	    }else{
	        $find = User::findOrFail($req['user_id']);
	        
	        $req['dibuat_oleh'] = $find->name;
	        $req['status'] = '0';
	        
	        $insert = Pesan::create($req);
	        
	        $arr = ['id' => $insert->id, 
	                'user_id' => $insert->user_id, 
	                'name' => $find->name,
	                'pesan' => substr($insert->pesan,0,31), 
	                'dibaca' => '0' , 
	                'status' => $insert->status,
	                'waktu' => $insert->created_at->format('d/m/y h:i A'),
	                'foto' => $find->foto,
	                'jumPesan' => $find->Pesan()->where(['dibaca' => '0','status' => '0'])->count(),
	                'pesan_nda_potong' => $insert->pesan,
	                'status_member' => $find->DetailKonsumen->status_member ];

	        SendNotif::SendNotPesan('2',$arr);

	        $success = 1;
	        $msg = "Berhasil Kirim Pesan";
	        $kr = 200;
	    } 
    	return response()->json(['success' => $success, 'msg' => 'Berhasil Kirim PEsan'],$kr);
   }

   public function listPesan(Request $request)
   {
   		$req = $request->all();
   		$rules = ['user_id' => 'required', 'page' => 'required', 'dataPerpage' => 'required'];
   		$messages = ['user_id.required' => 'user_id Tidak Bisa Kosong',
   					  'dataPerpage.required' => 'dataPerpage Tidak Bisa Kosong',
                      'page.required' => 'page Tidak Bisa Kosong'
   					 ];
   		$validator = Validator::make($req,$rules,$messages);
   		if($validator->fails()){
   			$success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
            $pageSaatIni = 0;
            $tampilPS = 0;
   		}else{
   			$page = $req['page'];
            $dataPerpage = $req['dataPerpage'];
            $offset = ($page - 1) * $dataPerpage;

			$pesan = Pesan::where('user_id', $req['user_id'])
					->orderBy('pesan.created_at','ASC')
					->limit($dataPerpage)
					->offset($offset)->get();

			$jumdat = Pesan::where('user_id',$req['user_id'])
                          ->count();

           $jumHal = ceil($jumdat / $dataPerpage);
           $pageSaatIni = (int) $page;
           $pageSelanjutnya = $page+1;
           if( ($pageSaatIni == $jumHal) || ($jumHal == 0) ){
              $tampilPS = 0;
           }else{
              $tampilPS = $pageSelanjutnya;
           }

           $success = 1;
           $msg = $pesan;
           $kr = 200;
   		}

   		return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
   }

   public function pesanById(Request $request)
   {
   	 $req = $request->all();
    $rules = ['pesan_id' => 'required'];
    $messsages = ['pesan_id.required' => 'pesan_id Tidak Bisa Kosong' ];
   
    $validator = Validator::make($req, $rules,$messsages);
    if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();
        $kr = 400;
    }else{

    	$pesan = Pesan::findOrFail($req['pesan_id']);

    
    	
    	$success = 1;
      	$msg = $pesan;
      	$kr = 200;

    }
    return response()->json(['success' => $success,'msg' => $msg], $kr);

   }

   public function bacaPesanTiapUser(Request $request)
   {
   	 $req = $request->all();
   	 $rules = ['user_id' => 'required'];
     $messsages = ['user_id.required' => 'user_id Tidak Bisa Kosong' ];
   
     $validator = Validator::make($req, $rules,$messsages);
     if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();
        $kr = 400;
     }else{

    	$pesan = Pesan::where([ 
    							['user_id', '=', $req['user_id']],
    							['status', '=', '1'],
    							['dibaca', '=', '0']
    						 ]);
    	$pesan->update(['dibaca' => '1']);
    
    	$success = 1;
      	$msg = 'Berhasil Baca Seluruh Pesan';
      	$kr = 200;

     }
     return response()->json(['success' => $success,'msg' => $msg], $kr);
   }


   public function bacaPesanTiapId(Request $request)
   {
   	  $req = $request->all();
   	  $rules = ['pesan_id' => 'required'];
      $messsages = ['pesan_id.required' => 'pesan_id Tidak Bisa Kosong' ];
   
      $validator = Validator::make($req, $rules,$messsages);
      if($validator->fails()){
         $success = 0;
         $msg = $validator->messages()->all();
         $kr = 400;
      }else{

    	 $pesan = Pesan::findOrFail($req['pesan_id']);
    	 $pesan->update(['dibaca' => '1']);
    
    	 $success = 1;
      	 $msg = 'Berhasil Baca Pesan';
      	 $kr = 200;

      }
      return response()->json(['success' => $success,'msg' => $msg], $kr);
   }

   public function hapusPesan(Request $request)
   {
   		$req = $request->all();
   		$req = $request->all();
	   	$rules = ['pesan_id' => 'required'];
	    $messsages = ['pesan_id.required' => 'pesan_id Tidak Bisa Kosong' ];
	   
	    $validator = Validator::make($req, $rules,$messsages);
	    if($validator->fails()){
	        $success = 0;
	        $msg = $validator->messages()->all();
	        $kr = 400;
	    }else{

	    	$pesan = Pesan::findOrFail($req['pesan_id']);
	    	$pesan->delete();
	    	
	    	$success = 1;
	      	$msg = 'Berhasil Hapus Pesan';
	      	$kr = 200;

	    }
	    return response()->json(['success' => $success,'msg' => $msg], $kr);
   }


}
