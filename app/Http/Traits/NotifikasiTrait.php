<?php
	namespace App\Http\Traits;
	use App\Notifikasi;
	use Auth;

	trait NotifikasiTrait{
		public function jumNotif($user_id)
		{
			$jumNotif = Notifikasi::where([['penerima_id','=',$user_id],
											 ['dibaca','=','0']
											])->count();
			return $jumNotif;
		}

		public function listNotif($user_id)
		{
			$listNotif = Notifikasi::where([['penerima_id','=',$user_id],['dibaca','=','0']])->limit('5')->orderBy('id','DESC')->get();
			return $listNotif;
		}

		public function buildNotif($user_id)
		{
			$jumNotif = $this->jumNotif($user_id);
			$daftarNotif = $this->listNotif($user_id);

			$notifikasi = ['jumNotif' => $jumNotif,'listNotif' => $daftarNotif];
			return $notifikasi;

		}
	}