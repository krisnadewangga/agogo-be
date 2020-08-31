<?php
namespace App\Helpers;
use App\Helpers\Acak;
use Intervention\Image\Facades\Image as Image;
use File;

class KompresFoto {

 // 	public static function UbahUkuran($image,$path_tujuan){
	// 	$angka = Acak::Kaseputar(20);
	// 	$filename  = date('YmdHis').'-'.$angka.'.' .$image->getClientOriginalExtension();
	// 	$path =("upload/images-100/$path_tujuan/" . $filename);
	// 	$path1 =("upload/images-400/$path_tujuan/" . $filename);
	// 	$path2 =("upload/images-700/$path_tujuan/" . $filename);
	// 	$path3 =("upload/images-1024/$path_tujuan/" . $filename);
	// 	Image::make($image->getRealPath())->resize(100, 100)->save($path);
	// 	Image::make($image->getRealPath())->resize(400, 400)->save($path1);
	// 	Image::make($image->getRealPath())->resize(700, 700)->save($path2);
	// 	Image::make($image->getRealPath())->resize(1024, 768)->save($path3);
	// 	return $path_tujuan."/".$filename;
	// }
	
	
	// public static function HapusFoto($path_tujuan){	
	// 	if(file_exists("upload/images-100/$path_tujuan")){
	// 		unlink("upload/images-100/$path_tujuan");
	// 	}

	// 	if(file_exists("upload/images-400/$path_tujuan")){
	// 		unlink("upload/images-400/$path_tujuan");
	// 	}
		
	// 	if(file_exists("upload/images-700/$path_tujuan")){
	// 		unlink("upload/images-700/$path_tujuan");
	// 	}

	// 	if(file_exists("upload/images-1024/$path_tujuan")){
	// 		unlink("upload/images-1024/$path_tujuan");
	// 	}
	// }

	public static function HapusFoto($path_tujuan){	
		// if(file_exists("upload/images-100/$path_tujuan")){
		// 	unlink("upload/images-100/$path_tujuan");
		// }

		// if(file_exists("upload/images-400/$path_tujuan")){
		// 	unlink("upload/images-400/$path_tujuan");
		// }
		
		// if(file_exists("upload/images-700/$path_tujuan")){
		// 	unlink("upload/images-700/$path_tujuan");
		// }

		// if(file_exists("upload/images-1024/$path_tujuan")){
		// 	unlink("upload/images-1024/$path_tujuan");
		// }

			
		if(file_exists($path_tujuan)){
			unlink($path_tujuan);
		}

	}

	public static function Upload($image,$folder)
	{
		$destinationPath = 'upload/'.$folder.'/'; // upload path
        $angka = Acak::Kaseputar(20);
        $filename  = date('YmdHis').'-'.$angka.'.' .$image->getClientOriginalExtension();
        $image->move($destinationPath, $filename);
        return $destinationPath.$filename;
	}

}