<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\SendNotif;
use App\Pengiriman;
use App\Transaksi;
use App\Notifikasi;
use App\Kurir;
use Carbon\Carbon;
use Auth;

class PengirimanController extends Controller
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
       $pengiriman = Pengiriman::where('status','0')->orderBy('created_at','desc')->get();
       $menu_active = "transaksi|pengiriman|0";
       return view('transaksi.pengiriman',compact('pengiriman','menu_active'));
       
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

        $cek_kurir = Pengiriman::where('kurir_id','=',$req['kurir_id'])->where('status','=','0')
                               ->count();

        if($cek_kurir > 0){
            return redirect()->back()->with('gagal','kurir')->with("error","Kurir Yang Anda Pilih Sementara Melakukan Pengiriman");
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
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $find->user_id,
        'judul_id' => $find->id,
        'judul' => 'Pengiriman Pesanan Nomor Transaksi '.$find->no_transaksi,
        'isi' => 'Pesanan Dengan Nomor Transaksi '.$find->no_transaksi.' Telah Dikirimkan Kerumah Anda',
        'jenis_notif' => 2,
        'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);

        SendNotif::SendNotPesan('5',['jenisNotif' => '2']);
        
        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pengiriman', $notif->judul_id);

        return redirect()->back()->with("success","Berhasil Mengaktifkan Pengiriman");
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
