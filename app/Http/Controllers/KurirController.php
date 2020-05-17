<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\KompresFoto;
use App\Kurir;
use App\Ongkir;
use Auth;

class KurirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function($request,$next){
            if(Gate::allows('manage-kurirs')) return $next($request);
            abort(404,'Halaman Tidak Ditemukan');
        });
    }

    public function index()
    {
        $kurir = Kurir::all();
        $ongkir = Ongkir::first();
        $menu_active = "user|kurir|0";
        return view('pengiriman.kurir', compact('kurir','menu_active','ongkir'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = $request->all();
        $validator = \Validator::make($req,['nama' => 'required|unique:kurir',
                                            'no_hp' => 'required|unique:kurir',
                                            'jenis_kendaraan' => 'required',
                                            'merek' => 'required',
                                            'no_polisi' => 'required',
                                            'foto' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG']);
       

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','simpan');
        }

        $req['foto'] = KompresFoto::UbahUkuran($req['foto'],'kurir');
        $insert = Kurir::create($req);

        return redirect()->back()->with('success','Berhasil Input Kurir');

    }

    public function SetOngkir(Request $request )
    {
        $req = $request->all();
        $validator = \Validator::make($req,['biaya_ongkir' => 'required|numeric']);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','simpan_ongkir');
        }

        $data = ['biaya_ongkir' => $req['biaya_ongkir'], 'dibuat_oleh' => "Admin - ".Auth::User()->name ];
        $count = Ongkir::count();
        if($count > 0){
            $sel_ongkir = Ongkir::first();
            $sel_ongkir->update($data);
        }else{
            $input = Ongkir::create($data);
        }
    
        return redirect()->back()->with('success','Berhasil Set Ongkir');
    }   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $req = $request->all();
        if(isset($req['foto'])){
            $data_validator = [  'nama' => 'required',
                                 'no_hp' => 'required',
                                 'jenis_kendaraan' => 'required',
                                 'merek' => 'required',
                                 'no_polisi' => 'required',
                                 'foto' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG'];
        }else{
            $data_validator = [  'nama' => 'required',
                                 'no_hp' => 'required',
                                 'jenis_kendaraan' => 'required',
                                 'merek' => 'required',
                                 'no_polisi' => 'required'];
        }

        $validator = \Validator::make($req,$data_validator);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        $find = Kurir::findOrFail($req['id']);
        if(isset($req['foto'])){
            if(!empty($find->foto)){
                $hapusFoto = KompresFoto::HapusFoto($find->foto); 
            }
            
            $uploadFoto = KompresFoto::UbahUkuran($req['foto'],'kurir');
            $req['foto'] = $uploadFoto;
        }else{
            $req = $request->except(['foto']);
        }

        $find->update($req);
        return redirect()->back()->with('success','Berhasil Update Kurir');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $find = Kurir::findOrFail($id);
        $hapusFoto = KompresFoto::HapusFoto($find->foto);
        $find->delete();

        return redirect()->back()->with('success','Berhasil Hapus Kurir');
    }
}
