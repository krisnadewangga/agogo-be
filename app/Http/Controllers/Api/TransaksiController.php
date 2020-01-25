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
use App\Helpers\Acak;
use App\Helpers\SendNotif;
use Validator;



class TransaksiController extends Controller
{
    public function Store(Request $request)
    {
    	$req = $request->all();
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
											Rule::in(['1', '2', '3'])],
					'banyak_item' => 'required',
					'waktu_kirim' => 'required'
		         ];
	
		$sel_user = User::findOrFail($req['user_id']);
		if($sel_user->DetailKonsumen->kunci_transaksi == 0){
			$itemTransaksi = [];
			$countItemError = 0;
			$ItemError = [];
			for($i=1; $i<=$req['banyak_item']; $i++){
				
				$rules['item_id'.$i] = 'required';
				$rules['jumlah'.$i] = 'required|numeric';
				$rules['harga'.$i] = 'required|numeric';
				$rules['margin'.$i] = 'required|numeric';
				
				$selItem = Item::findOrFail($req['item_id'.$i]);
				if($selItem->stock >= $req['jumlah'.$i]){
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
				}else{
					$countItemError += 1;	
					$ItemError[] = $selItem->nama_item;
				}
			}

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
				$msgItem = "Maaf ! Pesanan Untuk ".$tempMsgItem." Melebihi Jumlah Stock ";
			}
			
			$validator = Validator::make($req, $rules);
		    if($validator->fails()){
		        $success = 0;
		        $msg = $validator->messages()->all();
		        $response = $msg;
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

				
				$saldo = $sel_user->DetailKonsumen->saldo;
				$status_member = $sel_user->DetailKonsumen->status_member;
				
				if( ($req['metode_pembayaran'] == '1' || $req['metode_pembayaran'] == '2' )  && $status_member == '1'){
					if($req['metode_pembayaran'] == '1' && $countItemError == 0){
						if($saldo > $req['total_bayar'] ){
							$req_transaksi['tgl_bayar'] = Carbon::now();
							// return $req_transaksi;
							$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
							$min_stock_item = $this->UpdateStock($itemTransaksi);

							$new_saldo = $saldo - $req['total_bayar'];
							$this->UpdateSaldo($req['user_id'],$new_saldo);

							$success = 1;
							$msg = "Berhasil Simpan Transaksi";
						}else{
							$success = 0;
							$msg = "Saldo Anda Tidak Cukup";
						}
					}else if($req['metode_pembayaran'] == '2' && $countItemError == 0){
						$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
						$min_stock_item = $this->UpdateStock($itemTransaksi);
					
						$success = 1;
						$msg = "Berhasil Simpan Transaksi";
					}else{
						$success = 0;
						$msg = $msgItem;
					}
				}else if( ($req['metode_pembayaran'] == '1' || $req['metode_pembayaran'] == '2' ) && $status_member != "1"){
					$success = 0;
					$msg = "Maaf! Silahkan Daftarkan Akun Anda Menjadi Member";
				}else if($req['metode_pembayaran'] == '3' && ($status_member == "1" || $status_member == "0")){
					if($countItemError == 0){
						$ins_transaksi = $this->SimpanTransaksi($req_transaksi,$itemTransaksi);
						$min_stock_item = $this->UpdateStock($itemTransaksi);

						$success = 1;
						$msg = "Berhasil Simpan Transaksi";
					}else{
						$success = 0;
						$msg = $msgItem;
					}
				}
				
		    	if($success == "1"){
		    		$admin = User::where('level_id','2')->first();
	            	SendNotif::SendNotifPus($sel_user->id,$sel_user->name,$admin->id,$ins_transaksi->id,$sel_user->name.' Baru Saja Melakukan Transaksi','1');

	            	$transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
	            	if($transaksi_berlangsung == '3'){
	            		$sel_user->DetailKonsumen()->update(['kunci_transaksi' => '1']);
	            	}
		    	}
		    	

		    }
		}else{
			$success = '0';
			$msg = 'Maaf! Maksimal Pesanan Sebanyak 3, Silahkan Selesaikan Terlebih Dahulu Pesanan Yang Sedang Berlangsung';
		}
		

	    return response()->json(['success' => $success,'msg' => $msg],200);

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
        	 							  ->selectRaw("id,user_id,no_transaksi,banyak_item,total_bayar,metode_pembayaran,status,created_at,updated_at")
        	  							  ->orderBy('transaksi.id','DESC')
										  ->limit($dataPerpage)
										  ->offset($offset)->get();

	         $jumdat = Transaksi::where('user_id','=',$req['user_id'])->count();
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
        
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
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
        	$selItem = ItemTransaksi::join('item','item.id','=','item_transaksi.item_id')
        							  ->where('item_transaksi.transaksi_id','=',$transaksi->id)
        							  ->select('item_transaksi.*','item.nama_item')
        							  ->get();
        	
        	$transaksi['item_transaksi'] = $selItem;
        	

        	if($transaksi->metode_pembayaran != "3"){
        		
        		if( $transaksi->status >= '2' ){
        			$kurir = $transaksi->Pengiriman->Kurir;
        			$transaksi['pengiriman'] = $kurir;
        		}else{
        			$transaksi['pengiriman'] = "";
        		}
        		
        	}else{
        		$transaksi->AmbilPesanan;	
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
    	$maxKD = Transaksi::where('no_transaksi','LIKE','T'.date('Ymd').'%')->orderBy('id','DESC')->first();
        $nexKD = Acak::AmbilId($maxKD['no_transaksi'],'T'.date('Ymd'),9,3);
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

    public function UpdateStock($itemTransaksi)
    {
    	foreach ($itemTransaksi as $key ) {
    		$find = Item::findOrFail($key['item_id']);
    		$newStock = $find->stock - $key['jumlah'];
    		$update = $find->update(['stock' => $newStock]);
    	}
    }

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
    public function AjukanBatalTransaksi(Request $request)
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

        	$success = 1;
          	$msg = "Berhasil Ajukan Pembatalan Transaksi";
          	$kr = 200;
        }
        return response()->json(['success' => $success, 'msg' => $msg],$kr);
    }

}
