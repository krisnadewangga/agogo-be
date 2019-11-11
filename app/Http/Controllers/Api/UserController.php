<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\DetailKonsumen;
use App\Helpers\SendNotif;
use App\Helpers\Acak;
use Validator;
use Auth;


class UserController extends Controller
{
    public function register(Request $request){
     	 
		$req = $request->all();
		$messsages = ['name.required' => 'name Tidak Bisa Kosong',
                'no_hp.required'=>'no_hp Tidak Bisa Kosong',
                'no_hp.unique' => 'no_hp Sudah Digunakan',
                'email.required'=> 'email Tidak Bisa Kosong',
                'email.unique'=> 'email Sudah Digunakan',
                'password.required'=> 'password Tidak Bisa Kosong',
                
               ];

		$rules = ['name' => 'required',
		            'no_hp' => 'required|unique:detail_konsumen',
		            'email' => 'required|unique:users',
		            'password' => 'required',
		         ];
	  		

	   	$validator = Validator::make($request->all(), $rules,$messsages);
	    if($validator->fails()){
	        $success = 0;
	        $msg = $validator->messages()->all();
	        $response = $msg;
	    }else{
	       	$dataUser = $request->except('no_hp');
	       	$dataUser['level_id'] = 3;
	       	$dataUser['password'] = bcrypt($req['password']);
	       	$dataUser['status_aktif'] = '0';

	       	$acak_nomor = Acak::Kaseputar();
	        $no_aktifasi = date('ymdHis').$acak_nomor;
	        $dataDetail = ["no_hp" => $request->no_hp ,
	        			   "no_aktifasi" =>  $no_aktifasi  ];
	      	
	      	$register = User::create($dataUser);
	      	$find = User::findOrFail($register->id);
	       	$find->DetailKonsumen()->create($dataDetail);

	        if($register){
	          
	          $email = $register->email;
	  		  $data = ['name' => $register->name,
	                   'email_body' => "Sekarang kamu telah berhasil mengaktifkan akun  <span style='color:#FBB901;'>
	                        AgoogoBakery.com </span>  <br/>
	                        Nikmati belanja online dari produk AgogoBakery yang aman dan dapat dipercaya .. <br/>
	                        <p></p>
	                        Silahkan klik link dibawah ini untuk aktifasi akun anda <br/>http://".$_SERVER['HTTP_HOST']."/agogobakery.com/aktifasi/$no_aktifasi
	                        <p></p>
	                        Pingin Ngemil ? Atau.. Buat Acara ? Atau... Buat Orang Terspesial ? <br/> Pesan Aja Di <span style='color:#FBB901;'>AgogoBakery.com</span>"
	                   ];
	          
	          $subject = "Registrasi AgogoBakery.com";
	          SendNotif::kirimEmail($email,$data,$subject);
	        
	          $success = 1;
	          // $token = JWTAuth::fromUser($register);
	          $msg = User::findOrFail($register->id);
	          $response = [
	              "success" => $success,
	              "user_id" => $register['id'],
	              // "key" => "Bearer {$token}",
	              "msg" => $msg,
	          ];
	          // if(isset($req['token'])){
	          //    User::where('id',$register['id'])->update(['token' => $req['token'] ]);
	          // }

	        }else{
	          $success = 0;
	          $msg = 'Gagal Registrasi';
	          $response = [
	              "success" => $success,
	              "msg" => $msg,
	          ];
	        } 
	    }
	  	return response()->json($response,200);
	 }

	public function login(Request $request)
	{
		$req = $request->all();
        $messsages = array( 
                            'email.required'=>'email Harus Diisi',
                            'password.required'=>'password Harus Diisi',
                           );   

        $rules = array( 'email' => 'required',
                        'password' => 'required',
                      );

        $validator = Validator::make($request->all(), $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $response = $msg;
        }else{
            if(Auth::attempt(['email' => $req['email'], 'password' => $req['password'] ])){
                $user = Auth::user()->where('email',$req['email'])->first();
              
                $find = User::findOrFail($user->id);
                if($find->DetailKonsumen->alamat == ""){
                	$user['lengkapi_alamat'] = "0";
                }else{
                	$user['lengkapi_alamat'] = "1";
                }

                if($user->level_id == 3){
                  $success = 1;
                  $msg = $user;
                  // $token = JWTAuth::fromUser($user);

                  $response = [
                      "success" => $success,
                      "msg" => $msg,
                      // "key" => "Bearer {$token}",
                  ];
                  
                  // if(isset($req['token'])){
                  //   $user->update(['token_gcm' => $req['token']]);
                  // }
                  
                }else{
                  $success = 0;
                  $msg = "User Tidak Ditemukan";
                  $response = [
                      "success" => $success,
                      "msg" => $msg,
                  ];
                }
                
            }else{
                $success = 0;
                $msg = "Gagal Login";
                $response = [
                      "success" => $success,
                      "msg" => $msg,
                  ];
            } 
        }
        return response()->json($response);
	}

	public function LengkapiAlamat(Request $request)
	{
		$req = $request->all();
        
        $rules = ['lat' => 'required', 'long' => 'required', 'alamat' => 'required', 'user_id' => 'required'];
        $messsages = ['item_id.required' => 'item_id Tidak Bisa Kosong', 'lat.required' => 'lat Tidak Bisa Kosong','long.required' => 'long Tidak Bisa Kosong', 'alamat.required' => 'alamat Tidak Bisa Kosong', 'user_id.required' => 'user_id Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{
        	$find = User::findOrFail($req['user_id']);
        	$update = $find->DetailKonsumen()->update($req);
        	
        	$success = 1;
        	$msg = "Berhasil Melengkapi Alamat";
        	$kr = 200;
        }
        return response()->json(['success' => $success,'msg' => $msg], $kr);
	}

	public function ProfilUser(Request $request)
	{
		$req = $request->all();
        
        $rules = ['user_id' => 'required'];
        $messsages = ['user_id Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{
        	$find = User::findOrFail($req['user_id']);
        	$find->DetailKonsumen;
        	
        	$success = 1;
        	$msg = $find;
        	$kr = 200;
        }
        return response()->json(['success' => $success,'msg' => $msg], $kr);
	}

}
