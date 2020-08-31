<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kurir;
use App\User;
use App\Notifikasi;
use App\Transaksi;
use App\Helpers\SendNotif;
use Carbon\Carbon;
use Validator;
use Auth;

class KurirController extends Controller
{
  public function login(Request $request)
	{

		$req = $request->all();
        $messsages = array( 
                            'no_hp.required'=>'no_hp Harus Diisi',
                            'password.required'=>'password Harus Diisi',
                           );   

        $rules = array( 'no_hp' => 'required',
                        'password' => 'required',
                      );

        $validator = Validator::make($request->all(), $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
   
        }else{
            if(Auth::attempt(['no_hp' => $req['no_hp'], 'password' => $req['password'] ])){
                $user = Auth::user()->where('no_hp',$req['no_hp'])
                					->where('status_aktif','1')
                			 	    ->whereNull('deleted_at')
                			        ->where('level_id','8')
                					->first();
              	
              	if(isset($user->id)){
              		$user['jenis_kendaraan'] = $user->Kurir->jenis_kendaraan;
              		$user['merek'] = $user->Kurir->merek;
              		$user['no_polisi'] = $user->Kurir->no_polisi;

              		$success = 1;
                    $msg = $user;
              	}else{
              		$success = 0;
                  	$msg = "Gagal Login, Periksan Kembali No Hp Dan Password Anda";
              	}
            }else{
                $success = 0;
                $msg = "Gagal Login, Periksan Kembali No Hp Dan Password Anda";
            } 
        }
        return response()->json(['success' => $success, 'msg' => $msg],200);
	}

	public function DetailKurir(Request $request)
	{
		$req = $request->all();
		$rules = ['user_id' => 'required'];
		$messages = ['user_id.required' => 'user_id Tidak Bisa Kosong'];

		$validator = Validator::make($request->all(), $rules,$messages);
    if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();

    }else{
    	$user = User::join('kurir','kurir.user_id','=','users.id')
    				->select('users.*','kurir.jenis_kendaraan','kurir.merek','kurir.no_polisi')
    				->where('status_aktif','1')
			 	    ->whereNull('deleted_at')
			        ->where('users.level_id','8')
			        ->where('users.id',$req['user_id'])
					->first();

		if(isset($user->id)){
			$success = 1;
        	$msg = $user;
		}else{
			$success =0;
			$msg = "Kurir Tidak Ditemukan";
		}
    	

    }

    return response()->json(['success' => $success, 'msg' => $msg],200);
	}

	public function JobNow(Request $request)
	{
		$req = $request->all();
    $rules = ['user_id' => 'required'];
    $messages = ['user_id.required' => 'user_id Tidak Bisa Kosong'];

    $validator = Validator::make($request->all(), $rules,$messages);
    if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();

    }else{
      $transaksi = User::join('kurir','kurir.user_id','=','users.id')
                      ->join('pengiriman as a','a.kurir_id','=','kurir.id')
                      ->join('transaksi as b','b.id','=','a.transaksi_id')
                      ->where('users.id',$req['user_id'])
                      ->where('b.status','2')
                      ->selectRaw("b.id,
                                   b.no_transaksi,
                                   b.total_bayar,
                                   b.banyak_item,
                                   (select name from users where id = b.user_id) as pelanggan,
                                   b.lat,
                                   b.long,
                                   b.detail_alamat,
                                   (select no_hp from users where id = b.user_id) as no_hp
                                  ")
                     
                      ->first();
            
      if(isset($transaksi->id)){
          $success = 1;
          $msg = $transaksi;
      }else{
          $success =0;
          $msg = "Belum Ada Job";
      }
    }

    return response()->json(['success' => $success, 'msg' => $msg],200);
	}

	public function ListJob(Request $request)
	{
		$req = $request->all();
    $messsages = ['dataPerpage.required' => 'dataPerpage Tidak Bisa Kosong',
                  'page.required' => 'page Tidak Bisa Kosong',
                  'user_id.required' => 'user_id Tidak Bisa Kosong'];
    $rules = ['page' => 'required', 'dataPerpage' => 'required','user_id' => 'required'];

    $validator = Validator::make($req, $rules,$messsages);
    if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
          $kr = 400;
          $pageSaatIni = 0;
          $tampilPS = 0;
    }else{
       $page = $req['page'];
       $dataPerpage = $req['dataPerpage'];
       $offset = ($page - 1) * $dataPerpage;
       
       $transaksi = User::join('kurir','kurir.user_id','=','users.id')
                      ->join('pengiriman as a','a.kurir_id','=','kurir.id')
                      ->join('transaksi as b','b.id','=','a.transaksi_id')
                      ->where('users.id',$req['user_id'])
                      ->where('b.status','5')
                      ->selectRaw("b.id,
                                   b.no_transaksi,
                                   b.total_bayar,
                                   b.banyak_item,
                                   (select name from users where id = b.user_id) as pelanggan,
                                   b.lat,
                                   b.long,
                                   b.detail_alamat,
                                   (select no_hp from users where id = b.user_id) as no_hp
                                  ")
                      ->limit($dataPerpage)
                      ->offset($offset)->get();
                     
       $jumdat = User::join('kurir','kurir.user_id','=','users.id')
                      ->join('pengiriman as a','a.kurir_id','=','kurir.id')
                      ->join('transaksi as b','b.id','=','a.transaksi_id')
                      ->where('users.id',$req['user_id'])
                      ->where('b.status','5')
                      ->count();

       $jumHal = ceil($jumdat / $dataPerpage);
       $pageSaatIni = (int) $page;
       $pageSelanjutnya = $page+1;
       if( ($pageSaatIni == $jumHal) || ($jumHal == 0) ){
           $tampilPS = 0;
       }else{
           $tampilPS = $pageSelanjutnya;
       }

       $success = 1;
       $msg = $transaksi;
       $kr = 200;
    }
    
    return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
	}

	public function SelesaikanJob(Request $request)
	{
		$req = $request->all();
    $rules = ['transaksi_id' => 'required', 'diterima_oleh' => 'required'];
    $messages = ['transaksi_id.required' => 'Transaksi Id Tidak Bisa Kosong', 'diterima_oleh.required' => 'diterima_oleh Tidak Bisa Kosong'];

    $validator = Validator::make($request->all(), $rules,$messages);
    if($validator->fails()){
        $success = 0;
        $msg = $validator->messages()->all();
    }else{
        $transaksi = Transaksi::findOrFail($req['transaksi_id']);
        $transaksi->update(['status' => '5','tgl_bayar' => Carbon::now() ]);

        $transaksi->Pengiriman()->update(['diterima' => Carbon::now(), 
                                          'diterima_oleh' => $req['diterima_oleh'],
                                          'status' => '1']);
        
        //Insert Notifikasi
        $dnotif =
        [
        'pengirim_id' => '1',
        'penerima_id' => $transaksi->user_id,
        'judul_id' => $transaksi->id,
        'judul' => 'Terima Pesanan Nomor Transaksi '.$transaksi->no_transaksi,
        'isi' => 'Pesanan Dengan Nomor Transaksi '.$transaksi->no_transaksi.' Telah Diterima, Terimakasih Telah Berbelanja Di AgogoBakery',
        'jenis_notif' => 3,
        'dibaca' => '0'
        ];
        
        $notif = Notifikasi::create($dnotif);
        SendNotif::SendNotPesan('5',['jenisNotif' => '2']);

        //NotifGCM
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pengiriman', $notif->judul_id);
        
        $this->setKunciTransaksi($transaksi->user_id);

        if($transaksi){
          $success = 1;
          $msg = "Berhasi Selesaikan Job";
        }else{
          $success = 0;
          $msg = "Gagal Selesaikan Job";
        }
    }

    return response()->json(['success' => $success, 'msg' => $msg],200);
	}

  public function setKunciTransaksi($user_id)
  {
      $sel_user = User::findOrFail($user_id);
      $transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
      $kunci_transaksi = $sel_user->DetailKonsumen->kunci_transaksi;

      if($transaksi_berlangsung < '3' && $kunci_transaksi == '1'){
          $sel_user->DetailKonsumen()->update(['kunci_transaksi' => '0']);
      }
      
      $success = 1;
      return $success;
  }
}
