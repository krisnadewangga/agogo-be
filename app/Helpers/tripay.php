<?php
namespace App\Helpers;
use Ixudra\Curl\Facades\Curl;

class tripay{

	public static function Signature($no_transaksi,$amount)
    {   
      $privateKey = 'HNrwk-XkBqq-cT3Ra-ozRI0-f0J0G';
      $merchantCode = 'T1977';
      $merchantRef = $no_transaksi;
      $amount = $amount;

      $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
      return $signature;
    }
}