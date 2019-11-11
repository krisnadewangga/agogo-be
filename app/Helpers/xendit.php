<?php
namespace App\Helpers;
use Ixudra\Curl\Facades\Curl;

class xendit{

	public static function booking_bayar($external_id,$amount,$payer_email,$description)
    {   
       $data = ['external_id' => $external_id ,
                'amount' => $amount,
                'should_send_email'=> false,
                'payer_email' => $payer_email,
                'description' => $description
               ];
       $response = Curl::to('https://api.xendit.co/v2/invoices')
                        ->withData( $data )
                        ->withHeader('Authorization: Basic eG5kX2RldmVsb3BtZW50X09ZMkJmTDhoZ2JmOWs4SnVMT0VaU0RDV1p0NmtxTlIxazNMbVJ4bjlHUFg4TDJrQ1FCanc6')
                        ->asJson( true )
                        ->post();
       return $response;
    }
}