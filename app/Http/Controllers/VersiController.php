<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Versi;
use App\Tax;

class VersiController extends Controller
{
  
    public function __construct()
    {
      
        $this->middleware(function($request,$next){
            if(Gate::allows('manage-versi')) return $next($request);
            abort(404,'Halaman Tidak Ditemukan');
        });
    }

    public function index()
    {
       $versi = Versi::first();

       $menu_active = "master|versi|0";
       return view('versi.index',compact('menu_active','versi'));
    }

  
    public function store(Request $request)
    {
        $req = $request->all();

        $validator = \Validator::make($req,['versi' => 'required']);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
        }

        $insert = Versi::create($req);

        return redirect()->back()->with('success','Berhasil Input Versi');

    }

    
    public function update(Request $request, $id)
    {
        $req = $request->all();
        $validator = \Validator::make($req,['versi' => 'required']);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        $find = Versi::findOrFail($req['id']);
        $find->update($req);
        return redirect()->back()->with('success','Berhasil Update Versi');
    }


    public function showPajak(){
        $tax = Tax::all();
        $menu_active = "master|versi|0";
        return view('tax.index',compact('menu_active','tax'));
    }


    public function updatePajak(Request $request){

        
        $req = $request->all();
     
        $data_validator = [ 'id' => 'required',
                           'status_aktif' => 'required' ];
        

        $validator = \Validator::make($req,$data_validator);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator,'edit')->withInput()->with('gagal','update');
        }

        $find = Tax::findOrFail($req['id']);
        $find->update($req);
        return redirect()->back()->with('success','Berhasil Update Kategori');

    }

  
}
