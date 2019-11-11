<?php
namespace App\Helpers;
use App\RulesNilai;
/**
* 
*/
class accept_disposisi
{
	// Disposisi
	public static function accept(){
		$count = RulesNilai::where([['jenis', '1'],['nilai', '>', 0],])->count();
		$nilai = 100 * 2;
		return $hasil = $nilai/$count;
	}

	public static function finish(){
		$count = RulesNilai::where([['jenis', '1'],['nilai', '>', 0],])->count();
		$nilai = RulesNilai::where([['jenis', '1'],['nilai', '>', 0],])->sum('nilai');
		return $hasil = $nilai/$count;
	}

	// Agenda
	public static function hadir(){
		$count = RulesNilai::where([['jenis', '0'],['nilai', '>', 0],])->count();
		$nilai = RulesNilai::where([['jenis', '0'],['nilai', '>', 0],])->sum('nilai');
		return $hasil = $nilai/$count;
	}

	// Inovasi
	public static function inovasi(){
		$count = RulesNilai::where([['jenis', '2'],['nilai', '>', 0],])->count();
		$nilai = RulesNilai::where([['jenis', '2'],['nilai', '>', 0],])->sum('nilai');
		return $hasil = $nilai/$count;
	}
}