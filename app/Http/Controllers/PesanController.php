<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pesan;
use App\User;
use App\Notifikasi;
use App\Helpers\SendNotif;
use Auth;


class PesanController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	
	public function dashboardPesan()
	{
		$pesan = Pesan::distinct('user_id')
					   ->selectRaw("user_id,
					   			   (select name from users where id = pesan.user_id) as name,
					   			   (select max(id) from pesan as a where a.user_id = pesan.user_id) as id_max,
					   			   (select SUBSTR(b.pesan,1,31) from pesan as b where b.id = id_max) as pesan,
					   			   (select date_format(c.created_at, '%d/%m/%y %h:%i %p' ) from pesan as c where c.id = id_max) as waktu,
					   			   (select count(d.id) from pesan as d where d.user_id = pesan.user_id and d.dibaca = '0' and d.status='0') as jumPesan,
					   			   (select e.foto from users as e where e.id = pesan.user_id) as foto,
					   			   (select f.status_member from detail_konsumen as f where f.user_id = pesan.user_id  ) as status_member
					   			    ")->orderBy('waktu','desc')->get();
		

		return response()->json($pesan);
	}    

	public function listPesan(Request $request)
	{
		$req = $request->all();
		$pesan = Pesan::selectRaw("pesan.*, date_format(created_at, '%d/%m/%y %h:%i %p') as waktu_chat ")
						->where('user_id',$req['user_id'])->orderBy('created_at','asc')->get();

		return response()->json($pesan);
	}

	public function pesanUser(Request $request)
	{
		$req = $request->all();
		$validator = \Validator::make($req,['tujuan' => 'required', 
                                            'pesan' => 'required']);
    	if($validator->fails()){
          return redirect()->back()->withErrors($validator)->with('gagal','simpan')->withInput();
        }
        $req['user_id'] = $req['id'];
        $find_user = User::findOrFail($req['id']);

        $req['status'] = '1';
        $req['dibuat_oleh'] = Auth::User()->name;
        $insert = Pesan::create($req);

         //Insert Notifikasi
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $req['user_id'],
        'judul_id' => $insert->id,
        'judul' => 'Anda Memiliki Pesan Baru Dari AGOGO BAKERY',
        'isi' => $insert->pesan,
        'jenis_notif' => 5,
        'dibaca' => '0'
        ];

        $notif = Notifikasi::create($dnotif);
        // //NotifGCM ($pengirim,$judul,$pesan,$gambar, $id_user,$namaTable, $id)
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pesan', $notif->judul_id);

        return redirect()->back()->with('success','Berhasil Mengirimkan Pesan Ke '.$find_user->name);
	}

	
	public function kirimPesan(Request $request)
	{
		$req = $request->all();
		$req['status'] = '1';
		$user = User::findOrFail($req['user_id_k']);
		$req['user_id'] = $req['user_id_k'];
		$req['dibuat_oleh'] = $user->name;

		$insert = Pesan::create($req);
		$insert['waktu'] = $insert->created_at->format('d/m/y h:i A');
		
		$arr = ['id' => $insert->id, 
                'user_id' => $insert->user_id, 
                'name' => $user->name,
                'pesan' => substr($insert->pesan,0,31), 
                'dibaca' => '0' , 
                'status' => $insert->status,
                'waktu' => $insert->created_at->format('d/m/y h:i A'),
                'foto' => $user->foto,
                'jumPesan' => '0',
                'pesan_nda_potong' => $insert->pesan,
                'pengirim_id' => Auth::user()->id,
                'status_member' => $user->DetailKonsumen->status_member ];
		SendNotif::SendNotPesan('4',$arr);

		 //Insert Notifikasi
        $dnotif =
        [
        'pengirim_id' => Auth::User()->id,
        'penerima_id' => $req['user_id'],
        'judul_id' => $insert->id,
        'judul' => 'Anda Memiliki Pesan Baru Dari CS AGOGO',
        'isi' => $insert->pesan,
        'jenis_notif' => 5,
        'dibaca' => '0'
        ];
        $notif = Notifikasi::create($dnotif);

        // //NotifGCM ($pengirim,$judul,$pesan,$gambar, $id_user,$namaTable, $id)
        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'pesan', $notif->judul_id);


		$success = 1;
		return response()->json(['success' => $success, 'msg' => $insert]);
	}

	public function hapusPesan($id)
	{
		$find = Pesan::findOrFail($id);
		$find->delete();
		return response()->json(['success' => '1', 'msg' => 'Berhasil Hapus Pesan']);
	}

	public function getJumPesan()
	{
		$jumNotPesan = Pesan::where(['status' => '0', 'dibaca' => '0'])->count();
		return response()->json($jumNotPesan);
	}

	public function bacaPesan($id)
	{
		$update = Pesan::where('user_id',$id);
		$update->update(['dibaca' => '1']);

		$arr = ['user_id' => $id];


		SendNotif::SendNotPesan('3',$arr);
		return response()->json(['success' => '1']);
	}

	public function cariUser(Request $request)
	{
		$req = $request->all();
		$nama = $req['nama'];


		$listUser = User::where([ 
								  ['name','LIKE' ,"%".$nama."%"],
								  ['level_id', '=', '3']
							    ])
						 ->selectRaw("id as user_id,
						 			  name,
						 			  foto,
						 			  (select max(a.id) from pesan as a where a.user_id = users.id) as id_max,
						   			  (select SUBSTR(b.pesan,1,31) from pesan as b where b.id = id_max) as pesan,
						   			  (select date_format(c.created_at, '%d/%m/%y %h:%i %p' ) from pesan as c where c.id = id_max) as waktu,
						   			  (select count(d.id) from pesan as d where d.user_id = users.id and d.dibaca = '0' and d.status='0') as jumPesan
						 			")
						 ->get();

		return response()->json(['success'=> '1', 'msg' => $listUser]);
	}
}
