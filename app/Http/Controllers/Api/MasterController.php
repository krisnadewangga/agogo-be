<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Kategori;
use App\Item;
use Validator;

class MasterController extends Controller
{
    public function ListKategori()
    {
  	   $Kategori = Kategori::where('status_aktif','1')->orderBy('kategori','ASC')->get();
  	   $success = 1;
  	   return response()->json(['success' => $success, 'msg' => $Kategori], 200);
	}  
	
	public function ListItemAll(Request $request)
	{
	 	$req = $request->all();
        $messsages = ['dataPerpage.required' => 'dataPerpage Tidak Bisa Kosong',
                      'page.required' => 'page Tidak Bisa Kosong'];
        $rules = ['page' => 'required', 'dataPerpage' => 'required'];

        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
              $success = 0;
              $msg = $validator->messages()->all();
              $kr = 400;
              $pageSaatIni = 0;
              $tampilPS = 0;
         }else{
         	$page = $req['page'];
            $dataPerpage = $req['dataPerpage'];
            $offset = ($page - 1) * $dataPerpage;

			$item = Item::selectRaw("item.id,
		    						 item.kategori_id,
		    						 item.nama_item,
		    						 item.harga,
		    						 item.margin,
		    						 item.stock,
		    						 item.v_rasa, 
		    						 (select gambar from gambar_item where item_id = item.id and utama = '1') as gambar_utama")
					->where([
						['item.status_aktif','=','1'],
						['item.stock','>','0']
					])
					->orderBy('item.kategori_id')
					->limit($dataPerpage)
					->offset($offset)->get();

			$jumdat = Item::where([
									['item.status_aktif','=','1'],
									['item.stock','>','0']
								 ])
                          ->count();

          $jumHal = ceil($jumdat / $dataPerpage);
          $pageSaatIni = (int) $page;
          $pageSelanjutnya = $page+1;
          if( ($pageSaatIni == $jumHal) || ($jumHal == 0) ){
             $tampilPS = 0;
          }else{
             $tampilPS = $pageSelanjutnya;
          }

          $success = 1;
          $msg = $item;
          $kr = 200;

		}
  	   	return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
	}

	public function ListItemPerKat(Request $request)
	{
		$req = $request->all();
        $messsages = ['dataPerpage.required' => 'dataPerpage Tidak Bisa Kosong',
                      'page.required' => 'page Tidak Bisa Kosong', 
                      'kategori_id.required' => 'kategori_id Tidak Bisa Kosong' ];
        $rules = ['page' => 'required', 
        		  'dataPerpage' => 'required', 
        		  'kategori_id' => 'required'];

        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
            $pageSaatIni = 0;
            $tampilPS = 0;
        }else{
        	$page = $req['page'];
            $dataPerpage = $req['dataPerpage'];
            $offset = ($page - 1) * $dataPerpage;

			$item = Item::selectRaw("item.id,
		    						 item.kategori_id,
		    						 item.nama_item,
		    						 item.harga,
		    						 item.margin,
		    						 item.stock,
		    						 item.v_rasa, 
		    						 (select gambar from gambar_item where item_id = item.id and utama = '1') as gambar_utama")
					->where([
						['item.status_aktif','=','1'],
						['item.stock','>','0'],
						['kategori_id','=', $req['kategori_id'] ]
					])
					->orderBy('item.nama_item','ASC')
					->limit($dataPerpage)
					->offset($offset)->get();

			$jumdat = Item::where([
									['item.status_aktif','=','1'],
									['item.stock','>','0'],
									['kategori_id','=', $req['kategori_id'] ]
								 ])
                          ->count();

          $jumHal = ceil($jumdat / $dataPerpage);
          $pageSaatIni = (int) $page;
          $pageSelanjutnya = $page+1;
          if( ($pageSaatIni == $jumHal) || ($jumHal == 0) ){
             $tampilPS = 0;
          }else{
             $tampilPS = $pageSelanjutnya;
          }

          $success = 1;
          $msg = $item;
          $kr = 200;
        }
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'msg' => $msg], $kr);
	}

	public function DetailItem(Request $request)
	{
		$req = $request->all();
        $rules = ['item_id' => 'required'];
        $messsages = ['item_id.required' => 'item_id Tidak Bisa Kosong' ];
       
        $validator = Validator::make($req, $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
        }else{

        	$item = Item::selectRaw(" item.*,
        							  (select gambar from gambar_item 
        							  where item_id = item.id and utama = '1') as gambar_utama"
        							)
        				 ->where('item.id',$req['item_id'])
        				 ->first();

        	$item['gambar_item'] = $item->GambarItem()->get();
        	
        	$success = 1;
          	$msg = $item;
          	$kr = 200;

        }
        return response()->json(['success' => $success,'msg' => $msg], $kr);

	}

	
}
