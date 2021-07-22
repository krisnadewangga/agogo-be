<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaksi;
use App\User;
use DB;
use App\Preorders;
use App\DetailPreorder;
use App\ItemTransaksi;
use App\Role;
use App\Aproval;
use App\Item;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\Acak;

class PreorderController extends Controller
{
    //



 public function generateInvoice()
 {
    $forCode = Carbon::now()->format('ymd');
    
    $maxKD = Transaksi::where('no_transaksi','LIKE','PS-'.$forCode.'%')->orderBy('id','DESC')->first();
    if(!empty($maxKD->id)){
        $nexKD = Acak::AmbilId($maxKD['no_transaksi'],'PS-'.$forCode,9,3);  
    }else{
        $nexKD = 'PS-'.$forCode.'001';
    }
    return $nexKD;

  // $preorder = Transaksi::where('jenis',2)
  //                       ->where('for_ps','1')
  //                       ->orderBy('id', 'DESC');

  // if ($preorder->count() > 0) {
  //   $preorder = $preorder->first();
  //   $explode = explode('-', $preorder->no_transaksi);
  //   $count = $explode[1] + 1;
  //   return 'PS-' . $count;
  // }
  // return 'PS-1';
}


public function checkLastInvoicePesanan()
{
  $res = $this->generateInvoice();
  return response()->json(array(
      'current_invoice' => $res), 200);  

  // return response()->json(['current_invoice' => $res],200);
  // $preorder = Transaksi::where('jenis',2)->orderBy('id', 'DESC');
  // if ($preorder->count() > 0) {
  //   $preorder = $preorder->first();
  //   $explode = explode('-', $preorder->no_transaksi);
  //   $count = $explode[1] + 1;
  //   $result =  'PS-' . $count;
  //   return response()->json(array(
  //     'current_invoice' => $result), 200);        
  // }
  // $result = 'PS-1';
  // return response()->json(array(
  //   'current_invoice' => $result), 200);

}


public function index()
{
        // $preorders = Preorder::where(['status' => 'UNPAID'])->get();

  $preorders = Transaksi::join('preorders','preorders.transaksi_id','=','transaksi.id')->where(['status' => '1'])->where(['jenis'=>'2'])->with(array('user'=>function($query){
    $query->select('id','name');
  }))->select('transaksi.id as id',
  'transaksi.no_transaksi',
  'transaksi.user_id',
  'transaksi.catatan',
  'transaksi.status',
  'transaksi.detail_alamat as alamat',
  'transaksi.created_at',
  'transaksi.updated_at',
  'preorders.tgl_pesan',
  'preorders.tgl_selesai',
  'preorders.waktu_selesai',
  'preorders.telepon',
  'preorders.subtotal',
  'preorders.discount',
  'preorders.add_fee',
  'preorders.nama',
  'preorders.uang_muka',
  'preorders.total',
  'preorders.sisa_harus_bayar',
  'preorders.uang_dibayar',
  'preorders.uang_kembali'
)->get();

  return response()->json($preorders, 200);
}





public function store(Request $request)
{


   $req = $request->all();

   if(!empty($request[0]['alamat']) && !empty($request[0]['nama']) && !empty($request[0]['tgl_pesan']) && !empty($request[0]['tgl_selesai']) && !empty($request[0]['waktu_selesai']) && !empty($request[0]['telepon'])) 
    {

      if(!Auth::attempt(['name' => $req[0]['username_approval'], 'password' => $req[0]['pin_approval'] ]))
      return response()->json([
        'status' => 'failed',
        'status1' => '0',
        'message' => 'Invalid Username / PIN'
      ], 400);
    $user = $request->user();
    // $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2','7'])->count();
    $role = Aproval::where('user_id',$user->id)->where('rule','2')->count();

    if($role > 0){
      // if(!empty($request[0]['invoice']) ){            
        
      // }else {
      //   $no_transaksi = $this->generateInvoice('1');
      // }
      $kasir = User::findOrFail($req[0]['user_id']);
      $no_transaksi = $request[0]['invoice'] ;
      $req_transaksi = ['user_id' => $req[0]['user_id'],
            'no_transaksi' => $no_transaksi,
            'total_transaksi' => $req[0]['subtotal'],
            'total_bayar' => $req[0]['total'],
            'status' => '1',
            'jalur' => '2',
            'jenis' => '2',
            'biaya_pengiriman' => '0',
            'biaya_pengiriman' => '0',
            'jarak_tempuh' => '0',
            'total_biaya_pengiriman' => '0',
            'banyak_item' => count($req),
            'lat' => '-',
            'long' => '-',
            // 'kasir_id' => $req[0]['user_id'],
            'catatan' => $request[0]['catatan'],
            'detail_alamat' => $request[0]['alamat'],
            'tax' => $request[0]['tax'],
            'metode_pembayaran' => '3',
            'waktu_kirim' => date("Y-m-d H:i:s")
          ];

       

          if(!empty( $request[0]['id']) ){ 
            $req_transaksi['id'] = $request[0]['id'];
          }


          $cek = Transaksi::where('no_transaksi',$no_transaksi)->orderBy('id','desc')->first();
          if(empty($cek->id)){
              $insertTransaksi = Transaksi::create($req_transaksi);
              $find = Transaksi::findOrFail($insertTransaksi->id);

              $insItem = [];
              foreach ($req as $key) {
                $array = [];
                      // $array['transaksi_id'] =  $insertTransaksi->id;
                $array['item_id'] = $key['product_id'];
                $array['jumlah'] = $key['qty'];
                $array['harga'] = $key['price'];
                $array['margin'] = 0;
                $array['total'] = $key['qty'] * $key['price'];

                $insItem[] = $array;
              }

              $find->ItemTransaksi()->createMany($insItem);
              $find->Preorder()->create([
                'transaksi_id' => $insertTransaksi->id, 
                'nama'          => $request[0]['nama'],
                'tgl_pesan'     => $request[0]['tgl_pesan'],
                'tgl_selesai'   => $request[0]['tgl_selesai'],
                'waktu_selesai' => $request[0]['waktu_selesai'],
                'telepon'       => $request[0]['telepon'],
                'subtotal'      => $request[0]['subtotal'],
                'discount'      => $request[0]['diskon'],
                'add_fee'       => $request[0]['add_fee'],
                'uang_muka'     => $request[0]['uang_muka'],
                'total'         => $request[0]['total'],
                'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
                'sisa_bayar' => $request[0]['sisa_harus_bayar'],
                'uang_dibayar'  => $request[0]['uang_dibayar'],
                'uang_kembali'  => $request[0]['uang_kembali'],
                'pencatat_entri' => $kasir->id
              ]);


              return response()->json([
                'status' => 'success',
                'status1' => '1',
                'message' => $no_transaksi,
              ], 200);
          }else{
            if($cek->user_id != $req[0]['user_id']){


                $req_transaksi['no_transaksi'] = $this->generateInvoice('1');

                $insertTransaksi = Transaksi::create($req_transaksi);
                $find = Transaksi::findOrFail($insertTransaksi->id);

                $insItem = [];
                foreach ($req as $key) {
                  $array = [];
                        // $array['transaksi_id'] =  $insertTransaksi->id;
                  $array['item_id'] = $key['product_id'];
                  $array['jumlah'] = $key['qty'];
                  $array['harga'] = $key['price'];
                  $array['margin'] = 0;
                  $array['total'] = $key['qty'] * $key['price'];

                  $insItem[] = $array;
                }

                $find->ItemTransaksi()->createMany($insItem);
                $find->Preorder()->create([
                  'transaksi_id' => $insertTransaksi->id, 
                  'nama'          => $request[0]['nama'],
                  'tgl_pesan'     => $request[0]['tgl_pesan'],
                  'tgl_selesai'   => $request[0]['tgl_selesai'],
                  'waktu_selesai' => $request[0]['waktu_selesai'],
                  'telepon'       => $request[0]['telepon'],
                  'subtotal'      => $request[0]['subtotal'],
                  'discount'      => $request[0]['diskon'],
                  'add_fee'       => $request[0]['add_fee'],
                  'uang_muka'     => $request[0]['uang_muka'],
                  'total'         => $request[0]['total'],
                  'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
                  'sisa_bayar' => $request[0]['sisa_harus_bayar'],
                  'uang_dibayar'  => $request[0]['uang_dibayar'],
                  'uang_kembali'  => $request[0]['uang_kembali'],
                  'pencatat_entri' => $kasir->id]);


                return response()->json([
                  'status' => 'success',
                  'status1' => '1',
                  'message' => $no_transaksi,
                ], 200);
            }
          }

        

    }else{
      return response()->json([
        'status' => 'failed',
        'status1' => '0',
        'message' => 'Anda Bukan Approval'
      ], 400);

    }



   }
 // $request[0]['alamat']
 // $request[0]['nama'],
 // $request[0]['tgl_pesan'],
 // $request[0]['tgl_selesai'],
 // $request[0]['waktu_selesai'],
 // $request[0]['telepon'],

   else{
    return response()->json([
        'status' => 'failed',
        'message' => 'nama, tgl , jam selesai, telepon,alamat tidak bisa kosong'
      ], 400);  
   }
    
      




}


public function editPreorder(Request $request)
{
  $req = $request->all();

  if(!Auth::attempt(['name' => $req[0]['username_approval'], 'password' => $req[0]['pin_approval'] ]))
    return response()->json([
      'status' => 'failed',
      'message' => 'Invalid Username / PIN'
    ], 400);
  $user = $request->user();
  // $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2','7'])->count();
  $role = Aproval::where('user_id',$user->id)->where('rule','2')->count();

  if($role > 0){


    DB::beginTransaction();
    try {
      $kasir = User::findOrFail($req[0]['user_id']);
      $delPreorder = Preorders::where('transaksi_id',$request[0]['preorder_id']);
      $delPreorder->delete();
      ItemTransaksi::where('transaksi_id',$request[0]['preorder_id'])->delete();

      $preorder = Preorders::create(array(
        'transaksi_id'  => $request[0]['preorder_id'],
        'nama'          => $request[0]['nama'],
        'tgl_pesan'     => $request[0]['tgl_pesan'],
        'tgl_selesai'   => $request[0]['tgl_selesai'],
        'waktu_selesai' => $request[0]['waktu_selesai'],
        'telepon'       => $request[0]['telepon'],
        'subtotal'      => $request[0]['subtotal'],
        'discount'      => $request[0]['diskon'],
        'add_fee'       => $request[0]['add_fee'],
        'uang_muka'     => $request[0]['uang_muka'],
        'total'         => $request[0]['total'],
        'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
        'uang_dibayar'  => $request[0]['uang_dibayar'],
        'uang_kembali'  => $request[0]['uang_kembali'],
        'sisa_bayar' => $request[0]['sisa_harus_bayar'],
        'pencatat_entri' => $kasir->id
      ));

      $update = Transaksi::where('id',$req[0]['preorder_id'])->update(['total_transaksi' =>$req[0]['subtotal'],
        'total_bayar' => $req[0]['total'],
        'catatan' => $request[0]['catatan'],
        'tax' => $request[0]['tax'],
        'detail_alamat' => $request[0]['alamat']]);


      $result = collect($request)->map(function ($value) {
        return [
          'product_id'    => $value['product_id'],
          'qty'           => $value['qty'],
          'price'         => $value['price'],
        ];
      })->all();
            // return response($result);

      foreach ($result as $key => $row) {


       
          Transaksi::find($req[0]['preorder_id'])->ItemTransaksi()->create([
            'item_id' => $row['product_id'],
            'jumlah' => $row['qty'],
            'harga' => $row['price'],
            'margin' => 0,
            'total' => $row['qty'] * $row['qty']
          ]);                

          

        // return response($row['product_id']);
                //return response($getCount[0]['stock']);                               
      }

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => $preorder->invoice,
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


public function paid_preorder()
{
  // $preorders = Preorder::where(['status' => 'PAID'])->with(array('user'=>function($query){
  //   $query->select('id','username');
  // }))->get();        
  $preorders = Transaksi::join('users','users.id','=','transaksi.user_id')

  ->join('preorders','preorders.transaksi_id','=','transaksi.id')
  ->select('transaksi.id as id',
    'transaksi.no_transaksi',
    'users.id as user_id',
    'transaksi.total_bayar',
    'transaksi.catatan',
    'transaksi.detail_alamat',
    'preorders.nama',
    'preorders.tgl_selesai',
    'preorders.telepon',
    'preorders.add_fee',
    'preorders.uang_muka',
    'preorders.waktu_selesai',
    'preorders.tgl_pesan',
    'preorders.subtotal',
    'preorders.discount',
    'users.name'
  )
  ->where(['status' => '5','jenis'=>'2'])->get();

  return response()->json($preorders, 200);
}



public function show($id)
{
  $preorder_detail = ItemTransaksi::with(array('item'=>function($query){
    $query->select('nama_item','id','harga');
  }))->where(['transaksi_id' => (string)$id, 'status' => '1'])->get();
        // $product = Product::where('id',$order_detail[0]['product_id'])->get();
        // $result = compact('order_detail','product');
        // return response($order_detail[0]['product_id']);
  return response()->json($preorder_detail, 200);
}



public function bayarPreorder(Request $request)
{        



  if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
   return response()->json([
    'status' => 'failed',
    'message' => 'Invalid Username / PIN'
  ], 400);
 $user = $request->user();
 // $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2','7'])->count();
 $role = Aproval::where('user_id',$user->id)->where('rule','2')->count();

 if($role > 0){
   $kasir = User::findOrFail($request[0]['user_id']);
   $db = Transaksi::find((string)$request[0]['preorder_id']);
   $db->Preorder()->update([
    'tgl_selesai'   => date('Y-m-d'),
    'waktu_selesai' => date('H:i'),
    'sisa_harus_bayar'  => $request[0]['sisa_harus_bayar'],
    'uang_dibayar'  => $request[0]['uang_dibayar'],
    'uang_kembali'  => $request[0]['uang_kembali'],
    'pencatat_pengambilan' => $kasir->id
  ]);        


   $result = collect($request)->map(function ($value) {
    return [
      'product_id'    => $value['product_id'],
      'qty'           => $value['qty'],
      'price'         => $value['price'],
    ];
  })->all();
            // return response($result);

   foreach ($result as $key => $row) {  


      DB::table('item')->where('id', $row['product_id'])->decrement('stock', $row['qty']);

      DB::table('produksi')->where('item_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('penjualan_pemesanan', $row['qty']);
      DB::table('produksi')->where('item_id', $row['product_id'])->orderBy('id','DESC')->take(1)->increment('total_penjualan', $row['qty']);
      DB::table('produksi')->where('item_id', $row['product_id'])->orderBy('id','DESC')->take(1)->decrement('sisa_stock', $row['qty']);  
    
  
    $db->update(['status' => '5','tgl_bayar' => date("Y-m-d H:i:s"),'kasir_id' => $request[0]['user_id']]);

  }



  DB::commit();
  return response()->json([
    'status' => 'success',
    'message' => $db->no_transaksi,
  ], 200);

}else {

  return response()->json([
    'status' => 'gagal',
    'message' => "Anda Bukan Approval"
  ], 400);

}




}


public function cancelPreorder(Request $request,$id)
{

 if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
   return response()->json([
    'status' => 'failed',
    'message' => 'Invalid Username / PIN'
  ], 400);
 $user = $request->user();
 // $role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2','7'])->count();
 $role = Aproval::where('user_id',$user->id)->where('rule','2')->count();

 if($role > 0){

  $preorder = Transaksi::where('id', $id)->update(['status' => '3']);
        // $products->stock = $request->input('sisa_stock');
        // $products->save();
  return response()->json(['status' => 'success'], 200);
}
else {
  return response()->json([
    'status' => 'failed',
    'message' => 'Anda Bukan Approval'
  ], 400);
}
}
}
