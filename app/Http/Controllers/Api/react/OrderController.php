<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaksi;
use App\ItemTransaksi;
use App\R_Order;
use App\User;
use App\Item;
use App\Notifikasi;
use App\Helpers\SendNotif;
use App\Helpers\Acak;
use App\Role;
use App\Aproval;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Refund;
use Carbon\Carbon;

class OrderController extends Controller
{
	public function generateInvoice($opsi)
	{

    $forCode = Carbon::now()->format('ymd');
    
    $maxKD = Transaksi::where('no_transaksi','LIKE','TK-'.$forCode.'%')->orderBy('id','DESC')->first();
    if(!empty($maxKD->id)){
        $nexKD = Acak::AmbilId($maxKD['no_transaksi'],'TK-'.$forCode,9,3);  
    }else{
        $nexKD = 'TK-'.$forCode.'001';
    }
    return $nexKD;
  //   $req_transaksi['no_transaksi'] = $nexKD;

		// if($opsi == "1"){
		// 	$string_awal = "TK-";
		// 	$select = Transaksi::where('jalur','2')
  //                         ->where('for_ps','0')
  //                         ->orderBy('id','DESC');
		// }

		// if($select->count() > 0){
		// 	$first = $select->first();
		// 	$explode = explode('-',$first->no_transaksi);
		// 	$nextCode = $explode[1] + 1;
		// 	$invoice = $string_awal.$nextCode;
		// }else{
		// 	$invoice = $string_awal."1";
		// }

		
	}

 public function generateInvoiceRefunds()
 {
  $refund = Refund::orderBy('id', 'DESC');
  if ($refund->count() > 0) {
    $refund = $refund->first();
    $explode = explode('-', $refund->invoice);
    $count = $explode[1] + 1;
    return 'RF-' . $count;
  }
  return 'RF-1';
}







public function checkLastInvoice()
{
 $res = $this->generateInvoice('1');
 return response()->json(['current_invoice' => $res],200);
}


    public function Coba()
    {
      $productid = 7;
      $qty = 1;
      $kategori_id = 2;

      // DB::table('item')->where('id', $productid)->decrement('stock', $qty);
      $aa = DB::table('item')->where('kategori_id',$kategori_id)->orderBy('id','DESC')->take(1)->get();
      return $aa;            
    }



public function postOrder(Request $request)
{


  $no_transaksi = $this->generateInvoice('1');

  $req = $request->all();

  if(!empty($request[0]['invoice']) ){            
    $no_transaksi = $request[0]['invoice'] ;
  }
  else {
    $no_transaksi = $this->generateInvoice('1');
  }

  $req_transaksi = ['user_id' => $req[0]['user_id'],
                    'no_transaksi' => $no_transaksi,
                    'total_transaksi' => $req[0]['subtotal'],
                    'total_bayar' => $req[0]['total'],
                    'status' => '5',
                    'jalur' => '2',
                    'biaya_pengiriman' => '0',
                    'biaya_pengiriman' => '0',
                    'jarak_tempuh' => '0',
                    'total_biaya_pengiriman' => '0',
                    'banyak_item' => count($req),
                    'lat' => '-',
                    'long' => '-',
                    'detail_alamat' => '-',
                    'kasir_id' => $req[0]['user_id'],
                    'metode_pembayaran' => '3',
                    'tgl_bayar' => date("Y-m-d H:i:s"),
                    'waktu_kirim' => date("Y-m-d H:i:s"),
                    'tax' => $req[0]['tax'],
                    'for_ps' => '0'
  ];

  if(!empty( $request[0]['id']) ){ 
    $req_transaksi['id'] = $request[0]['id'];
  }


  $sel = Transaksi::where([['no_transaksi', '=', $req[0]['r_code']],
                            ['user_id' ,'=', $req[0]['user_id'] ]
                          ])->orderBy('id','desc')->first();


  if(!isset($sel)){

   $insertTransaksi = Transaksi::create($req_transaksi);

  $insItem = [];  
  foreach ($req as $key) {
    $array = [];
    // $array['transaksi_id'] =  $insertTransaksi->id;
    $findItem = Item::findOrFail($key['product_id']);
    $array['transaksi_id'] = $insertTransaksi->id;
    $array['item_id'] = $key['product_id'];
    $array['jumlah'] = $key['qty'];
    $array['harga'] = $findItem['harga'];
    $array['margin'] = 0;
    $array['total'] = $key['qty'] * $findItem['harga'];
    $array['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    $array['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

    $insItem[] = $array;

    $getCount = Item::where(['id' => $key['product_id']])->get();
    // if ($getCount[0]['stock'] >= $key['qty']) {
      


        // $find->ItemTransaksi()->createMany($insItem);
        //$insert = ItemTransaksi::insert($insItem);

        DB::table('item')->where('id', $key['product_id'])->decrement('stock', $key['qty']);   
        DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['qty']);
        DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['qty']);
       // DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['qty']);

    // }else {
    //     throw new \Exception('Stock ' . $getCount[0]['nama_item'] . ' Tidak Mencukupi');
    // }

  }

  
  $find = Transaksi::findOrFail($insertTransaksi->id);
  $find->ItemTransaksi()->createMany($insItem);
  $find->R_Order()->create(['transaksi_id' => $insertTransaksi->id, 
   'uang_dibayar' => $req[0]['dibayar'],
   'uang_kembali' => $req[0]['kembali'],
   'status' => $req[0]['status'] ]);

  if($insertTransaksi){
    return response()->json([
     'status' => 'success',
     'message' => $no_transaksi,
    ], 200);
  }else{
     return response()->json([
     'status' => 'failed',
     'message' => 'terjadi kesalahan, silahkan refresh dan input transaksi lagi',
    ], 500);
  }

  }else{

    return response()->json([
     'status' => 'failed',
     'message' => 'error',
    ], 200);
  }

  
}

public function bayarTransaksiM(Request $request)
{
    $req = $request->all();
    $no_transaksi = $req[0]['no_transaksi'];
    $total = $req[0]['total'];

    $kasir_id = $req[0]['user_id'];
    $sel_kasir = User::findOrFail($kasir_id);
    $nama_kasir = $sel_kasir->name;

    $sel = Transaksi::where('no_transaksi',$no_transaksi)->first();
    if($sel->total_bayar != $total){
      $sel->update(['total_transaksi' => $total,
        'banyak_item' => count($req),
        'total_bayar' => $total, 
        'status' => '5',
        'kasir_id' => $kasir_id,
        'tgl_bayar' => date("Y-m-d H:i:s")
      ]);
      
    }else{
      $sel->update(['status' => '5', 'tgl_bayar' => date("Y-m-d H:i:s") , 'kasir_id' => $kasir_id]);
    }

    foreach ($req as $key ) {
        $cek = ItemTransaksi::where([ 
          ['transaksi_id','=',$sel->id],
          ['item_id', '=', $key['product_id']]
        ])->first();

        if($cek->jumlah != $key['qty']){
         $cek->update(['jumlah' => $key['qty'], 'total' => $key['price'] ]);
        }

        $getCount = Item::where(['id' => $key['product_id']])->get();
        if ($getCount[0]['stock'] >= $key['qty']) {
            DB::table('item')->where('id', $key['product_id'])
                ->decrement('stock', $key['qty']);  

            DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['qty']);
            
            DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['qty']);
            
           // DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['qty']);

        }else {
            throw new \Exception('Stock ' . $getCount[0]['nama_item'] . ' Tidak Mencukupi');
        }


      }

    $sel->AmbilPesanan()->create(['diambil_oleh' => '-', 
      'input_by' => 'Kasir - '.$nama_kasir 
    ]);
    $dnotif =
    [
      'pengirim_id' => $kasir_id,
      'penerima_id' => $sel->user_id,
      'judul_id' => $sel->id,
      'judul' => 'Pengambilan Pesanan Nomor Transaksi '.$sel->no_transaksi,
      'isi' => 'Terima Kasih Telah Belanja Di Agogo Bakery, Pesanan Dengan Nomor Transaksi '.$sel->no_transaksi.' Telah Diterima ',
      'jenis_notif' => 8,
      'dibaca' => '0'
    ];

    $notif = Notifikasi::create($dnotif);
             //NotifGCM
    SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

    $this->setKunciTransaksi($sel->user_id);
    $inserR_Order = R_Order::create(['transaksi_id' => $sel->id, 
     'uang_dibayar' => $req[0]['dibayar'],
     'uang_kembali' => $req[0]['kembali'],
     'status' => $req[0]['status'] ]);

    $new_no_transaksi = $this->generateInvoice("1");
    return response()->json([
      'status' => 'success',
      'message' => $new_no_transaksi,
    ], 200);
}






public function getTransaksi($no_transaksi)
{
  $transaksi = Transaksi::where([ 
    ['metode_pembayaran','=','3'],
    ['no_transaksi','=',$no_transaksi],
    ['status','=','1']
  ]);

  if($transaksi->count() > 0){
   $transaksi1 = $transaksi->first();
   $item_transaksi = ItemTransaksi::join('item as a','a.id','=','item_transaksi.item_id')
   ->select('item_transaksi.item_id as id',
    'a.nama_item as name',
    'item_transaksi.jumlah as qty',
    'item_transaksi.harga as price')
   ->where('item_transaksi.transaksi_id', $transaksi1->id)
   ->get();
            // return $item_transaksi;
   
   $waktu_kirim = $transaksi1->waktu_kirim;
   $waktu_sekarang = date('Y-m-d H:i:s');

   if($transaksi1->jenis == '1'){
     if($waktu_kirim > $waktu_sekarang){
        $transaksi1['item_transaksi'] = $item_transaksi; 
        $success = '1';
        $message = $transaksi1;
     }else{
        $success = '0';
        $message = 'Pesanan Dengan No Transaksi '.$no_transaksi.' Telah Expire';
     }
   }elseif($transaksi1->jenis == '2'){
      $transaksi1['item_transaksi'] = $item_transaksi; 
      $success = '1';
      $message = $transaksi1;
   }else{
      $success = '0';
      $message = "No Transaksi ".$no_transaksi." Tidak Ditemukan";
   }
   
 }else{
   $success = '0';
   $message = "No Transaksi ".$no_transaksi." Tidak Ditemukan";
}

return response()->json(['success' => $success, 'message' => $message],200);
}

public function setKunciTransaksi($user_id)
{
  $sel_user = User::findOrFail($user_id);
  $transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
  $kunci_transaksi = $sel_user->DetailKonsumen->kunci_transaksi;

  if($transaksi_berlangsung < '3' && $kunci_transaksi == '1'){
    $sel_user->DetailKonsumen()->update(['kunci_transaksi' => '0']);
  }

  $success = 1;
  return $success;
}

public function keepOrder(Request $request)
{
  try{
    $req = $request->all();
    $orderId = $req[0]['order_id'];
            // return $order_id;

    if(!empty($orderId)){
      $this->deleteOrder($orderId);

      $req_transaksi = ['id' => $orderId,
      'user_id' => $req[0]['user_id'],
      'no_transaksi' => $req[0]['invoice'],
      'total_transaksi' => $req[0]['subtotal'],
      'total_bayar' => $req[0]['total'],
      'status' => '7',
      'jalur' => '2',
      'biaya_pengiriman' => '0',
      'biaya_pengiriman' => '0',
      'jarak_tempuh' => '0',
      'total_biaya_pengiriman' => '0',
      'banyak_item' => count($req),
      'lat' => '-',
      'long' => '-',
      'detail_alamat' => '-',
      'kasir_id' => $req[0]['user_id'],
      'metode_pembayaran' => '3',
      'tgl_bayar' => date("Y-m-d H:i:s"),
      'waktu_kirim' => date("Y-m-d H:i:s")
    ];
  }else{
                $no_transaksi = $this->generateInvoice('1'); // invoice nomor
                $req_transaksi = ['user_id' => $req[0]['user_id'],
                'no_transaksi' => $no_transaksi,
                'total_transaksi' => $req[0]['subtotal'],
                'total_bayar' => $req[0]['total'],
                'status' => '7',
                'jalur' => '2',
                'biaya_pengiriman' => '0',
                'biaya_pengiriman' => '0',
                'jarak_tempuh' => '0',
                'total_biaya_pengiriman' => '0',
                'banyak_item' => count($req),
                'lat' => '-',
                'long' => '-',
                'kasir_id' => $req[0]['user_id'],
                'detail_alamat' => '-',
                'metode_pembayaran' => '3',
                'tgl_bayar' => date("Y-m-d H:i:s"),
                'waktu_kirim' => date("Y-m-d H:i:s")
              ];

            }
            
            $insertTransaksi = Transaksi::create($req_transaksi);
            $transaksi_id = $insertTransaksi->id;
            $insItem = [];
            foreach ($req as $key) {
              $array = [];
                // $array['transaksi_id'] =  $transaksi_id;
              $array['item_id'] = $key['product_id'];
              $array['jumlah'] = $key['qty'];
              $array['harga'] = $key['price'];
              $array['margin'] = 0;
              $array['total'] = $key['qty'] * $key['price'];

              $insItem[] = $array;
            }

            $find = Transaksi::findOrFail($transaksi_id);
            $find->ItemTransaksi()->createMany($insItem);
            $find->R_Order()->create(['transaksi_id' => $transaksi_id, 
             'uang_dibayar' => 0,
             'uang_kembali' => 0,
             'status' => $req[0]['status'] ]);

            return response()->json([
             'status' => 'success',
             'message' => $insertTransaksi->no_transaksi,
           ], 200);
          }catch(Exception $e){
            return response()->json([
              'status' => 'failed',
              'message' => $e->getMessage()
            ], 400);
          }

        }



        public function getPaidOrders()
        {
          $orders = Transaksi::where(['status' => '5','jenis'=>'1'])->get();
          return response()->json($orders, 200);
        }


        public function getUnpaidOrders()
        {
         $sel = Transaksi::join('r_order as a','transaksi.id','=','a.transaksi_id')
         ->select('transaksi.id',
           'transaksi.no_transaksi as invoice',
           'transaksi.user_id',
           'transaksi.total_transaksi as subtotal',
           'a.discount',
           'a.add_fee',
           'transaksi.total_bayar as total',
           'a.uang_dibayar',
           'a.uang_kembali',
           'transaksi.created_at')
         ->where('a.status','UNPAID')
         ->get();
         return response()->json($sel,200);
       }




       public function getOrderDetail($id)
       {
         $detail_transaksi = ItemTransaksi::select('id',
           'transaksi_id as order_id',
           'item_id as product_id',
           'jumlah as qty',
           'harga as price',
           'item_id')
         ->where('item_transaksi.status','1')
         ->where('transaksi_id',$id)->get();

         $index = 0;
         foreach ($detail_transaksi as $key) {
           $item = Item::where('id',$key->item_id)->first();
           $array =  ['id' => $item->id, 'name' => $item->nama_item, 'price' => $item->harga ];

           $detail_transaksi[$index]['product'] = $array;
           $index++;
         }
         return response()->json($detail_transaksi, 200);
       }

       public function deleteOrder($id){
        $order = Transaksi::find($id);
        $order->delete();
        
        return response()->json([
          'status' => 'data deleted',
          'message' => $order->no_transaksi,
        ], 200);
      }









      public function postRefunds(Request $request){
 // return response()->json([
 //                'status' => 'failed',
 //                'message' => $request[0]['product_id']
 //            ], 400);

        if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
         return response()->json([
          'status' => 'failed',
          'message' => 'Invalid Username / PIN'
        ], 400);
       $user = $request->user();
       // $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2','7'])->count();
       $role = Aproval::where('user_id',$user->id)->where('rule','1')->count();


       if($role > 0){

        DB::beginTransaction();
        try {


          $total = 0;

          $result = collect($request)->map(function ($value) {
            return [
              'transaksi_id'    => $value['transaksi_id'],
              'item_id'  => $value['product_id'],
              'qty'         => $value['qty'],
              'harga'       => $value['price'],
              'total'       => $value['total'],
            ];
          })->all();





          foreach ($result as $key => $row) {  
                // Kurangin Total Amount di Summary Order



            DB::table('item_transaksi')
            ->where('transaksi_id', $row['transaksi_id'])
            ->where('item_id', $row['item_id'])
            ->update(['status' => '0']);

            DB::table('item')->where('id', $row['item_id'])
            ->increment('stock', $row['qty']);        

            $total = $total + $row['total'];

          }


          $refund = Refund::create(array(
            'invoice'       => $this->generateInvoiceRefunds(),
            'transaksi_id'      => $request[0]['transaksi_id'],
            'user_id'   => $request[0]['user_id'],
            'total'         => $total,
          ));


          if($request[0]['jum'] <=0 ){
            DB::table('transaksi')->where('id', $row['transaksi_id'])
            ->update(['status'=> '8']);
          }

          DB::commit();
          return response()->json([
            'status' => 'success',
            'message' =>  $request[0]['price'],
          ], 200);
        } catch (Exception $e) {
          DB::rollback();
          return response()->json([
            'status' => 'failed',
            'message' => $e->getMessage()
          ], 400);
        }
      }
      else {
        return response()->json([
          'status' => 'failed',
          'message' => 'Anda Bukan Approval'
        ], 400);
      }
    }




  }

// =======
// <?php

// namespace App\Http\Controllers\Api\react;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Transaksi;
// use App\ItemTransaksi;
// use App\R_Order;
// use App\User;
// use App\Item;
// use App\Notifikasi;
// use App\Helpers\SendNotif;
// use App\Role;
// use Illuminate\Support\Facades\Auth;
// use DB;
// use App\Refund;

// class OrderController extends Controller
// {
// 	public function generateInvoice($opsi)
// 	{
// 		if($opsi == "1"){
// 			$string_awal = "TK-";
// 			$select = Transaksi::where('jalur','2')->orderBy('id','DESC');
// 		}

// 		if($select->count() > 0){
// 			$first = $select->first();
// 			$explode = explode('-',$first->no_transaksi);
// 			$nextCode = $explode[1] + 1;
// 			$invoice = $string_awal.$nextCode;
// 		}else{
// 			$invoice = $string_awal."1";
// 		}

// 		return $invoice;
// 	}

//  public function generateInvoiceRefunds()
//  {
//   $refund = Refund::orderBy('id', 'DESC');
//   if ($refund->count() > 0) {
//     $refund = $refund->first();
//     $explode = explode('-', $refund->invoice);
//     $count = $explode[1] + 1;
//     return 'RF-' . $count;
//   }
//   return 'RF-1';
// }







// public function checkLastInvoice()
// {
//  $res = $this->generateInvoice('1');
//  return response()->json(['current_invoice' => $res],200);
// }


//     public function Coba()
//     {
//       $productid = 7;
//       $qty = 1;
//       $kategori_id = 2;

//       // DB::table('item')->where('id', $productid)->decrement('stock', $qty);
//       $aa = DB::table('item')->where('kategori_id',$kategori_id)->orderBy('id','DESC')->take(1)->get();
//       return $aa;            
//     }

// public function postOrder(Request $request)
// {
//  $req = $request->all();

//  if(!empty($request[0]['invoice']) ){            
//   $no_transaksi = $request[0]['invoice'] ;
// }
// else {
//   $no_transaksi = $this->generateInvoice('1');
// }

// $req_transaksi = ['user_id' => $req[0]['user_id'],
// 'no_transaksi' => $no_transaksi,
// 'total_transaksi' => $req[0]['subtotal'],
// 'total_bayar' => $req[0]['total'],
// 'status' => '5',
// 'jalur' => '2',
// 'biaya_pengiriman' => '0',
// 'biaya_pengiriman' => '0',
// 'jarak_tempuh' => '0',
// 'total_biaya_pengiriman' => '0',
// 'banyak_item' => count($req),
// 'lat' => '-',
// 'long' => '-',
// 'detail_alamat' => '-',
// 'metode_pembayaran' => '3',
// 'tgl_bayar' => date("Y-m-d H:i:s"),
// 'waktu_kirim' => date("Y-m-d H:i:s")
// ];

// if(!empty( $request[0]['id']) ){ 
//   $req_transaksi['id'] = $request[0]['id'];
// }


// $insItem = [];
// foreach ($req as $key) {
//   $array = [];
//           // $array['transaksi_id'] =  $insertTransaksi->id;
//   $array['item_id'] = $key['product_id'];
//   $array['jumlah'] = $key['qty'];
//   $array['harga'] = $key['price'];
//   $array['margin'] = 0;
//   $array['total'] = $key['qty'] * $key['price'];

//   $insItem[] = $array;



//   $getCount = Item::where(['id' => $key['product_id']])->get();

//            $getCount = Item::where(['id' => $key['product_id']])->get();
            
//             if ($getCount[0]['stock'] >= $key['qty']) {
//                 DB::table('item')->where('id', $key['product_id'])
//                     ->decrement('stock', $key['qty']);  
              
               
//                 DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_toko', $key['qty']);
                
//                 DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $key['qty']);
                
//                 DB::table('produksi')->where('item_id', $key['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $key['qty']);

//             }else {
//                 throw new \Exception('Stock ' . $getCount[0]['nama_item'] . ' Tidak Mencukupi');
//             }


// }




// $insertTransaksi = Transaksi::create($req_transaksi);
// $find = Transaksi::findOrFail($insertTransaksi->id);
// $find->ItemTransaksi()->createMany($insItem);
// $find->R_Order()->create(['transaksi_id' => $insertTransaksi->id, 
//  'uang_dibayar' => $req[0]['dibayar'],
//  'uang_kembali' => $req[0]['kembali'],
//  'status' => $req[0]['status'] ]);


// return response()->json([
//  'status' => 'success',
//  'message' => $no_transaksi,
// ], 200);
// }

// public function bayarTransaksiM(Request $request)
// {
//   $req = $request->all();
//   $no_transaksi = $req[0]['no_transaksi'];
//   $total = $req[0]['total'];

//   $kasir_id = $req[0]['user_id'];
//   $sel_kasir = User::findOrFail($kasir_id);
//   $nama_kasir = $sel_kasir->name;

//   $sel = Transaksi::where('no_transaksi',$no_transaksi)->first();
//   if($sel->total_bayar != $total){
//     $sel->update(['total_transaksi' => $total,
//       'banyak_item' => count($req),
//       'total_bayar' => $total, 
//       'status' => '5',
//       'tgl_bayar' => date("Y-m-d H:i:s")
//     ]);

//     foreach ($req as $key ) {
//       $cek = ItemTransaksi::where([ 
//         ['transaksi_id','=',$sel->id],
//         ['item_id', '=', $key['product_id']]
//       ])->first();

//       if($cek->jumlah != $key['qty']){
//        $cek->update(['jumlah' => $key['qty'], 'total' => $key['price'] ]);
//      }
//    }
//  }else{
//   $sel->update(['status' => '5', 'tgl_bayar' => date("Y-m-d H:i:s") ]);
// }
// $sel->AmbilPesanan()->create(['diambil_oleh' => '-', 
//   'input_by' => 'Kasir - '.$nama_kasir 
// ]);
// $dnotif =
// [
//   'pengirim_id' => $kasir_id,
//   'penerima_id' => $sel->user_id,
//   'judul_id' => $sel->id,
//   'judul' => 'Pengambilan Pesanan Nomor Transaksi '.$sel->no_transaksi,
//   'isi' => 'Terima Kasih Telah Belanja Di Agogo Bakery, Pesanan Dengan Nomor Transaksi '.$sel->no_transaksi.' Telah Diterima ',
//   'jenis_notif' => 8,
//   'dibaca' => '0'
// ];

// $notif = Notifikasi::create($dnotif);
//          //NotifGCM
// SendNotif::sendTopicWithUserId($notif->pengirim_id, $notif->judul, substr($notif->isi, 30), 0, $notif->penerima_id, 'transaksi', $notif->judul_id);

// $this->setKunciTransaksi($sel->user_id);
// $inserR_Order = R_Order::create(['transaksi_id' => $sel->id, 
//  'uang_dibayar' => $req[0]['dibayar'],
//  'uang_kembali' => $req[0]['kembali'],
//  'status' => $req[0]['status'] ]);

// $new_no_transaksi = $this->generateInvoice("1");
// return response()->json([
//   'status' => 'success',
//   'message' => $new_no_transaksi,
// ], 200);
// }

// public function getTransaksi($no_transaksi)
// {
//   $transaksi = Transaksi::where([ 
//     ['metode_pembayaran','=','3'],
//     ['no_transaksi','=',$no_transaksi],
//     ['status','=','1']
//   ]);
//   if($transaksi->count() > 0){
//    $transaksi1 = $transaksi->first();
//    $item_transaksi = ItemTransaksi::join('item as a','a.id','=','item_transaksi.item_id')
//    ->select('item_transaksi.item_id as id',
//     'a.nama_item as name',
//     'item_transaksi.jumlah as qty',
//     'item_transaksi.harga as price')
//    ->where('item_transaksi.transaksi_id', $transaksi1->id)
//    ->get();
//             // return $item_transaksi;
//    $transaksi1['item_transaksi'] = $item_transaksi; 
//    $success = '1';
//    $message = $transaksi1;
//  }else{
//   $success = '0';
//   $message = "No Transaksi Tidak Ditemukan";
// }

// return response()->json(['success' => $success, 'message' => $message],200);
// }

// public function setKunciTransaksi($user_id)
// {
//   $sel_user = User::findOrFail($user_id);
//   $transaksi_berlangsung = $sel_user->Transaksi->whereNotIn('status',['5','3'] )->count();
//   $kunci_transaksi = $sel_user->DetailKonsumen->kunci_transaksi;

//   if($transaksi_berlangsung < '3' && $kunci_transaksi == '1'){
//     $sel_user->DetailKonsumen()->update(['kunci_transaksi' => '0']);
//   }

//   $success = 1;
//   return $success;
// }

// public function keepOrder(Request $request)
// {
//   try{
//     $req = $request->all();
//     $orderId = $req[0]['order_id'];
//             // return $order_id;

//     if(!empty($orderId)){
//       $this->deleteOrder($orderId);

//       $req_transaksi = ['id' => $orderId,
//       'user_id' => $req[0]['user_id'],
//       'no_transaksi' => $req[0]['invoice'],
//       'total_transaksi' => $req[0]['subtotal'],
//       'total_bayar' => $req[0]['total'],
//       'status' => '7',
//       'jalur' => '2',
//       'biaya_pengiriman' => '0',
//       'biaya_pengiriman' => '0',
//       'jarak_tempuh' => '0',
//       'total_biaya_pengiriman' => '0',
//       'banyak_item' => count($req),
//       'lat' => '-',
//       'long' => '-',
//       'detail_alamat' => '-',
//       'metode_pembayaran' => '3',
//       'tgl_bayar' => date("Y-m-d H:i:s"),
//       'waktu_kirim' => date("Y-m-d H:i:s")
//     ];
//   }else{
//                 $no_transaksi = $this->generateInvoice('1'); // invoice nomor
//                 $req_transaksi = ['user_id' => $req[0]['user_id'],
//                 'no_transaksi' => $no_transaksi,
//                 'total_transaksi' => $req[0]['subtotal'],
//                 'total_bayar' => $req[0]['total'],
//                 'status' => '7',
//                 'jalur' => '2',
//                 'biaya_pengiriman' => '0',
//                 'biaya_pengiriman' => '0',
//                 'jarak_tempuh' => '0',
//                 'total_biaya_pengiriman' => '0',
//                 'banyak_item' => count($req),
//                 'lat' => '-',
//                 'long' => '-',
//                 'detail_alamat' => '-',
//                 'metode_pembayaran' => '3',
//                 'tgl_bayar' => date("Y-m-d H:i:s"),
//                 'waktu_kirim' => date("Y-m-d H:i:s")
//               ];

//             }
            
//             $insertTransaksi = Transaksi::create($req_transaksi);
//             $transaksi_id = $insertTransaksi->id;
//             $insItem = [];
//             foreach ($req as $key) {
//               $array = [];
//                 // $array['transaksi_id'] =  $transaksi_id;
//               $array['item_id'] = $key['product_id'];
//               $array['jumlah'] = $key['qty'];
//               $array['harga'] = $key['price'];
//               $array['margin'] = 0;
//               $array['total'] = $key['qty'] * $key['price'];

//               $insItem[] = $array;
//             }

//             $find = Transaksi::findOrFail($transaksi_id);
//             $find->ItemTransaksi()->createMany($insItem);
//             $find->R_Order()->create(['transaksi_id' => $transaksi_id, 
//              'uang_dibayar' => 0,
//              'uang_kembali' => 0,
//              'status' => $req[0]['status'] ]);

//             return response()->json([
//              'status' => 'success',
//              'message' => $insertTransaksi->no_transaksi,
//            ], 200);
//           }catch(Exception $e){
//             return response()->json([
//               'status' => 'failed',
//               'message' => $e->getMessage()
//             ], 400);
//           }

//         }



//         public function getPaidOrders()
//         {
//           $orders = Transaksi::where(['status' => '5','jenis'=>'1'])->get();
//           return response()->json($orders, 200);
//         }


//         public function getUnpaidOrders()
//         {
//          $sel = Transaksi::join('r_order as a','transaksi.id','=','a.transaksi_id')
//          ->select('transaksi.id',
//            'transaksi.no_transaksi as invoice',
//            'transaksi.user_id',
//            'transaksi.total_transaksi as subtotal',
//            'a.discount',
//            'a.add_fee',
//            'transaksi.total_bayar as total',
//            'a.uang_dibayar',
//            'a.uang_kembali',
//            'transaksi.created_at')
//          ->where('a.status','UNPAID')
//          ->get();
//          return response()->json($sel,200);
//        }




//        public function getOrderDetail($id)
//        {
//          $detail_transaksi = ItemTransaksi::select('id',
//            'transaksi_id as order_id',
//            'item_id as product_id',
//            'jumlah as qty',
//            'harga as price',
//            'item_id')
//          ->where('item_transaksi.status','1')
//          ->where('transaksi_id',$id)->get();

//          $index = 0;
//          foreach ($detail_transaksi as $key) {
//            $item = Item::where('id',$key->item_id)->first();
//            $array =  ['id' => $item->id, 'name' => $item->nama_item, 'price' => $item->harga ];

//            $detail_transaksi[$index]['product'] = $array;
//            $index++;
//          }
//          return response()->json($detail_transaksi, 200);
//        }

//        public function deleteOrder($id){
//         $order = Transaksi::find($id);
//         $order->delete();
        
//         return response()->json([
//           'status' => 'data deleted',
//           'message' => $order->no_transaksi,
//         ], 200);
//       }









//       public function postRefunds(Request $request){
//  // return response()->json([
//  //                'status' => 'failed',
//  //                'message' => $request[0]['product_id']
//  //            ], 400);

//         if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
//          return response()->json([
//           'status' => 'failed',
//           'message' => 'Invalid Username / PIN'
//         ], 400);
//        $user = $request->user();
//        $role = Role::where('user_id',$user->id)->where('level_id',1)->orWhere('level_id',2)->count();


//        if($role > 0){

//         DB::beginTransaction();
//         try {


//           $total = 0;

//           $result = collect($request)->map(function ($value) {
//             return [
//               'transaksi_id'    => $value['transaksi_id'],
//               'item_id'  => $value['product_id'],
//               'qty'         => $value['qty'],
//               'harga'       => $value['price'],
//               'total'       => $value['total'],
//             ];
//           })->all();





//           foreach ($result as $key => $row) {  
//                 // Kurangin Total Amount di Summary Order



//             DB::table('item_transaksi')
//             ->where('transaksi_id', $row['transaksi_id'])
//             ->where('item_id', $row['item_id'])
//             ->update(['status' => '0']);

//             DB::table('item')->where('id', $row['item_id'])
//             ->increment('stock', $row['qty']);        

//             $total = $total + $row['total'];

//           }


//           $refund = Refund::create(array(
//             'invoice'       => $this->generateInvoiceRefunds(),
//             'transaksi_id'      => $request[0]['transaksi_id'],
//             'user_id'   => $request[0]['user_id'],
//             'total'         => $total,
//           ));


//           if($request[0]['jum'] <=0 ){
//             DB::table('transaksi')->where('id', $row['transaksi_id'])
//             ->update(['status'=> '8']);
//           }

//           DB::commit();
//           return response()->json([
//             'status' => 'success',
//             'message' =>  $request[0]['price'],
//           ], 200);
//         } catch (Exception $e) {
//           DB::rollback();
//           return response()->json([
//             'status' => 'failed',
//             'message' => $e->getMessage()
//           ], 400);
//         }
//       }
//       else {
//         return response()->json([
//           'status' => 'failed',
//           'message' => 'Invalid PIN'
//         ], 400);
//       }
//     }




//   }
// >>>>>>> 3c79cf935e0cc26f69ccf4672837c37ac9a6ac17
