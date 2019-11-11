<?php
namespace App\Helpers;
class Jarak {
	public static function getJarak($lat11,$long1,$lat2,$long2)
    {

      $dataJson = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$lat11.",".$long1."&destinations=".$lat2.",".$long2."&key=AIzaSyAoboaPEpg6YCrkbFTYCUb-Xl9y3o0ZDBs");

      $data = json_decode($dataJson,true);
      $nilaiJarak = $data['rows'][0]['elements'][0]['distance']['text'];
      $pisah = explode(" ",$nilaiJarak);

      if($pisah[0] < 1){
        $fix_jarak = 1;
      }else{
        $fix_jarak = $pisah[0];
      }
      $response = ['jarak' => round($fix_jarak), 'asal' => $data['origin_addresses'][0], 'tujuan' => $data['destination_addresses'][0] ];
      // return round($fix_jarak);
      return $response;
    }
	
}