<?php
namespace App\Helpers;

class PathImageReact {

	public static function getPath($resolusi)
	{
		$path_image = "http://localhost/test/agogo/upload/images-".$resolusi."/";
		return $path_image;
	}
}