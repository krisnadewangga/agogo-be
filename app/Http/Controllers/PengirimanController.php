<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\SendNotif;
use App\Pengiriman;
use App\Transaksi;
use App\Item;
use App\Notifikasi;
use App\Kurir;
use App\User;
use Carbon\Carbon;
use Auth;
use DB;

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

        // $cek_kurir = Pengiriman::where('kurir_id','=',$req['kurir_id'])->where('status','=','0')
        //                        ->count();

        // if($cek_kurir > 0){
        //     return redirect()->back()->with('gagal','kurir')->with("error","Kurir Yang Anda Pilih Sementara Melakukan Pengiriman");
        // }

        $input = ['transaksi_id' => $req['transaksi_id'],
                  'kurir_id' => $req['kurir_id'],
                  'dikirimkan' => Carbon::now(),
                  'status' => '0' ];
        // return $input;
        $insert = Pengiriman::create($input);
        $find = Transaksi::findOrfail($req['transaksi_id']);
        $find->update(['status' => '2']);   
        $min_stock_item = $this->UpdateStock($find->no_transaksi);
        
        //Insert Notifikasi
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $find->user_id,
        'judul_id' => $find->id,
        'judul' => 'Pengiriman Pesanan Nomor Transaksi '.$find->no_transaksi,
        'isi' => 'Pesanan Dengan Nomor Transaksi '.$find->no_transaksi.' Dalam Pengiriman Kerumah Anda',
        'jenis_notif' => 2,
        'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);

        SendNotif::SendNotPesan('5',['jenisNotif' => '2']);

        $userWa = User::findOrfail($find->user_id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);

        
        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pengiriman', $notif->judul_id);


        return redirect()->back()->with("success","Berhasil Mengaktifkan Pengiriman");
    }

    public function UpdateStock($no_transaksi)
    {
        $sel = Transaksi::where('no_transaksi',$no_transaksi)->first();
        $itemTransaksi = $sel->ItemTransaksi;

        foreach ($itemTransaksi as $key ) {
           $find = Item::findOrFail($key['item_id']);
           $newStock = $find->stock - $key['jumlah'];
           $update = $find->update(['stock' => $newStock]);

           DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['jumlah']);
                    
           DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['jumlah']);
                    
           DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['jumlah']);

        }
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
