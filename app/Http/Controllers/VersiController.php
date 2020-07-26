<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Versi;

class VersiController extends Controller
{
  
    public function __construct()
    {
        $this->middleware('auth');
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

  
}
