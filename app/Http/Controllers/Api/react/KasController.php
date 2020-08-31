<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Kas;
use App\Role;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Transaksi;
use Carbon\Carbon;

class KasController extends Controller
{
    //

	public function cekKas($id){


   		$kas = Kas::where('user_id',$id)->where('status','0')->orderBy('id','DESC')->first();

		if($kas){
			return response()->json([
			'status' => 'notcounted',
			'message' => 'belum dihitung',
			'id' => $kas['id']
 		], 200);	
		}else{
			return response()->json([
			'status' => 'counted',
			'message' => 'telah dihitung'
		], 200);	
		}
			

		// if (Kas::where('user_id',"2")->where('saldo_akhir',0)) {
		// 	return response()->json([
		// 	'status' => 'notcounted',
		// 	'message' => 'Kas belum dihitung',
		// ], 200);
		// }else{

		// return response()->json([
		// 	'status' => 'counted',
		// 	'message' => 'Kas Sudah dihitung',
		// ], 200);	
		// }
	}


	public function postKas(Request $request)
	{
		if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
			return response()->json([
				'status' => 'failed',
				'message' => 'Invalid Username / PIN'
			], 200);
		$user = $request->user();
		$role = Role::where('user_id',$user->id)
                    ->whereIn('level_id',['1','2'])->count();

        

		if($role > 0){

			DB::beginTransaction();
			try {
				$kas = Kas::create(array(
					'diskon'		=> 0,
					'total_refund'  => 0,
					'tgl_hitung'     => null,
  					'user_id'       => $request[0]['user_id'],
					'saldo_awal'    => $request[0]['saldo_awal'],
					'transaksi'     => $request[0]['transaksi'],
					'saldo_akhir'   => $request[0]['saldo_akhir']
				));
            // return response($result);
				DB::commit();

				return response()->json([
					'status' => 'success',
					'message' => $kas,
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
				'message' => 'Invalid Username / PIN'
			], 200);
		}
        // return $users;


	}


  public function getTrxTest(){

  		$db = DB::table('transaksi')->join('preorders','preorders.transaksi_id','=','transaksi.id')->
  			where('transaksi.status','5')->sum('preorders.uang_muka');

  		return response()->json(['data'=> $db]);
  }

  public function getTrx($id)
    {

       
       	$qr = Kas::where('user_id',$id)->where('status','0')->orderBy('id','DESC')->first();
    	$waktu = $qr['created_at'];
    

    	// total transaksi gabungan order + pesanan  yg telah di bayar
        $totalTranskasiPaid = DB::table('transaksi')
        ->where('tgl_bayar', '>', $waktu)
        ->where('status','5')
        ->where('kasir_id',$id)
        ->sum('total_bayar');



	// total order bukan pesanan transaksi yg telah di bayar
        $totalOrders = DB::table('transaksi')
        ->where('tgl_bayar', '>', $waktu)
        ->where('status','5')
        ->where('jenis','1')
        ->where('kasir_id',$id)
        ->sum('total_bayar');


   //      //total pesanana uang muka
   //      $sumPreordersDP = DB::table('transaksi')->join('preorders','preorders.transaksi_id','=','transaksi.id')
   //      ->where('transaksi.created_at', '>', $waktu)
   //      ->where('transaksi.status','1') //belum bayar
   //      // ->where('hari_pelunasan','notsameday')
 		// ->where('transaksi.user_id',$id)
   //      ->where('transaksi.jenis','2')
   //      ->sum('preorders.uang_muka');


            //total pesanana uang muka
        $sumPreordersDP = DB::table('transaksi')->join('preorders','preorders.transaksi_id','=','transaksi.id')
        ->where('transaksi.created_at', '>', $waktu)
        ->where('transaksi.status','1') //belum bayar
        // ->where('hari_pelunasan','notsameday')
 		->where('transaksi.user_id',$id)
        ->where('transaksi.jenis','2')
        ->sum('preorders.uang_muka');

             //total pelunasan pesanan
        $sumPreordersPelunasan = DB::table('transaksi')->join('preorders','preorders.transaksi_id','=','transaksi.id')
        ->where('transaksi.tgl_bayar', '>', $waktu)
        ->where('transaksi.status','5') //belum bayar
        // ->where('hari_pelunasan','notsameday')
 		->where('transaksi.kasir_id',$id)
        ->where('transaksi.jenis','2')
        ->sum('preorders.sisa_bayar');


          //diskon order
        $sumDiskonOrder = DB::table('transaksi')->join('r_order','r_order.transaksi_id','=','transaksi.id')
        ->where('transaksi.tgl_bayar', '>', $waktu)
        ->where('transaksi.status','5') //belum bayar
        // ->where('hari_pelunasan','notsameday')
 		->where('transaksi.kasir_id',$id)
        ->sum('r_order.discount');

              //diskon pesanan
        $sumDiskonPesanan = DB::table('transaksi')->join('preorders','preorders.transaksi_id','=','transaksi.id')
        ->where('transaksi.created_at', '>', $waktu)
        // ->where('hari_pelunasan','notsameday')
 		->where('transaksi.kasir_id',$id)
        ->sum('preorders.discount');
 
        $diskon =  $sumDiskonPesanan + $sumDiskonOrder;
        $totalKeseluruhan = ($totalTranskasiPaid + $sumPreordersDP) - $diskon;

        return response()->json(array(
            'total_transaksi' => (int)$totalKeseluruhan,
            'total_orders' => (int)$totalOrders,
            'total_dp_preorders' => (int)$sumPreordersDP,
            'total_pelunasan_preorders' => (int)$sumPreordersPelunasan,
            'diskon' => $diskon,
            'saldo_awal' =>  $qr['saldo_awal'],
            'total_refund' => 0
        ), 200);

        
    }


    public function CheckApproval(Request $request)
    {

        if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
			return response()->json([
				'status' => 'failed',
				'message' => 'Invalid Username / PIN'
			], 200);
		$user = $request->user();
		$role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2'])->count();

		if($role > 0){
            return response()->json([
                    'status' => 'success',
                    'message' => 'user approve',
            ], 200);        

        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not approve'
            ], 200);
        }
    }


  public function updateKas(Request $request,$id)
    {
        if(!Auth::attempt(['name' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'] ]))
			return response()->json([
				'status' => 'failed',
				'message' => 'Invalid Username / PIN'
			], 200);
		$user = $request->user();
		$role = Role::where('user_id',$user->id)->whereIn('level_id',['1','2'])->count();

		if($role > 0){
        
            
            $transaksi = $request[0]['transaksi'];
            $saldo_akhir = $request[0]['saldo_akhir'];
            $diskon = $request[0]['diskon'];
            $tgl_hitung = $request[0]['tgl_hitung'];
            $refund = $request[0]['refund'];
       

            // return $saldo_akhir;

            $kas = DB::table('kas')->where('id', $id)->update(
                [
                'transaksi'   => $transaksi,
                'saldo_akhir' => $saldo_akhir,
                'diskon' => $diskon,
                'tgl_hitung' => $tgl_hitung,
                'total_refund' => $refund,
                'status' => '1'
                ]
            );

          
            return response()->json([
                'status' => 'success',
                'message' => $kas,
            ]);        
        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid Username / PIN'
            ], 200);
        }
        // return $users;
        
        
    }



}
