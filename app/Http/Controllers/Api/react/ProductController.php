<?php

namespace App\Http\Controllers\api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PathImageReact;
use App\Kategori;
use App\Item;
use App\Http\Resources\Item as ItemResource;

class ProductController extends Controller
{
	public $path_image;

	public function __construct(){

		$this->path_image = PathImageReact::getPath(400);;

	}


    public function categories()
    {
    	$categories = Kategori::where('status_aktif','1')->select('id','kategori as name')->get();
    	return response()->json($categories);
    }

    public function products()
    {
    	$item = Item::selectRaw("item.id,
    							 item.code,
    							 item.nama_item as name,
    							 item.stock,
    							 item.harga as price,
    							 item.kategori_id as category_id,
    							 (select kategori from kategori where id = item.kategori_id) as category_name
    							")

					->where([
						['item.status_aktif','=','1'],
						['item.stock','>','0']
					])
					->orderBy('item.kategori_id')
					->get();

		$path_image = $this->path_image;

		$item->map(function($item) use ($path_image) {
			$item['photo'] = $path_image.$item->GambarUtama;
			return $item;
		});

		return response()->json($item);
    }

    public function show($id){
    	$item = Item::findOrFail($id);

    	return response()->json($item);
    }
}
