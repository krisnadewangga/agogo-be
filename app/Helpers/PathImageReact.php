<?php
namespace App\Helpers;

class PathImageReact {

	public static function getPath($resolusi)
	{

		 $path_image = "http://192.168.1.30/agogo/upload/images-".$resolusi."/";

		 //$path_image = "http://agogobakery.com/atur/upload/images-".$resolusi."/";


		 ///$path_image = "http://agogobakery.com/atur/upload/";
		 // $path_image = "http://127.0.0.1/agogo/upload/";
		//$path_image = "http://agogobakery.com/upload/images-".$resolusi."/";

		return $path_image;
	}
}