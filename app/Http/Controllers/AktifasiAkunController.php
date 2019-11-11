<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\DetailKonsumen;

class AktifasiAkunController extends Controller
{
   public function Aktifasi($id)
   {
   	 $find = User::join('detail_konsumen','users.id','=','detail_konsumen.user_id')
   	 			   ->where(['no_aktifasi' => $id, 'status_aktif' => '0' ])
   	 			   ->select('users.*','detail_konsumen.no_aktifasi')
   	 			   ->first();
   	
   	 if(isset($find->id)){
   	 	$update = User::where('id',$find->id)->update(['status_aktif' => '1','email_verified_at' => date('Y-m-d H:i:s')]);
   	 	
   	 	if($update){
   	 		$data = ['name' => $find->name, 'success' => '1'];
   	 	}else{
   	 		$data = ['name' => $find->name, 'success' => '2'];
   	 	}
   	 	
   	 }else{
   	 	$data = ['name' => '', 'success' => '0'];
   	 }
   	 return view('aktifasi',compact('data'));
   }
}
