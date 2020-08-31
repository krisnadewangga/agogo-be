<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Helpers\KompresFoto;
use App\Kurir;
use App\Ongkir;
use App\User;
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
        $kurir = Kurir::whereIn('user_id',function($query){
            return $query->select('id')
                         ->from('users')
                         ->whereNull('deleted_at');
        })->get();
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
        $validator = \Validator::make($req,['name' => 'required',
                                            'name' => Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                        
                                                      }),
                                            'no_hp' => 'required',
                                            'no_hp' => Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                        
                                                      }),
                                            'email' => 'required|email',
                                            'email' => Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                        
                                                      }),
                                            'password' => 'required',
                                            'jenis_kendaraan' => 'required',
                                            'merek' => 'required',
                                            'no_polisi' => 'required',
                                            'foto' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG']);
       
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput()->with('gagal','simpan');
        }

        $dataKurir = $request->except('name','email','no_hp','foto','password');
        $dataUser = $request->only('name','email','no_hp','foto','password');
        $dataUser['foto'] = KompresFoto::Upload($req['foto'],'kurir');
        $dataUser['level_id'] = '8';
        $dataUser['password'] = bcrypt($request->password);

        $insert = User::create($dataUser);
        $dataKurir['user_id'] = $insert->id;
        $insertK = Kurir::create($dataKurir);

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
        $data_validator = ['jenis_kendaraan' => 'required',
                           'merek' => 'required',
                           'no_polisi' => 'required'];

        $find = User::findOrFail($req['id']);
        if($find->email != $req['email']){
            $data_validator['email'] = 'required';
            $data_validator['email'] = Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                                     
                                                      });

        }

        if($find->no_hp != $req['no_hp']){
            $data_validator['no_hp'] = 'required';
            $data_validator['no_hp'] = Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                      });
        }
        if($find->name != $req['name']){
            $data_validator['name'] = 'required';
            $data_validator['name'] = Rule::unique('users')->where(function ($query) {
                                                        return $query->where('level_id','8')
                                                                     ->whereNull('deleted_at');
                                                      });
        }

        if(isset($req['password'])){
            $data_validator['password'] = 'required|min:6';
        }

        if(isset($req['foto'])){
            $data_validator['foto'] = 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG';
        }
        
        $validator = \Validator::make($req,$data_validator);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        
        $dataUser = ['name' => $req['name'], 'no_hp' => $req['no_hp'],'email' => $req['email'], 'status_aktif' => $req['status_aktif'] ];
        $dataKurir = ['jenis_kendaraan' => $req['jenis_kendaraan'],'merek' => $req['merek'], 'no_polisi' => $req['no_polisi']];

        if(isset($req['password'])){
           $dataUser['password'] = bcrypt($request->password);
        }

        if(isset($req['foto'])){
            if(!empty($find->foto)){
                $hapusFoto = KompresFoto::HapusFoto($find->foto); 
            }

            $uploadFoto = KompresFoto::Upload($req['foto'],'kurir');
            $dataUser['foto'] = $uploadFoto;
        }

      

        $find->Update($dataUser);
        $find->Kurir()->Update($dataKurir);

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
        $find = User::findOrFail($id);
       

        $hapusFoto = KompresFoto::HapusFoto($find->foto);
        $find->delete();

        return redirect()->back()->with('success','Berhasil Hapus Kurir');
    }
}
