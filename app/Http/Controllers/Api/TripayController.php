<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class TripayController extends Controller
{
	
	public $api_key;
	public $private_key;
	public $merchant_code;
	public $merchant_ref;

	public function __construct()
	{
		$this->api_key = 'Bearer 4synTlbXG2qsABvPRz7aT16aeq88fP4fhJKz3a1D';
		$this->private_key = 'HNrwk-XkBqq-cT3Ra-ozRI0-f0J0G';
		$this->merchant_code ='T1977';
		$this->merchant_ref = '';
	}

    public function paymentChannel(Request $Request)
    {
    	$response = Curl::to('https://payment.tripay.co.id/api/merchant/payment-channel')
                        ->withHeader('Authorization: '.$this->api_key)
                        ->asJson( true )
                        ->get();
    	return $response;
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
		    	$success = 'TRUE';
		    }else{
		    	$success = 'FALSE';
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
