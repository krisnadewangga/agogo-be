<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\KompresFoto;
use App\User;
use Auth;

class ProfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function inGantiPassword()
    {
    	$menu_active = "||0";
    	return view('user.ganti_password',compact('menu_active'));
    }

    public function gantiPassword(Request $request)
	{
		$req = $request->all();    
		$validation = \Validator::make($req,['password_lama' => 'required',
                                             'password' => 'required|string|min:6|confirmed',
                                             ])->validate();

		
		if (!(Hash::check($req['password_lama'], Auth::user()->password))) {
			return redirect()->back()->with("error","Password Lama Salah");
		}else{
			$user = Auth::user();
			$user->password = bcrypt($req['password']);
			$user->save();

			return  redirect()->back()->with("success","Berhasil Update Password");
		}
	}

	public function gantiFotoProfil(Request $request)
	{
		$req = $request->all();
        $validator = \Validator::make($req,['gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG']);
       
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','gambar_fp');
        }

        $find = User::findOrFail(Auth::User()->id);
        if(!empty($find->foto)){
        	KompresFoto::HapusFoto($find->foto);
        }
        
        $upload = KompresFoto::Upload($req['gambar'],'user');
        $update = $find->update(['foto' => $upload ]);

        return redirect()->back()->with('success_ganti_fp','Ganti Foto Profil');
	}
}
