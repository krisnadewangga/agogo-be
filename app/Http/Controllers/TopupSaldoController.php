<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notifikasi;
use App\HistoriTopup;
use App\Helpers\SendNotif;
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
        $menu_active = "transaksi|topup|0";
        return view('topup_saldo.index',compact('menu_active'));
    }

    public function ListTopupSaldo()
    {
        $menu_active = "transaksi|topup";
        $list_topup = HistoriTopup::orderBy('created_at','desc')->get();

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
