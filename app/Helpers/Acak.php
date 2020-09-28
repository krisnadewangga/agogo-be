<?php
namespace App\Helpers;
class Acak {
	public static function Kaseputar($id = 20){
			$pool = '1234567890abcdefghijkmnpqrstuvwxyz';		
			$word = '';
			for ($i = 0; $i < $id; $i++){
				$word .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
			}
			return $word; 
	}

	public static function AmbilId($maxKD,$karakter,$angka_awal,$angka_akhir){
		
		$substr = (int) substr($maxKD, $angka_awal,$angka_akhir);
		$kode = $substr + 1;
		$newkode = $karakter.sprintf("%0".$angka_akhir."s", $kode);
		return $newkode;	
	}	

	public static function idKonfirItem($kode,$transaksi_id){
		$newkode = $transaksi_id.sprintf("%02s", $kode);
		return $newkode;	
	}

	public static function otp($angka){
		if(strlen($angka) == 1) {
			$angka = $angka. rand(0, 9) . rand(0, 9) . rand(0, 9);
		} else if(strlen($angka) == 2) {
			$angka = $angka. rand(0, 9) . rand(0, 9);
		} else if(strlen($angka) == 3) {
			$angka = $angka. rand(0, 9);
		}
		$newkode = str_shuffle($angka);
		return $newkode;
	}

}