<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\DetailKonsumen;
use App\Helpers\SendNotif;
use App\Helpers\KompresFoto;
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
  		            'no_hp' => 'required|unique:users',
  		            'email' => 'required|unique:users',
  		            'password' => 'required',
  		         ];
	  		

	   	$validator = Validator::make($request->all(), $rules,$messsages);
	    if($validator->fails()){
	        $success = 0;
	        $msg = $validator->messages()->all();
	    }else{
	       	$dataUser = $request->except('no_hp');
	       	$dataUser['level_id'] = 3;
	       	$dataUser['password'] = bcrypt($req['password']);
	       	$dataUser['status_aktif'] = '0';
          $dataUser['no_hp'] =  $request->no_hp;

	       	$acak_nomor = Acak::Kaseputar();
	        $no_aktifasi = date('ymdHis').$acak_nomor;
	        $dataDetail = ["no_aktifasi" =>  $no_aktifasi  ];
	      	
	      	$register = User::create($dataUser);
	      	$find = User::findOrFail($register->id);
	       	$find->DetailKonsumen()->create($dataDetail);

	        if($register){
	          
	          $email = $register->email;
	  		    $data = ['name' => $register->name,
	                   'email_body' => "Sekarang kamu telah berhasil melakukan registrasi akun  <span style='color:#FBB901;'>
	                        AgoogoBakery.com </span>  <br/>
	                        Nikmati belanja online dari produk AgogoBakery yang aman dan dapat dipercaya .. <br/>
	                        <p></p>
	                        Silahkan klik link dibawah ini untuk aktifasi akun anda <br/>http://".$_SERVER['HTTP_HOST']."/aktifasi/$no_aktifasi
	                        <p></p>
	                        Pingin Ngemil ? Atau.. Buat Acara ? Atau... Buat Orang Terspesial ? <br/> Pesan Aja Di <span style='color:#FBB901;'>AgogoBakery.com</span>"
	                   ];
	          
	          $subject = "Registrasi AgogoBakery.com";
	          SendNotif::kirimEmail($email,$data,$subject);
	        
	          $success = 1;
	          // $token = JWTAuth::fromUser($register);
	          $msg = "Silahkan Aktifasi Akun Anda , Kami Telah Mengirimkan Link Aktifasi Ke Email Anda";
	          // $response = [
	          //     "success" => $success,
	          //     "user_id" => $register['id'],
	          //     // "key" => "Bearer {$token}",
	          //     "msg" => $msg,
	          // ];
	          // if(isset($req['token'])){
	          //    User::where('id',$register['id'])->update(['token' => $req['token'] ]);
	          // }

	        }else{
	          $success = 0;
	          $msg = 'Gagal Registrasi';
	         
	        } 
	    }

	  	return response()->json(['success' => $success, 'msg' => $msg],200);
	}

	public function login(Request $request)
	{
		$req = $request->all();
        $messsages = array( 
                            'no_hp.required'=>'no_hp Harus Diisi',
                            'password.required'=>'password Harus Diisi',
                           );   

        $rules = array( 'no_hp' => 'required',
                        'password' => 'required',
                      );

        $validator = Validator::make($request->all(), $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
   
        }else{
            if(Auth::attempt(['no_hp' => $req['no_hp'], 'password' => $req['password'] ])){
                $user = Auth::user()->where('no_hp',$req['no_hp'])->first();
                $find = User::findOrFail($user->id);

                if($user->level_id == 3){
                  if(!empty($user->email_verified_at) && $user->status_aktif == '1' ){
                    if($find->DetailKonsumen->alamat == ""){
                      $user['lengkapi_alamat'] = "0";
                    }else{
                      $user['lengkapi_alamat'] = "1";
                    }
                    
                    $success = 1;
                    $msg = $user;
                  }else if(!empty($user->email_verified_at) && $user->status_aktif == '0'){
                    $success = 0;
                    $msg = "Maaf! Untuk Saat Ini Akun Anda Diblokir";
                  }else if(empty($user->email_verified_at) && $user->status_aktif == '0' ) {
                    $success = 0;
                    $msg = "Silahkan Aktifasi Akun Anda";
                  }
                  
                  // $token = JWTAuth::fromUser($user);
                  // $response = [
                  //     "success" => $success,
                  //     "msg" => $msg,
                  //     // "key" => "Bearer {$token}",
                  // ];
                  // if(isset($req['token'])){
                  //   $user->update(['token_gcm' => $req['token']]);
                  // }
                  
                }else{
                  $success = 0;
                  $msg = "User Tidak Ditemukan";
                }
                
            }else{
                $success = 0;
                $msg = "Gagal Login, Periksan Kembali No Hp Dan Password Anda";
            } 
        }
        return response()->json(['success' => $success, 'msg' => $msg],200);
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
  
  public function UpdateProfil(Request $request)
  {
      $req = $request->all();
      $jumReq = count($req);
      $array_field = ['name','foto','password','jenis_kelamin','tgl_lahir'];
      $array_field_rule = ['name' => 'required', 
                           'jenis_kelamin' => 'required', 
                           'tgl_lahir' => 'required|date',
                           'foto' => 'required|mimes:jpg,jpeg,png',
                           'user_id' => 'required',
                           'password' => 'required|min:6'
                          ];

      $array_field_messages = ['name' => ['name.required' => 'nama Tidak Bisa Kosong'],
                               'user_id' => ['user_id.required' => 'user_id Tidak Bisa Kosong'],
                               'jenis_kelamin' => ['jenis_kelamin.required'=> 'jenis_kelamin Tidak Bisa Kosong'],
                               'tgl_lahir' => ['tgl_lahir.required'=> 'tgl_lahir Tidak Bisa Kosong',
                                               'tgl_lahir.date' => 'tgl_lahir Format Y-m-d '
                                              ],
                               'foto' => ['foto.required' => 'foto Harus Diisi',
                                          'foto.mimes' => 'foto Harus Extensi Harus jpg,jpeg,png'],
                               'password' => ['password.required' => 'password Tidak Bisa Kosong',
                                              'password.min' => 'password Minimal 6 Digit']
                              ];

      if(isset($req['user_id'])){
        $rules = array();
        $messsages = [];
        $find = User::findOrFail($req['user_id']);
        $temp = array();
        if($jumReq == 2){
            foreach ($req as $key => $value) {
              if(in_array($key, $array_field)){
                $rules[$key] =  $array_field_rule[$key];
                
                foreach ($array_field_messages[$key] as $key1 => $value1) {
                  $messsages[$key1] = $value1;
                }

                if($key != 'user_id'){
                  $temp[$key] = $value;
                  $validator = Validator::make($request->all(), $rules,$messsages);
                  if($validator->fails()){
                      $success = 0;
                      $msg = $validator->messages()->all();
                  }else{
                      if($key == 'name'){
                        $update = $find->update($temp);
                      }elseif($key == 'foto'){
                        if(!empty($find->foto)){
                          KompresFoto::HapusFoto($find->foto);
                        }
        
                        $upload = KompresFoto::UbahUkuran($req['foto'],'user');
                        $update = $find->update(['foto' => $upload ]);

                      }else if($key == 'password'){
                         $temp['password'] = bcrypt($req['password']);
                         $update = $find->update($temp);

                      }else{
                         $update = $find->DetailKonsumen()->update($temp);
                      }
                      
                      if($update){
                        $success = 1;
                        $msg = "Berhasil Update";
                      }else{
                        $success = 0;
                        $msg = "Gagal Update";
                      }
                  } 
                }
               
              }else{
                $success = 0;
                $msg = "Yang Bisa Diedit Hanya name,foto,password,tgl_lahir,jenis_kelamin";
              }
            }
        }else if($jumReq == 1){
          $success = 0;
          $msg = "Request Yang Diterima Minimal 2";
        }else{
           $success = 0;
           $msg = "Request Telah Lebih Dari 2";
        }
        
      }else{
        $success = 0;
        $msg = "Tidak ada user_id yang akan di update, masukan parameter user_id dan field yang akan diedit (name,foto,password,tgl_lahir,jenis_kelamin)";
      }
      return response()->json(['success'=> $success,'msg'=>$msg]);
        // return $messsages;
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
  
  public function resetPassword(Request $request)
  {
    $req = $request->all();
    $rules = [ 'email' => 'required|exists:users,email' ];
    $messsages = ['email.required' => 'email Masukan Email Anda',
                 'email.exists' => 'email Tidak Ditemukan'
                ];
    $validator = Validator::make($request->all(), $rules,$messsages);
    if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();

    }else{
        $find = User::where([ ['email','=',$req['email'] ], 
                              ['level_id', '=' ,'3'] 
                           ])->first();
        if(isset($find->id) ){
          
          $passwordNew = Acak::Kaseputar(6);
          $find->Update(['password' => bcrypt($passwordNew) ]);

          $data = ['name' => $find->name,
                   'email_body' => "Sekarang kamu telah berhasil melakukan reset password akun  <span style='color:#FBB901;'>
                      AgoogoBakery.com </span>  <br/>
                         Silahkan Login dengan password standar : $passwordNew<br/>
                          <p></p>
                          Dan pastikan anda langsung mengganti password standar guna keamanan dari Akun Anda .
                          <p></p>
                         "
                     ];
            
          $subject = "Reset Password Akun AgogoBakery.com";
          SendNotif::kirimEmail($find->email,$data,$subject);

          $success = 1;
          $msg = "Berhasil Reset Password";

        }else{
          $success = 0;
          $msg = "email Tidak Ditemukan";
        }
        
    }
    return response()->json(['success' => $success, 'msg' => $msg],200);
  }
  
}
