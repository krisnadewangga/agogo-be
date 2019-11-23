<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pengiriman;
use App\Transaksi;
use Carbon\Carbon;

class PengirimanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $pengiriman = Pengiriman::where('status','0')->get();

       return view('transaksi.pengiriman',compact('pengiriman'));

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
        $validator = \Validator::make($req,['kurir_id' => 'required']);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->with('gagal','simpan');
        }

        $input = ['transaksi_id' => $req['transaksi_id'],
                  'kurir_id' => $req['kurir_id'],
                  'dikirimkan' => Carbon::now(),
                  'status' => '0' ];
        // return $input;
        $insert = Pengiriman::create($input);
        $find = Transaksi::findOrfail($req['transaksi_id']);
        $find->update(['status' => '2']);   
        
        //Insert Notifikasi
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
