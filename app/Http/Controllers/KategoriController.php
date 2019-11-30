<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\KompresFoto;
use App\Kategori;


class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kategori = Kategori::all();
        $menu_active = "item|kategori";
        return view('item.kategori', compact('kategori','menu_active'));
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
       $validator = \Validator::make($req,['kategori' => 'required|unique:kategori', 
                                           'gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG']);
       if($validator->fails()){
         return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
       }

       $req['gambar'] = KompresFoto::UbahUkuran($req['gambar'],'kategori');
       $insert = Kategori::create($req);
        
       return redirect()->back()->with('success','Berhasil Input Kategori');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
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
        if(isset($req['gambar'])){
            $data_validator = ['kategori' => 'required', 
                                                'gambar' => 'required|image|mimes:jpeg,png,jpg,JPG,PNG,JPEG',
                                                'status_aktif' => 'required' ];
        }else{
            $data_validator = [ 'kategori' => 'required',
                           'status_aktif' => 'required' ];
        }

        $validator = \Validator::make($req,$data_validator);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        $find = Kategori::findOrFail($req['id']);
        if(isset($req['gambar'])){
            if(!empty($find->gambar)){
                $hapusFoto = KompresFoto::HapusFoto($find->gambar); 
            }
            
            $uploadFoto = KompresFoto::UbahUkuran($req['gambar'],'kategori');
            $req['gambar'] = $uploadFoto;
        }else{
            $req = $request->except(['gambar']);
        }

        $find->update($req);
        return redirect()->back()->with('success','Berhasil Update Kategori');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $find = Kategori::findOrFail($id);
       $find->delete();
       return redirect()->back()->with('success','Berhasil Hapus Kategori');
    }
}
