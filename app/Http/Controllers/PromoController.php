<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\KompresFoto;
use App\Promo;
use Auth;

class PromoController extends Controller
{
    
	public function __construct()
	{
		 $this->middleware('auth');
	}

    public function index()
    {
    	$promo = Promo::where('status','1')->get();

    	$menu_active = "transaksi|promo|0";
    	return view('transaksi.promo', compact('menu_active','promo'));
    }

    public function listPromoSelesai()
    {
    	$promo = Promo::where('status','0')->get();
    	$menu_active = "transaksi|promo|0";
    	

    	return view('transaksi.list_promo', compact('menu_active','promo'));

    }

    public function store(Request $request)
    {
    	$req = $request->all();
    	$validator = \Validator::make($req,['judul' => 'required', 
    									    'berlaku_sampai' => 'required',
    									    'gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG' ]);
    	if($validator->fails()){
          return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
        }

        $pisah_berlaku_sampai = explode("/", $req['berlaku_sampai']);
        $req['berlaku_sampai'] = $pisah_berlaku_sampai['2']."-".$pisah_berlaku_sampai['1']."-".$pisah_berlaku_sampai['0'];

        $req['gambar'] = KompresFoto::UbahUkuran($req['gambar'],'promo');
        $req['status'] = "1";

        $req['dibuat_oleh'] = Auth::User()->name;
        $insert = Promo::create($req);

        return redirect()->back()->with('success','Berhasil Buat Promo');
    }


      public function update(Request $request, $id)
    {
        $req = $request->all();

        if(isset($req['gambar'])){
            $data_validator =['judul' => 'required', 
    						  'berlaku_sampai' => 'required',
						      'gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG' ];
        }else{
            $data_validator = ['judul' => 'required', 
    						  'berlaku_sampai' => 'required'];
        }

        $validator = \Validator::make($req,$data_validator);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        $find = Promo::findOrFail($req['id']);

        if(isset($req['gambar'])){
            if(!empty($find->gambar)){
                $hapusFoto = KompresFoto::HapusFoto($find->gambar); 
            }
            
            $uploadFoto = KompresFoto::UbahUkuran($req['gambar'],'promo');
            $req['gambar'] = $uploadFoto;
        }else{
            $req = $request->except(['gambar']);
        }

        $pisah_berlaku_sampai = explode("/", $req['berlaku_sampai']);
        $req['berlaku_sampai'] = $pisah_berlaku_sampai['2']."-".$pisah_berlaku_sampai['1']."-".$pisah_berlaku_sampai['0'];
        if($req['status'] == '0'){
        	$req['dibuat_oleh'] = Auth::User()->name;
        }

        $find->update($req);
        return redirect()->back()->with('success','Berhasil Update Promo');
    }

    public function destroy($id)
    {
    	$find = Promo::findOrFail($id);
        $hapusFoto = KompresFoto::HapusFoto($find->gambar);
        $find->delete();

        return redirect()->back()->with('success','Berhasil Hapus Promo');
    }
}
