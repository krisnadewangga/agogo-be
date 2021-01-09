<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notifikasi;
use App\HistoriTopup;
use App\Transaksi;
use App\Helpers\Acak;
use App\Helpers\SendNotif;
use Carbon\Carbon;
use Auth;

class TopupSaldoController extends Controller
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
        $menu_active = "item|topup|0";
        return view('topup_saldo.index',compact('menu_active'));
    }

    public function ListTopupSaldo()
    {
        $menu_active = "transaksi|topup";
        $list_topup = HistoriTopup::join('users as a','a.id','=','histori_topup.user_id')
                                    ->join('detail_konsumen as b','a.id','=','b.user_id')
                                    ->select('histori_topup.*','a.name','a.no_hp','b.alamat')
                                    ->orderBy('created_at','desc')->get();

      
        return view('topup_saldo.list',compact('menu_active','list_topup'));
    }

    public function CariUser(Request $request)
    {
        $param = $request->param;
        $search = User::join('detail_konsumen as a', 'users.id', '=', 'a.user_id')
                        ->where('users.status_aktif','=','1')
                        ->where('name','like','%'.$param.'%')
                        ->orWhere('no_hp','=',$param)

                        ->get();
        $jumCount = $search->count();
        $response = ['jumlah' => $jumCount, 'msg' => $search];

        return response($response);

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
        $validator = \Validator::make($req,['saldo' => 'required|numeric']);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
        }
  

        $find = User::findOrfail($req['user_id']);
        $new_saldo = $find->DetailKonsumen->saldo + $req['saldo'];

        if($req['status_member'] == '0'){
            $dataUpdate = ['status_member' => '1','saldo' => $new_saldo];
        }else if($req['status_member'] == '1'){
            $dataUpdate = ['saldo' => $new_saldo];
        }else{
            return redirect()->back();
        }

        $find->DetailKonsumen()->update($dataUpdate);
        $find->HistoriTopup()->create(['user_id' => $req['user_id'], 
                                        'nominal' => $req['saldo'],
                                        'ditopup_oleh' => 'Admin - '.Auth::User()->name    
                                      ]);

        $forCode = Carbon::now()->format('Ymd');
        $maxKD = Transaksi::where('no_transaksi','LIKE','T'.$forCode.'%')->orderBy('id','DESC')->first();
        if(!empty($maxKD->id)){
            $nexKD = Acak::AmbilId($maxKD['no_transaksi'],'T'.$forCode,9,3);    
        }else{
            $nexKD = 'T'.$forCode.'001';
        }

        $transaksi = Transaksi::create(['user_id' => $req['user_id'],
                                        'no_transaksi' => $nexKD,
                                        'total_transaksi' => $req['saldo'],
                                        'biaya_pengiriman' => 0,
                                        'jarak_tempuh' => 0,
                                        'total_biaya_pengiriman' => 0,
                                        'kode_voucher' => '-',
                                        'potongan' => 0,
                                        'total_bayar' => $req['saldo'],
                                        'banyak_item' => 0,
                                        'alamat_lain' => '0',
                                        'lat' => '-',
                                        'long' => '-',
                                        'detail_alamat' => '-',
                                        'metode_pembayaran' => '3',
                                        'transaksi_member' => '1',
                                        'status' => '5',
                                        'tgl_bayar' => Carbon::now()->format('Y-m-d H:i:s'),
                                        'catatan' => '-',
                                        'durasi_kirim' => 0,
                                        'waktu_kirim' => Carbon::now()->format('Y-m-d H:i:s'),
                                        'for_ps' => '0',
                                        'top_up' => '1'
                                      ]);
        //Insert Notifikasi
       
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $find->id,
        'judul_id' => $find->id,
        'judul' => 'Topup Saldo Agogo',
        'isi' => 'Anda Baru Saja Melakukan Topup Saldo Di Agogo Bakery Sebesar Rp '.number_format($req['saldo'],'0','','.'),
        'jenis_notif' => 4,
        'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);

        $userWa = User::findOrfail($find->id);
        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);

        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'histori_topup', $notif->judul_id);



        return redirect()->back()->with('success','Berhasil Topup Saldo');
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
