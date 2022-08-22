<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;
use App\LogKonfirBayar;
use App\Transaksi;
use App\User;
use App\Notifikasi;
use Carbon\Carbon;
use App\Helpers\SendNotif;
use App\NotifExpired;

class TripayController extends Controller
{
	
	public $api_key;
	public $private_key;
	public $merchant_code;
	public $merchant_ref;

	public function __construct()
	{
		$this->api_key = 'Bearer 5Ta6VblpbNbzLuFajpLx9J8U1gPOt79EReAW8PXe';
		$this->private_key = 'nPVBJ-Jf2CT-sXDFb-b9sjv-Zrvtn';
		$this->merchant_code ='T1977';
		$this->merchant_ref = '';
	}

    public function paymentChannel(Request $Request)
    {
    	$response = Curl::to('http://agogobakery.com/api/payment_channel')
                        ->asJson()
						->get();
    	return response()->json($response);

	}
	
	public function kalkulator(Request $Request)
    {
		$req = $Request->all();
    	$response = Curl::to('http://agogobakery.com/api/kalkulator')
						->withData( array( 'amount' => $req['amount'], 'code' => $req['code']) )
                        ->asJson()
						->get();
    	return response()->json($response);
    }


    public function Callback(Request $Request)
    {
    	$req = $Request->all();
    	
    	$json = file_get_contents("php://input");

		// ambil callback signature
		$callbackSignature = isset($_SERVER['HTTP_X_CALLBACK_SIGNATURE']) ? $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] : '';

		// generate signature untuk dicocokkan dengan X-Callback-Signature
		$signature = hash_hmac('sha256', $json, $this->private_key);

		// validasi signature
		if( $callbackSignature !== $signature ) {
		    exit("Invalid Signature"); // signature tidak valid, hentikan proses
		}

		$data = json_decode($json);
		$event = $_SERVER['HTTP_X_CALLBACK_EVENT'];

		if( $event == 'payment_status' )
		{
		    if( $data->status == 'PAID' )
		    {
		    	
		    	$select = Transaksi::where('no_transaksi',$data->merchant_ref)->first();
		    	$find = Transaksi::findOrfail($select->id);
		    	$find->update(['status' => '1', 'tgl_bayar' => Carbon::now()->format('Y-m-d H:i:s') ]);
				NotifExpired::where('transaksi_id',$select->id)->update(['status' => '1']);
		    	$create_log = LogKonfirBayar::create(['transaksi_id' => $find->id,'input_by' => 'Tripay' ]);

		    	 SendNotif::SendNotPesan('5',['jenisNotif' => '4']);
			     $dnotif =
			        [
			            'pengirim_id' => 1,
			            'penerima_id' => $find->user_id,
			            'judul_id' => $find->id,
			            'judul' => 'Konfirmasi Pembayaran No. Transaksi '.$find->no_transaksi,
			            'isi' => 'Terima Kasih Telah Melakukan Transfer Pembayaran Untuk Pesanan Nomor Transaksi '.$find->no_transaksi.' Pesanan Anda Akan Kami Proses Untuk Pengantaran Ke Rumah Anda',
			            'jenis_notif' => 7,
			            'dibaca' => '0'
			        ];
			    
			        $notif = Notifikasi::create($dnotif);
			        $userWa = User::findOrfail($find->user_id);
			        SendNotif::sendNotifWa($userWa->no_hp,$notif->isi);
			         //NotifGCM
			        SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

			        $success = true;
		    }else{
		    	$success = false;
		    }
		}

		return response()->json(['success' => $success]);

    }
    public function Transaksi(Request $Request)
    {
    	$privateKey = $this->private_key;
		$merchantCode = $this->merchant_code;
		$merchantRef = 'INV55567';
		$amount = 1500000;

		$signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
		return $signature;

    }
}
