<?php
namespace App\Helpers;
use Ixudra\Curl\Facades\Curl;

class tripay{

	public static function Signature($no_transaksi,$amount)
    {   
      //$privateKey = '4Su6Q-n0GAn-mO7ZY-DUdlj-LoCXr'; // developement mode
      
      $privateKey = 'nPVBJ-Jf2CT-sXDFb-b9sjv-Zrvtn'; //real prodution
      $merchantCode = 'T1977';
      $merchantRef = $no_transaksi;
      $amount = $amount;

      $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
      return $signature;
    }
}