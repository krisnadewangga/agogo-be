<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Role;
use App\Aproval;
use App\Produksi;
class ProduksiController extends Controller
{
    //



 public function getAvailProduct()
 {
    $products = DB::table('item')->where('stock', '>=', 1)->get();
    return response()->json($products, 200);
}

public function getNotAvailProduct()
{
    $products = DB::table('item')->where('stock', '<=', 0)->get();
    return response()->json($products, 200);
}



public function getTrxByProduct($id) {


    // $production = DB::table('produksi')
    // ->where('item_id', $id)
    // ->where('created_at', '>', Carbon::today())
    // ->orderBy('created_at','DESC')->first();

   
    $production = DB::table('produksi')
    ->join('item','item.id', '=', 'produksi.item_id')
    ->where('item.id', $id)
    ->where('produksi.created_at', '>', Carbon::today())
    ->select('produksi.id','produksi.item_id','produksi.produksi1','produksi.produksi2','produksi.produksi3','produksi.total_produksi','produksi.penjualan_toko','produksi.penjualan_pemesanan','produksi.total_penjualan','produksi.ket_rusak','produksi.ket_lain','produksi.total_lain','produksi.catatan','produksi.stock_awal','item.stock as sisa_stock','produksi.created_at','produksi.updated_at')
    ->orderBy('produksi.created_at','DESC')->first();


    // if ($production == null ) {

    //     $date_produksi = DB::table('produksi')
    //     ->select('created_at')
    //     ->where('item_id', $id)
    //     ->orderBy('created_at', 'DESC')->first();


    //         //Cek apakah ada initial produksi / tidak
    //     if ($date_produksi == null) {  



    //         $order = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_transaksi.item_id', $id)
    //             // ->whereBetween('order_details.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','5')
    //         ->where('transaksi.jenis','1')
    //             // ->get();
    //         ->sum('jumlah');

    //          $orderDalamPengantaran = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_transaksi.item_id', $id)
    //             // ->whereBetween('order_details.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','2')
    //         ->where('transaksi.jenis','1')
    //             // ->get();
    //         ->sum('jumlah');



    //         $preorder = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_transaksi.item_id', $id)
    //             // ->whereBetween('preorder_details.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','5')
    //         ->where('transaksi.jenis','2')            
    //             // ->where('status','PAID')
    //         ->sum('jumlah');



    //         $getStock = DB::table('item')
    //         ->select('stock')            
    //         ->where('id', $id) 
    //         ->orderBy('created_at', 'DESC')
    //         ->first();
    //         $stock_awal = $getStock->stock + $preorder + $order + $orderDalamPengantaran ;



    //         return response()->json(array(
    //             // 'status'        => 'failed',
    //             'count_order'   => $order,
    //             'count_preorder'=> $preorder,
    //             'stok_awal'     => $stock_awal,
    //             'sisa_stock'    => $getStock->stock,
    //             'message'       => 'production : null',
    //             'production'    => $production,
    //         ),200);

    //     }    
    //     else {
    //             // Jika ada tanggal produksi maka menggunakan tanggal produksi yg sudah didapatkan tadi
    //         $start_date = Carbon::parse($date_produksi->created_at)->format('Y-m-d') . ' 00:00:01';
    //         $end_date = Carbon::parse($date_produksi->created_at)->format('Y-m-d') . ' 23:59:59';
    //             // $end_date = Carbon::now()->format('Y-m-d H:i:s');
    //             // $tgl_produksi = $date_production_not_null;


    //         $production = DB::table('produksi')
    //         ->where('item_id', $id)
    //         ->whereBetween('created_at', [$start_date, $end_date])
    //         ->orderBy('created_at','DESC')->first();

    //             //  dd($production);

    //         $order = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_transaksi.item_id', $id)
    //         ->whereBetween('item_transaksi.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','5')
    //         ->where('transaksi.jenis','1')
    //             // ->get();
    //         ->sum('jumlah');

    //         $orderDalamPengantaran = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_transaksi.item_id', $id)
    //         ->whereBetween('item_transaksi.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','2')
    //         ->where('transaksi.jenis','1')
    //             // ->get();
    //         ->sum('jumlah');

    //         $preorder = DB::table('item_transaksi')
    //         ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
    //         ->where('item_id', $id)
    //         ->whereBetween('item_transaksi.created_at', [$start_date, $end_date])
    //         ->where('transaksi.status','5')  
    //         ->where('transaksi.jenis','2')          
    //             // ->where('status','PAID')
    //         ->sum('jumlah');


    //             // $getStock = DB::table('products')
    //             // ->select('stock')
    //             // ->where('id', $id) 
    //             // ->get();
    //             // $stock_awal = $getStock[0]->stock + $preorder + $order;

    //         $getStock = DB::table('produksi')
    //         ->select('stock_awal')            
    //         ->where('item_id', $id) 
    //         ->orderBy('created_at', 'DESC')
    //         ->first();
    //         $stock_awal = $getStock->stock_awal + $preorder + $order + $orderDalamPengantaran;

    //         return response()->json(array(
    //                 // 'last_trx_date' =>  null,
    //             'count_order'   => $order,
    //             'count_preorder'=> $preorder,
    //             'stok_awal'     => $stock_awal,
    //             'production'    => $production,

    //         ),200); 
    //     }           

    // }else {

        $curent_date = Carbon::now()->format('Y-m-d');

        // $curent_date = Carbon::now();

        $start_date = Carbon::parse($curent_date)->format('Y-m-d') . ' 00:00:01';
        $end_date = Carbon::parse($curent_date)->format('Y-m-d') . ' 23:59:59';


        
        $order = DB::table('item_transaksi')
        ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
        ->where('item_transaksi.item_id', $id)
        ->whereBetween('transaksi.tgl_bayar', [$start_date, $end_date])
        ->where('transaksi.status','5')
        ->where('transaksi.jenis','1')
            // ->get();
        ->sum('jumlah');



        $orderDalamPengantaran = DB::table('item_transaksi')
        ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
        ->where('item_transaksi.item_id', $id)
        ->whereBetween('transaksi.tgl_bayar', [$start_date, $end_date])
        ->where('transaksi.status','2')
        ->where('transaksi.jenis','1')
            // ->get();
        ->sum('jumlah');

        $preorder = DB::table('item_transaksi')
        ->join('transaksi','item_transaksi.transaksi_id', '=', 'transaksi.id')
        ->where('item_id', $id)
        ->whereBetween('transaksi.tgl_bayar', [$start_date, $end_date])
        ->where('transaksi.status','5')
        ->where('transaksi.jenis','2')            
            // ->where('status','PAID')
        ->sum('jumlah');

      //Ambil stok awal dari table prosuksi jika data produksi sudah ada
        $getStock = DB::table('produksi')
        ->select('stock_awal')            
        ->where('item_id', $id) 
        ->whereBetween('created_at', [$start_date, $end_date])
        ->orderBy('created_at', 'ASC')
        ->first();

        
        $stock_awal = $getStock->stock_awal;

            // dd($curent_date);


        return response()->json(array(
            'last_trx_date' => $curent_date,
            'count_order'   => $order + $orderDalamPengantaran,
            'count_preorder'=> $preorder,
            'stok_awal'     => $stock_awal,
            'production'    => $production,

        ),200);

    //}

}

public function GetLastDate()
{
    $date = DB::table('produksi')->select('created_at')->orderBy('created_at','DESC')->first();
    if ($date == null) {
        $date = 'no production';
    }
    return response()->json([            
        'date'   => $date
    ], 200);
}


// public function updateStock(Request $request,$id)
// {

//           // return $request[0]['sisa_stock'];
//     $sisa_stock = $request[0]['sisa_stock'];
//     $products = DB::table('item')->where('id', $id)->update(['stock' => $sisa_stock]);
//         // $products->stock = $request->input('sisa_stock');
//         // $products->save();
//     return response()->json(['status' => 'success'], 200);
// }



public function postProduction(Request $request)
{

    if($request == null ){

        return response()->json([
            'status' => 'failed',
            'message' => 'Anda Bukan Approval'
        ], 400);

    }else{
        $ubah_tanggal = null;
        $ubah_tanggal = $request[0]['ubah_tanggal'];
    
        if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval']]))
            return response()->json([
              'status' => 'failed',
              'message' => 'Invalid Username / PIN'
          ], 400);
        $user = $request->user();
        // $role = Role::where('user_id',$user->id)
        //              ->whereIn('level_id',['1','2','7'])
        //              ->count();
        $role = Aproval::where('user_id',$user->id)->where('rule','3')->count();
    
        if($role > 0){
    
            DB::beginTransaction();
            try {
    
    
                $date_produksi = DB::table('produksi')
                ->select('created_at')
                ->orderBy('created_at', 'DESC')->first();
                
                $tgl_produksi = null;
    
          
    
                if (Carbon::parse($date_produksi->created_at)->format('Y-m-d') < Carbon::now()->format('Y-m-d') ){            
                    $tgl_produksi = Carbon::parse($date_produksi->created_at)->format('Y-m-d') . '23:59:59';    
                   
                }            
              
                else {
                    $tgl_produksi = Carbon::now()->format('Y-m-d H:i:s');
                
                }
    
                $cari = Produksi::where('item_id',$request[0]['product_id'])->orderBy('created_at','DESC')->first();
    
                if(Carbon::parse($cari->created_at)->format('Y-m-d H:i:s') < Carbon::now()->format('Y-m-d H:i:s')){
    
                    if($request[0]['produksi1'] > 0 || $request[0]['ket_rusak'] > 0 || $request[0]['ket_lain'] > 0 ){           
                        $production = Produksi::create([
                            'item_id'               => $request[0]['product_id'] ,
                            'produksi1'             => $request[0]['produksi1'],
                            'produksi2'             => 0,
                            'produksi3'             => 0,
                            'total_produksi'        => $request[0]['total_produksi'],
                            'penjualan_toko'        => $request[0]['penjualan_toko'],
                            'penjualan_pemesanan'   => $request[0]['penjualan_pemesanan'],
                            'total_penjualan'       => $request[0]['total_penjualan'],
                            'ket_rusak'             => $request[0]['ket_rusak'],
                            'ket_lain'              => $request[0]['ket_lain'],
                            'total_lain'            => $request[0]['total_lain'],
                            'catatan'               => $request[0]['catatan'],
                            'stock_awal'            => $request[0]['stock_awal'],
                            'sisa_stock'            => $request[0]['sisa_stock'],
                            'created_at'            => $tgl_produksi
        
        
                        ]); 
                        $sisa_stock = $request[0]['sisa_stock'];
                        $products = DB::table('item')->where('id', $request[0]['product_id'])->update(['stock' => $sisa_stock]);
                    } 
                }
    
    
    
    
                
    
    
                
    
                DB::commit();
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produksi Berhasil',
                ]);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ], 400);
            }
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda Bukan Approval'
            ], 400);
        }

    }
    
}


// public function postProduction(Request $request)
// {
//     $ubah_tanggal = null;
//     $ubah_tanggal = $request[0]['ubah_tanggal'];

//     if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval']]))
//         return response()->json([
//           'status' => 'failed',
//           'message' => 'Invalid Username / PIN'
//       ], 400);
//     $user = $request->user();
//     // $role = Role::where('user_id',$user->id)
//     //              ->whereIn('level_id',['1','2','7'])
//     //              ->count();
//     $role = Aproval::where('user_id',$user->id)->where('rule','3')->count();

//     if($role > 0){

//         DB::beginTransaction();
//         try {

//             $production = DB::table('produksi')
//             // ->where('product_id', $id)
//             ->where('created_at', '>=', Carbon::today())
//             ->orderBy('created_at','DESC')->first();

//             $date_produksi = DB::table('produksi')
//             ->select('created_at')
//             // ->where('product_id', $id)
//             ->orderBy('created_at', 'DESC')->first();

//             $tgl_produksi = null;





//             if ($date_produksi == null) {
//                 $curent_date = Carbon::now()->format('Y-m-d');
//                 $tgl_produksi = $curent_date;                
//             }
//             elseif ($production == null && $ubah_tanggal == "no") {            
//                 $date_null_production = Carbon::parse($date_produksi->created_at)->format('Y-m-d') . '23:59:59';    
//                 $tgl_produksi = $date_null_production;
//             }            
//             // elseif ($production != null && $ubah_tanggal == "yes"){
//             //     $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
//             //     $tgl_produksi = $date_production_not_null;

//             // }
//             // elseif ($production != null && $ubah_tanggal == "no" ){
//             //     $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
//             //     $tgl_produksi = $date_production_not_null;
//             // }
//             else {
//                 $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
//                 $tgl_produksi = $date_production_not_null;
//                 // return $tgl_produksi;
//             }

//             // return $tgl_produksi;



//             $result = collect($request)->map(function ($value) {
//                 return [
//                     'item_id'               => $value['product_id'],
//                     'produksi1'             => $value['produksi1'],
//                     'produksi2'             => $value['produksi2'],
//                     'produksi3'             => $value['produksi3'],
//                     'total_produksi'        => $value['total_produksi'],
//                     'penjualan_toko'        => $value['penjualan_toko'],
//                     'penjualan_pemesanan'   => $value['penjualan_pemesanan'],
//                     'total_penjualan'       => $value['total_penjualan'],
//                     'ket_rusak'             => $value['ket_rusak'],
//                     'ket_lain'              => $value['ket_lain'],
//                     'total_lain'            => $value['total_lain'],
//                     'catatan'               => $value['catatan'],
//                     'stock_awal'            => $value['stock_awal'],
//                     'sisa_stock'            => $value['sisa_stock'],            
//                 ];
//             })->all();
//             // return response($result);

//             foreach ($result as $key => $row) {     
//                 if($row['produksi1'] > 0 || $row['ket_rusak'] > 0 || $row['ket_lain'] > 0 ){           
//                     $production = Produksi::create([
//                         'item_id'               => $row['item_id'] ,
//                         'produksi1'             => $row['produksi1'],
//                         'produksi2'             => 0,
//                         'produksi3'             => 0,
//                         'total_produksi'        => $row['total_produksi'],
//                         'penjualan_toko'        => $row['penjualan_toko'],
//                         'penjualan_pemesanan'   => $row['penjualan_pemesanan'],
//                         'total_penjualan'       => $row['total_penjualan'],
//                         'ket_rusak'             => $row['ket_rusak'],
//                         'ket_lain'              => $row['ket_lain'],
//                         'total_lain'            => $row['total_lain'],
//                         'catatan'               => $row['catatan'],
//                         'stock_awal'            => $row['stock_awal'],
//                         'sisa_stock'            => $row['sisa_stock'],
//                         'created_at'            => $tgl_produksi,
//                     ]); 
//                     $sisa_stock = $request[0]['sisa_stock'];
//                     $products = DB::table('item')->where('id', $request[0]['product_id'])->update(['stock' => $sisa_stock]);
//                 } 

               
//                     // return response($row['product_id']);
//                     //return response($getCount[0]['stock']);

                


//             }

//             DB::commit();

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Produksi Berhasil',
//             ]);
//         } catch (Exception $e) {
//             DB::rollback();
//             return response()->json([
//                 'status' => 'failed',
//                 'message' => $e->getMessage()
//             ], 400);
//         }
//     }else {
//         return response()->json([
//             'status' => 'failed',
//             'message' => 'Anda Bukan Approval'
//         ], 400);
//     }
// }


public function ubahTanggal(Request $request)
{

    $ubah_tanggal = null;
    $ubah_tanggal = $request[0]['ubah_tanggal'];

        // $get_role = User::role(['admin', 'manager'])
        // ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        // if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {        

    DB::beginTransaction();
    try {

        $production = DB::table('produksi')
            // ->where('product_id', $id)
        ->where('created_at', '>=', Carbon::today())
        ->orderBy('created_at','DESC')->first();

        $date_produksi = DB::table('produksi')
        ->select('created_at')
            // ->where('product_id', $id)
        ->orderBy('created_at', 'DESC')->first();

        $tgl_produksi = null;


        if ($date_produksi == null) {
            $curent_date = Carbon::now()->format('Y-m-d');
            $tgl_produksi = $curent_date;                
        }
        elseif ($production == null && $ubah_tanggal == "no") {            
            $date_null_production = Carbon::parse($date_produksi->created_at)->format('Y-m-d') . '23:59:59';    
            $tgl_produksi = $date_null_production;
        }            
            // elseif ($production != null && $ubah_tanggal == "yes"){
            //     $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
            //     $tgl_produksi = $date_production_not_null;

            // }
            // elseif ($production != null && $ubah_tanggal == "no" ){
            //     $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
            //     $tgl_produksi = $date_production_not_null;
            // }
        else {
            $date_production_not_null = Carbon::now()->format('Y-m-d H:i:s');
            $tgl_produksi = $date_production_not_null;
                // return $tgl_produksi;
        }

            // return $tgl_produksi;



        $result = collect($request)->map(function ($value) {
            return [
                'item_id'            => $value['product_id'],
                'produksi1'             => $value['produksi1'],
                'produksi2'             => $value['produksi2'],
                'produksi3'             => $value['produksi3'],
                'total_produksi'        => $value['total_produksi'],
                'penjualan_toko'        => $value['penjualan_toko'],
                'penjualan_pemesanan'   => $value['penjualan_pemesanan'],
                'total_penjualan'       => $value['total_penjualan'],
                'ket_rusak'             => $value['ket_rusak'],
                'ket_lain'              => $value['ket_lain'],
                'total_lain'            => $value['total_lain'],
                'catatan'               => $value['catatan'],
                'stock_awal'            => $value['stock_awal'],
                'sisa_stock'            => $value['sisa_stock'],            
            ];
        })->all();
            // return response($result);

        foreach ($result as $key => $row) {                
            $production = Produksi::create([
                'item_id'            => $row['item_id'],
                'produksi1'             => $row['produksi1'],
                'produksi2'             => $row['produksi2'],
                'produksi3'             => $row['produksi3'],
                'total_produksi'        => $row['total_produksi'],
                'penjualan_toko'        => $row['penjualan_toko'],
                'penjualan_pemesanan'   => $row['penjualan_pemesanan'],
                'total_penjualan'       => $row['total_penjualan'],
                'ket_rusak'             => $row['ket_rusak'],
                'ket_lain'              => $row['ket_lain'],
                'total_lain'            => $row['total_lain'],
                'catatan'               => $row['catatan'],
                'stock_awal'            => $row['stock_awal'],
                'sisa_stock'            => $row['sisa_stock'],
                'created_at'            => $tgl_produksi,
            ]);                
                    // return response($row['product_id']);
                //return response($getCount[0]['stock']);

        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Produksi Berhasil',
        ]);
    } catch (Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 'failed',
            'message' => $e->getMessage()
        ], 400);
    }
}

}