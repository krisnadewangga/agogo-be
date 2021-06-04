<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Transaksi;
use App\Notifikasi;
use App\User;
use App\Item;
use App\Ongkir;
use App\ItemTransaksi;
use App\HistoriTopup;
use App\Helpers\Acak;
use App\Helpers\SendNotif;
use App\Helpers\tripay;
use Ixudra\Curl\Facades\Curl;
use App\NotifExpired;
use App\Otp;
use Validator;
use DB;



class TransaksiController extends Controller
{
    public function Store(Request $request)
    {
    
    	$req = $request->all();
    	// return $req['metode_pembayaran'];

		$rules = [  'user_id' => 'required',
					'total_transaksi' => 'required|numeric',
					'biaya_pengiriman' => 'required|numeric',
					'jarak_tempuh' => 'required|numeric',
					'total_biaya_pengiriman' => 'required|numeric',
					'total_bayar' => 'required|numeric',
					'alamat_lain' => ['required', Rule::in(['0', '1']) ],
					'lat' => 'required',
					'long' => 'required',
					'detail_alamat' => 'required',
					'metode_pembayaran' => ['required', 
											Rule::in(['1', '2', '3','4'])],
					'banyak_item' => 'required',
					'waktu_kirim' => 'required',
					// 'confirm_stock' => 'required',
					// 'confirm_operasional' => 'required',
					'confirm_error' => 'required'
		         ];
		// return $req['metode_pembayaran'];
		if($req['metode_pembayaran'] == "2"){
			$rules['method'] = 'required';
			$rules['opsi_bt'] = 'required';
			$rules['biaya_admin'] = 'required';
		}
		
		$sel_user = User::findOrFail($req['user_id']);
		$transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )
		->where('created_at','<',date('Y-m-d H:i:s'))->count();
    	// if($transaksi_berlangsung == '3'){
    	// 	$sel_user->DetailKonsumen()->update(['kunci_transaksi' => '1']);
    	// }
		
		// if($sel_user->DetailKonsumen->kunci_transaksi == 0){

		if($transaksi_berlangsung < '3'){

			$itemTransaksi = [];
			$countItemError = 0;
			$ItemError = [];
			for($i=1; $i<=$req['banyak_item']; $i++){
				$rules['item_id'.$i] = 'required';
				$rules['jumlah'.$i] = 'required|numeric';
				$rules['harga'.$i] = 'required|numeric';
				$rules['margin'.$i] = 'required|numeric';
				
				$selItem = Item::findOrFail($req['item_id'.$i]);
				if($selItem->stock < $req['jumlah'.$i]){
					$countItemError += 1;	
					$ItemError[] = $selItem->nama_item;
				}

				$itemTransaksi[$i-1] = ['item_id' => $req['item_id'.$i], 
										'jumlah' => $req['jumlah'.$i],
										'harga' => $req['harga'.$i],
										'margin' => $req['margin'.$i],
									   ];

				if(isset($req['diskon'.$i])){
					$itemTransaksi[$i-1]['diskon'] = $req['diskon'.$i];
					$itemTransaksi[$i-1]['harga_diskon'] = $req['harga_diskon'.$i];
					$itemTransaksi[$i-1]['total'] = $req['jumlah'.$i] * $req['harga_diskon'.$i];
				}else{
					$itemTransaksi[$i-1]['total'] = $req['jumlah'.$i] * $req['harga'.$i];
				}
				
			}

			
			
			if($req['confirm_error'] == 0){
				// stok
				if($countItemError > 0){
					$tempMsgItem = "";
					for($a=1; $a<=$countItemError; $a++ ){
						if($a < $countItemError && $a != $countItemError-1){
							$sambungan = ", ";
						}else if($a == $countItemError-1 ){
							$sambungan = " dan ";
						}else{
							$sambungan = ".";
						}
						$tempMsgItem .= $ItemError[$a-1].$sambungan;
					}
				
					$msgItem = "Pesanan Untuk ".$tempMsgItem." Saat ini stock kosong/stok tidak mencukupi ";
					$req['waktu_kirim'] = Carbon::now()->add(1,'day')->format('Y-m-d')." 08:00:00";
				}

				// operasional
				$jamBuka = strtotime('07:00:00');
				$jamTutup = strtotime('20:00:00');
				$jamSekarang = strtotime(date('H:i:s'));

				if( ($jamSekarang > $jamTutup) || ($jamSekarang < $jamBuka) ){
					$operasional = 0;
					$msg_operasional = "Saat ini sudah bukan Waktu Operasional";
					
					$req['waktu_kirim'] = Carbon::now()->add(1,'day')->format('Y-m-d')." 08:00:00";
				}else{
				
					$operasional = 1;
					$msg_operasional = "";
				}


				if($operasional == 0 && $countItemError == 0 ){ 
					$confirm_error = 0;
					$msg = "Maaf ! ".$msg_operasional." pesanan anda akan diproses besok ".Carbon::now()->add(1,'day')->format('d/m/Y')." setelah Pukul 07:00 AM";
				}elseif($operasional == 0 && $countItemError > 0){
					$confirm_error = 2;
					$msg = "Maaf ! ".$msg_operasional." Dan ".$msgItem." pesanan anda akan diproses besok ".Carbon::now()->add(1,'day')->format('d/m/Y')." setelah Pukul 07:00 AM";
				}elseif($operasional == 1 && $countItemError >  0){
					$confirm_error = 0;
					$msg = "Maaf !  ".$msgItem.", pesanan anda akan diproses besok ".Carbon::now()->add(1,'day')->format('d/m/Y')." setelah Pukul 07:00 AM";
				}else{
					$confirm_error = 1;
				}

				
			}else{
				$confirm_error = 1;
				$msg = "";
				$data = "";
			}

		

			$validator = Validator::make($req, $rules);
		    if($validator->fails()){
		        $success = 0;
		        $msg = $validator->messages()->all();
		        $response = $msg;
		        $data = "";
		    }else{
				$req_transaksi = $request->only('user_id',
												'total_transaksi',
												'biaya_pengiriman',
												'jarak_tempuh',
												'total_biaya_pengiriman',
												'kode_voucher',
												'potongan',
												'total_bayar',
												'alamat_lain',
												'lat',
												'long',
												'detail_alamat',
												'metode_pembayaran',
												'banyak_item',
												'catatan',
												'waktu_kirim');	

				if($req['metode_pembayaran'] == "2"){
					$req_transaksi['opsi_bt'] = $request->opsi_bt;
					$req_transaksi['biaya_admin'] = $request->biaya_admin;
				}

				$saldo = $sel_user->DetailKonsumen->saldo;
				$status_member = $sel_user->DetailKonsumen->status_member;
				

				if($confirm_error == 1){
					if($req['metode_pembayaran'] == '1'){
						if($status_member == "1"){
							if($saldo > $req['total_bayar'] ){

								if(isset($req['otp'])){
									$findOtp = Otp::where('user_id',$req['user_id'])->first();
									if($findOtp->otp == $req['otp']){
										$req_transaksi['tgl_bayar'] = Carbon::now();
										$req_transaksi['transaksi_member'] = '1';
										// return $req_transaksi;
										$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
										
										// $min_stock_item = $this->UpdateStock($itemTransaksi);

										$new_saldo = $saldo - $req['total_bayar'];
										$this->UpdateSaldo($req['user_id'],$new_saldo);



									     $pesanWa = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi " .$ins_transaksi->no_transaksi." \n Dengan Metode Pembayaran melalui saldo anda sebesar ". $req['total_bayar']. "\n yang sebelumnya saldo anda ".$saldo.", sisah saldo anda sekarang ".$new_saldo;

										// notif android
										$dnotif =[
												'pengirim_id' => '1',
												'penerima_id' => $req['user_id'],
												'judul_id' => $ins_transaksi->id,
												'judul' => 'Pesanan No '.$ins_transaksi->no_transaksi,
												'isi' => $pesanWa,
												'jenis_notif' => 1,
												'dibaca' => '0'
												];
										$a = SendNotif::sendNotifWa($sel_user->no_hp,$pesanWa);        	
										$notif = Notifikasi::create($dnotif);
										$sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pesanan Baru', $notif->judul_id);
									
									
									

											$success = 1;
											$msg = "Berhasil Simpan Transaksi";
											$data = '';
										}else{
											$success = 0;
											$msg = "Kode Otp Yang Dmasukan Salah";
											$data = '';
										}
								}else{
									$success = 3;
									$msg = "Masukan Kode Otp";
									$data = '';
								}


							}else{
								$success = 0;
								$msg = "Saldo Anda Tidak Cukup";
								$data = '';
							}
						}else{
							$success = 0;
							$msg = "Maaf! Silahkan Daftarkan Akun Anda Menjadi Member";
							$data = '';
						}

					}else if($req['metode_pembayaran'] == "2"){
					
						$waktu_skrang = Carbon::now();
						$waktu_skrang1 = Carbon::now();

						$batas_bayar = $waktu_skrang->addHours(6);
						$timesTampBB = Carbon::parse($batas_bayar)->timestamp; 

						$kirim_notif = $waktu_skrang1->addMinutes(330)->format('Y-m-d H:i:s');
						$req_transaksi['waktu_kirim_tf'] = $batas_bayar;
						$req_transaksi['waktu_kirim'] = $req['waktu_kirim'];

						

						// return $req_transaksi;	
						$req_transaksi['status'] = '6';

						
						$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
						// $min_stock_item = $this->UpdateStock($itemTransaksi);
						
						$itemForEmail = "";
						$selitemForEmail = Transaksi::findOrFail($ins_transaksi->id);
						$dataItemForEmail = $selitemForEmail->ItemTransaksi()->get();
						$signature = tripay::Signature($ins_transaksi->no_transaksi,$ins_transaksi->total_bayar);
						$noItemForEmail = 1;
					
						$order = [];
						foreach ( $dataItemForEmail as $key) {
							$itemForEmail .= "<tr>
											  	<td align='center'>".$noItemForEmail."</td>
											  	<td>".$key->Item->nama_item."</td>
											  	<td align='center'>".$key->jumlah." PCS</td>
											  	<td align='right'>Rp. ".number_format($key->harga,'0','','.')."</td>
											  	<td align='right'>Rp. ".number_format($key->total,'0','','.')."</td>
											  </tr>";
							$noItemForEmail++;

							$order['name'] = $key->Item->nama_item;
							$order['price'] = $key->Item->harga;
							$order['quantity'] = $key->jumlah;

							$arr_order[] = $order;
						}

						$itemForEmail .= "<tr>
											 <td align='center'>#</td>
											 <td colspan='3'>Ongkir </td>
											 <td align='right'>Rp. ".number_format($ins_transaksi->total_biaya_pengiriman,'0','','.')."</td>
										  </tr>
										  <tr>
											 <td align='center'>#</td>
											 <td colspan='3'>Total Bayar </td>
											 <td align='right'>Rp. ".number_format($ins_transaksi->total_bayar,'0','','.')."</td>
										  </tr>";

						$ongkir = ['name' => 'Ongkir', 'price' => $req['biaya_pengiriman'], 'quantity' => $req['jarak_tempuh'] ];
						array_push($arr_order, $ongkir);

				
						$data = ['method' => $req['method'] ,
				                 'merchant_ref' => $ins_transaksi->no_transaksi,
					             'amount'=> $ins_transaksi->total_bayar,
					             'customer_name' => $sel_user->name,
					             'customer_email' => $sel_user->email,
					             'customer_phone' => $sel_user->no_hp,
					             'order_items' => $arr_order,
					             'callback_url' => '',
					             'return_url' => '',
					             'expired_time' => $timesTampBB,
					             'signature' => $signature,
								];
						
						
								// real production
								$sendData = Curl::to('https://payment.tripay.co.id/api/transaction/create')
								->withData( $data )
								->withHeader('Authorization: Bearer 4synTlbXG2qsABvPRz7aT16aeq88fP4fhJKz3a1D')
								->asJson()
								->post();
							

					     #mode development		
				       	// $sendData = Curl::to('https://payment.tripay.co.id/api-sandbox/transaction/create')
				        //                 ->withData( $data )
				        //                 ->withHeader('Authorization: Bearer DEV-kLKOWQyiJfC2GUJq0myEhEldoUKORxYUGSLg5eeg')
				        //                 ->asJson()
						//                 ->post();
						



					 $ff = $sendData->data;
				    	$pesanWa = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi " .$ins_transaksi->no_transaksi." \nSegera Lakukan Pembayaran Dengan Mentransfer dengan total ".number_format($ins_transaksi->total_bayar + (int) $req['fee'] ,'0','','.')." Ke ".$ff->payment_name." : \nKode Pembayaran ".$ff->pay_code."  \nAtau Bisa melalui link ini  \n".$ff->checkout_url."\n Batas Waktu Pembayaran ".$ins_transaksi->waktu_kirim_tf->format('d/m/Y H:i A');
				    	$pesanAndro = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi " .$ins_transaksi->no_transaksi." \nSegera Lakukan Pembayaran Dengan Mentransfer dengan total ".number_format($ins_transaksi->total_bayar + (int) $req['fee'] ,'0','','.')." Ke ".$ff->payment_name." : \nKode Pembayaran ".$ff->pay_code."  \nAtau Bisa melalui link ini <a href='".$ff->checkout_url."'>".$ff->checkout_url."</a> Batas Waktu Pembayaran".$ins_transaksi->waktu_kirim->format('d/m/Y H:i A');


					    // notif android
					    $dnotif =[
				                'pengirim_id' => '1',
				                'penerima_id' => $req['user_id'],
				                'judul_id' => $ins_transaksi->id,
				                'judul' => 'Pembayaran Pesanan No '.$ins_transaksi->no_transaksi,
				                'isi' => $pesanAndro,
				                'jenis_notif' => 1,
				                'dibaca' => '0'
				                ];

				        //setNotifExpired
						$dataNE = ['transaksi_id' => $ins_transaksi->id, 
								   'email' => $sel_user->no_hp,
								   'waktu_kirim' => $kirim_notif ,
								   'item' => $itemForEmail,
								   'status' => '0'];
						$insDataNE = NotifExpired::create($dataNE);
	                     

				        $notif = Notifikasi::create($dnotif);
				        $a = SendNotif::sendNotifWa($sel_user->no_hp,$pesanWa);
				        $sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pesanan Baru', $notif->judul_id);
					    

						$success = 1;
						$msg = "Berhasil Simpan Transaksi";
						$data =  response()->json($sendData);
						
					}else if($req['metode_pembayaran'] == "3"){
					
						$waktu_skrang = Carbon::parse($req_transaksi['waktu_kirim']);
						$batas_bayar = $waktu_skrang->addHours(6);
						

						$req_transaksi['waktu_kirim'] = $batas_bayar;

						$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
						$itemForEmail = "";
						$selitemForEmail = Transaksi::findOrFail($ins_transaksi->id);
						$dataItemForEmail = $selitemForEmail->ItemTransaksi()->get();
						$noItemForEmail = 1;
					
						// $order = [];
						foreach ( $dataItemForEmail as $key) {
							$itemForEmail .= "<tr>
											  	<td align='center'>".$noItemForEmail."</td>
											  	<td>".$key->Item->nama_item."</td>
											  	<td align='center'>".$key->jumlah." PCS</td>
											  	<td align='right'>Rp. ".number_format($key->harga,'0','','.')."</td>
											  	<td align='right'>Rp. ".number_format($key->total,'0','','.')."</td>
											  </tr>";
							$noItemForEmail++;

							
						}

						
						$itemForEmail .= "<tr>
											 <td align='center'>#</td>
											 <td colspan='3'>Ongkir </td>
											 <td align='right'>Rp. ".number_format($ins_transaksi->total_biaya_pengiriman,'0','','.')."</td>
										  </tr>
										  <tr>
											 <td align='center'>#</td>
											 <td colspan='3'>Total Bayar </td>
											 <td align='right'>Rp. ".number_format($ins_transaksi->total_bayar,'0','','.')."</td>
										  </tr>";

						//setNotifExpired
						$kirim_notif = date('Y-m-d H:i:s',strtotime($ins_transaksi['waktu_kirim']) - 1200);
						$dataNE = ['transaksi_id' => $ins_transaksi->id, 
								   'email' => $sel_user->no_hp,
								   'waktu_kirim' => $kirim_notif ,
								   'item' => $itemForEmail,
								   'status' => '0'];
						$insDataNE = NotifExpired::create($dataNE);

						// kirim email
						$email_body = "<div style='padding:10px;'>
										<div>
											Anda Baru Saja Melakukan Pemesanan Di Agogobakery.com <br/>
											Dengan No Transaksi <b class='fg-red'>".$ins_transaksi->no_transaksi."</b> Dan List Pemesanan Sebagai Berikut
									    </div>

								        <table class='blueTable' style='margin-top:10px; margin-bottom:10px;'>
											<thead>
												<th style='width:10px;'>No</th>
												<th>Item</th>
												<th>Jumlah</th>
												<th>Harga</th>
												<th>Total</th>
											</thead>
											<tbody>
												".$itemForEmail."
											</tbody>
										</table>
									
										<div syle='margin-top:10px; '>
											Batas Pengambilan Pesanan Sampai : 
											<h2 style='margin-top:3px; margin-bottom:3px;'>".$ins_transaksi->waktu_kirim->format('d/m/Y h:i A')."</h2>
											<i style='font-size:12px;'>*) Pesanan Akan Dibatalkan Apabila Sampai Dengan Batas Waktu Yang Telah Ditentukan Anda Belum Mengambil Pesanan </i>
										</div>
									
										<hr />
									   </div>
								      ";
					    $email = $sel_user->email;
						$data = ['name' => $sel_user->name,
					             'email_body' => $email_body
					            ];
						$subject = "Pesanan Agogobakery.com";

						// $a = SendNotif::kirimEmail($email,$data,$subject);

					    $pesanWa = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi " .$ins_transaksi->no_transaksi." \nDengan Metode Pembayaran Bayar Di Toko . Batas Waktu Pengambilan Pesanan ".$ins_transaksi->waktu_kirim->format('d/m/Y h:i A');

						// notif android
					    $dnotif =[
				                'pengirim_id' => '1',
				                'penerima_id' => $req['user_id'],
				                'judul_id' => $ins_transaksi->id,
				                'judul' => 'Pesanan No '.$ins_transaksi->no_transaksi,
				                'isi' => 'Anda Telah Melakukan Pesanan Dengan Nomor Transaksi '.$ins_transaksi->no_transaksi.' Dengan Metode Pembayaran Bayar Di Toko . Batas Waktu Pengambilan Pesanan '.$ins_transaksi->waktu_kirim->format('d/m/Y h:i A'),
				                'jenis_notif' => 1,
				                'dibaca' => '0'
				                ];
				        $a = SendNotif::sendNotifWa($sel_user->no_hp,$pesanWa);        	
				        $notif = Notifikasi::create($dnotif);
				        $sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pesanan Baru', $notif->judul_id);
					    

						$success = 1;
						$msg = "Berhasil Simpan Transaksi";
						$data = "";
					}else if($req['metode_pembayaran'] == "4"){
						
						$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
						// $order = [];
						

						//setNotifExpired
						

						// kirim email
						

					    $pesanWa = "Anda Telah Melakukan Pesanan Dengan Nomor Transaksi " .$ins_transaksi->no_transaksi." \nDengan Metode Pembayaran COD ";

						// notif android
					    $dnotif =[
				                'pengirim_id' => '1',
				                'penerima_id' => $req['user_id'],
				                'judul_id' => $ins_transaksi->id,
				                'judul' => 'Pesanan No '.$ins_transaksi->no_transaksi,
				                'isi' => 'Anda Telah Melakukan Pesanan Dengan Nomor Transaksi '.$ins_transaksi->no_transaksi.' Dengan Metode Pembayaran COD ',
				                'jenis_notif' => 1,
				                'dibaca' => '0'
				                ];
				        $a = SendNotif::sendNotifWa($sel_user->no_hp,$pesanWa);        	
				        $notif = Notifikasi::create($dnotif);
				        $sendNotAndroid = SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'Pesanan Baru', $notif->judul_id);
					    

						$success = 1;
						$msg = "Berhasil Simpan Transaksi";
						$data = "";
					}
				}else{
					$success = 2;
					$msg = $msg;
					$data = $req;
				}
				
		    	if($success == "1"){
	            	$admin = User::whereIn('level_id',['2','7'])->where('status_aktif','1')->get();

		        	foreach($admin as $key){
		        		SendNotif::SendNotifPus($sel_user->id,$sel_user->name,$key->id,$ins_transaksi->id,$sel_user->name.' Baru Saja Melakukan Transaksi','1');
		        	}

		        	//loadJumNot
		        	SendNotif::SendNotPesan('5',['jenisNotif' => '1']);
		        	
		        	// Kirim Notif Ke Web User
        			SendNotif::SendNotPesan('1','',[$req['user_id']]);
        			
		        	if($req['metode_pembayaran'] == "2"){
		        		SendNotif::SendNotPesan('5',['jenisNotif' => '4']);
		        	}
		        	
	            	$transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
	            	// if($transaksi_berlangsung == '3'){
	            	// 	$sel_user->DetailKonsumen()->update(['kunci_transaksi' => '1']);
	            	// }
		    	}
		    }
		}else{
			$success = '0';
			$msg = 'Maaf! Maksimal Pesanan Sebanyak 3, Silahkan Selesaikan Terlebih Dahulu Pesanan Yang Sedang Berlangsung';
			$data = '';
		}


	    return response()->json(['success' => $success,'msg' => $msg, 'data' => $data],200);

    }
    
    public function GetOtp(Request $request)
    {
    	$req = $request->all();
        $rules = ['user_id' => 'required'];
        $messsages = ['user_id.required' => 'user_id Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{
        	$cek = Otp::where('user_id', $req['user_id'])->first();
	        $angka =  date('s') . $req['user_id'];
	        $otp =  Acak::otp($angka);
	        if ($cek) {
	            $angka =  date('s') . $req['user_id'];
	            $otp =  Acak::otp($angka);
	            $data['otp'] = $otp;
	            $cek->update($data);
	            $id = $cek->id;
	            $no_hp = $cek->User->no_hp;
	        } else {
	            $data['otp'] = $otp;
	            $data['user_id'] = $req['user_id'];
	            $i = Otp::create($data);
	            $id = $i->id;
	            $no_hp = $i->User->no_hp;
	        }

	        $msg_wa = 'Agogo Bakery Kode OTP : ' . $otp . ' .Kode OTP Bersifat Rahasia dan Jangan Beritahu Siapapun !!';
	        $response = SendNotif::sendNotifWa($no_hp, $msg_wa);
	        // $r = array(json_decode($response, true));
	      	
        	$success = 1;
          	$msg = $otp;
          	$kr = 200;
        }
        return response()->json(['success' => $success,'msg' => $msg], $kr);
    }

    public function ListTransaksi(Request $request)
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
        	 
        	 $list_transaksi = Transaksi::where('user_id','=',$req['user_id'])
        	 							  ->selectRaw("id,user_id,no_transaksi,banyak_item,total_bayar,metode_pembayaran,status,created_at,updated_at,detail_alamat")
        	 							//   ->where('status','!=','3')
        	 							//   ->where('waktu_kirim','>', Carbon::now()->format('Y-m-d H:i:s'))
        	  							  ->orderBy('transaksi.id','DESC')
										  ->limit($dataPerpage)
										  ->offset($offset)->get();

			 $list_transaksi->map(function($list_transaksi){
			 	$items = ItemTransaksi::join('item','item.id','=','item_transaksi.item_id')
			 							->selectRaw("item.id,
			 										 item.nama_item,
			 										 item_transaksi.jumlah,
			 										 item_transaksi.harga,
			 										 item_transaksi.total,
			 										 (Select gambar from gambar_item where item_id = item.id and utama = '1' ) as gambar_utama")
			 							->where('transaksi_id',$list_transaksi->id)->get();
			 	$list_transaksi['items'] = $items;
			 });	

	         $jumdat = Transaksi::where('user_id','=',$req['user_id'])
	         					//  ->where('status','!=','3')
        	 					//  ->where('waktu_kirim','>', Carbon::now()->format('Y-m-d H:i:s'))
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
	         $msg = $list_transaksi;
	         $kr = 200;
        }
        
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg, 'jumHal' => $jumHal], $kr);
    }

    public function ListPenggunaanSaldo(Request $request)
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

             $transaksi = Transaksi::where('status','5')
									->where('metode_pembayaran','1')
									->orWhere('top_up','1')
             						->where('user_id',$req['user_id'])
             						->selectRaw("id,user_id,no_transaksi,banyak_item,total_bayar,metode_pembayaran,status,created_at as waktu,top_up,created_at")
         						    ->orderBy('transaksi.id','DESC')
									->limit($dataPerpage)
									->offset($offset)->get();
            
             $transaksi->map(function($transaksi){
			 	$items = ItemTransaksi::join('item','item.id','=','item_transaksi.item_id')
			 							->selectRaw("item.id,
			 										 item.nama_item,
			 										 item_transaksi.jumlah,
			 										 item_transaksi.harga,
			 										 item_transaksi.total,
			 										 (Select gambar from gambar_item where item_id = item.id and utama = '1' ) as gambar_utama")
			 							->where('transaksi_id',$transaksi->id)->get();
			 	$transaksi['items'] = $items;
			 });

            $jumdat =  Transaksi::where('status','5')
									 ->where('metode_pembayaran','1')
									 ->orWhere('top_up','1')
             						->where('user_id',$req['user_id'])
	         					    ->count();

	         // $HistoriTopup = HistoriTopup::where('user_id',$req['user_id'])->get();
	         // return $HistoriTopup;
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
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg, 'jumHal' => $jumHal ], $kr);

    }

    public function DetailTransaksi(Request $request)
    {
    	$req = $request->all();
        $rules = ['transaksi_id' => 'required'];
        $messsages = ['transaksi_id.required' => 'transaksi_id Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{
        	$transaksi = Transaksi::select("id",
							        	  "user_id",
							        	  "no_transaksi",
							        	  "banyak_item",
							        	  "total_transaksi",
							        	  "jarak_tempuh",
							        	  "total_biaya_pengiriman",
							        	  "opsi_bt",
							        	  "biaya_admin",
							        	  "total_bayar",
							        	  "alamat_lain",
							        	  "lat",
							        	  "long",
							        	  "detail_alamat",
							        	  "durasi_kirim",
							        	  "waktu_kirim",
							        	  "metode_pembayaran",
							        	  "status",
							        	  "created_at",
							        	  "updated_at")
        							->where('id','=',$req['transaksi_id'])
        							->first();
        	$selno_hp = User::where('id',$transaksi->user_id)->first();
        	$transaksi['no_hp'] = $selno_hp->no_hp;
        	$transaksi['nama'] = $selno_hp->name;

        	$selItem = ItemTransaksi::join('item','item.id','=','item_transaksi.item_id')
        							  ->where('item_transaksi.transaksi_id','=',$transaksi->id)
        							  ->selectRaw("item_transaksi.*,item.nama_item,
        							  			  (Select gambar from gambar_item where item_id = item_transaksi.item_id and utama = '1' ) as gambar_utama ")
        							  ->get();
        	
        	$transaksi['item_transaksi'] = $selItem;
        	if($transaksi->metode_pembayaran != "3"){
        		
        		if( $transaksi->status >= '2' && $transaksi->status != '3' && $transaksi->status != '6' && $transaksi->status != '4'){
        			$kurir = $transaksi->Pengiriman->Kurir;
        			// $transaksi['pengiriman'] = $kurir;
        			$transaksi['pengiriman']['kurir']['nama'] = $kurir->User->name;
        			$transaksi['pengiriman']['kurir']['no_hp'] = $kurir->User->no_hp;
        		}else{
        			$transaksi['pengiriman'] = "";
        		}
        		
        	}else{
        		$transaksi->AmbilPesanan;	
        	}

        	if(isset($transaksi->AjukanBatalPesanan->id) && $transaksi->status == '3' ){
        		$transaksi['status_pembatalan'] = "1";
        	}else{
        		$transaksi['status_pembatalan'] = "0";
        	}

        	$transaksi->BatalPesanan;
        	
        	$success = 1;
          	$msg = $transaksi;
          	$kr = 200;
        }
        return response()->json(['success' => $success,'msg' => $msg], $kr);
    }

    public function SimpanTransaksi($req_transaksi,$itemTransaksi)
    {

    	$forCode = Carbon::now()->format('Ymd');
    	$maxKD = Transaksi::where('no_transaksi','LIKE','T'.$forCode.'%')->orderBy('id','DESC')->first();
    	if(!empty($maxKD->id)){
    		$nexKD = Acak::AmbilId($maxKD['no_transaksi'],'T'.$forCode,9,3);	
    	}else{
    		$nexKD = 'T'.$forCode.'001';
    	}
       
        $req_transaksi['no_transaksi'] = $nexKD;

        // if(isset($req_transaksi['durasi_kirim'])){
        // 	if($req_transaksi['metode_pembayaran'] != '3'){
        // 		if($req_transaksi['durasi_kirim'] == 0){
		      //   	$waktu_kirim = Carbon::now();
		      //   }else{
		      //   	$waktu_kirim = Carbon::now()->addMinutes($req_transaksi['durasi_kirim']);
		      //   }
		      //   $req_transaksi['waktu_kirim'] = $waktu_kirim;
        // 	}
        // }

        $ins_transaksi = Transaksi::create($req_transaksi);
        $find = Transaksi::findOrFail($ins_transaksi->id);
        $ins_item = $find->ItemTransaksi()->createMany($itemTransaksi);

        return $ins_transaksi;
    }


    public function UpdateSaldo($user_id,$new_saldo)
    {
    	$sel_user = User::findOrFail($user_id);
		$update_saldo = $sel_user->DetailKonsumen()->update(['saldo' => $new_saldo]);
    }

    // public function UpdateStock($itemTransaksi)
    // {
    	
    // 	foreach ($itemTransaksi as $key ) {
    // 		$find = Item::findOrFail($key['item_id']);
    // 		$newStock = $find->stock - $key['jumlah'];
    // 		$update = $find->update(['stock' => $newStock]);

    // 		DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['jumlah']);
                
    //         DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['jumlah']);
                
    //         DB::table('produksi')->where('item_id', $key['item_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['jumlah']);

    // 	}
    // }

    public function GetOngkir()
    {
    	$ongkir = Ongkir::first();
    	if(is_null($ongkir)){
    		$success = 0;
    		$response = "Biaya Ongkir Belum Di Set";
    	}else{
    		$success = 1;
    		$response = ['biaya_ongkir' => $ongkir->biaya_ongkir];
    	}

    	return response()->json(['success' => $success, 'msg' => $response], 200);
    }
    	
    //Tanda Tanya
    public function AjukanBatalPesanan(Request $request)
    {
    	$req = $request->all();
    	$rules = ['transaksi_id' => 'required', 'nama_user' => 'required' ];
        $messsages = ['transaksi_id.required' => 'transaksi_id Tidak Bisa Kosong', 'nama_user.required' => 'nama_user Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{
        	$find = Transaksi::findOrFail($req['transaksi_id']);
        	if($find->status == "1" || $find->status == "6"){
        		$find->Update(['status' => "4"]);
        		$find->AjukanBatalPesanan()->create(['diajukan_oleh' => $req['nama_user'], 'status' => '0' ]);

	        	// ($pengirim_id,$pengirim_nama,$penerima_id,$judul_id,$judul,$jenis_notif)

	        	$admin = User::whereIn('level_id',['2','7'])->where('status_aktif','1')->get();
	        	
	        	foreach($admin as $key){
	        		SendNotif::SendNotifPus($find->user_id,$req['nama_user'],$key->id,$req['transaksi_id'],$req['nama_user'].' Mengajukan Untuk Pembatalan Pesanan','7');
	        	}
	        	SendNotif::SendNotPesan('5',['jenisNotif' => '3']);

	        	$success = 1;
	          	$msg = "Berhasil Ajukan Pembatalan Transaksi";
	          	$kr = 200;
        	}else{
        		$success = 0;
        		$msg = "Maaf! Pengajuan Pembatalan Tidak Bisa Dilakukan";
        		$kr = 200;
        	}
        	
        }
        return response()->json(['success' => $success, 'msg' => $msg],$kr);
    }
    
}
