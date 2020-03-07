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

class OrderController extends Controller
{
	public function generateInvoice($opsi)
	{
		if($opsi == "1"){
			$string_awal = "TK-";
			$select = Transaksi::where('jalur','2')->orderBy('id','DESC');
		}

		if($select->count() > 0){
			$first = $select->first();
			$explode = explode('-',$first->no_transaksi);
			$nextCode = $explode[1] + 1;
			$invoice = $string_awal.$nextCode;
		}else{
			$invoice = $string_awal."1";
		}

		return $invoice;
	}

    public function checkLastInvoice()
    {
    	$res = $this->generateInvoice('1');
    	return response()->json(['current_invoice' => $res],200);
    }

    public function postOrder(Request $request)
    {
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
    					  'metode_pembayaran' => '3',
    					  'tgl_bayar' => date("Y-m-d H:i:s"),
    					  'waktu_kirim' => date("Y-m-d H:i:s")
    					];
    	
      if(!empty( $request[0]['id']) ){ 
        $req_transaksi['id'] = $request[0]['id'];
      }
      
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
      $find->R_Order()->create(['transaksi_id' => $insertTransaksi->id, 
                       'uang_dibayar' => $req[0]['dibayar'],
                       'uang_kembali' => $req[0]['kembali'],
                       'status' => $req[0]['status'] ]);
    	

   		return response()->json([
					                'status' => 'success',
					                'message' => $no_transaksi,
					            ], 200);
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
                          'tgl_bayar' => date("Y-m-d H:i:s")
                         ]);
   
            foreach ($req as $key ) {
                $cek = ItemTransaksi::where([ 
                                                ['transaksi_id','=',$sel->id],
                                                ['item_id', '=', $key['product_id']]
                                            ])->first();

                if($cek->jumlah != $key['qty']){
                   $cek->update(['jumlah' => $key['qty'], 'total' => $key['price'] ]);
                }
            }
        }else{
            $sel->update(['status' => '5', 'tgl_bayar' => date("Y-m-d H:i:s") ]);
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
            $transaksi1['item_transaksi'] = $item_transaksi; 
            $success = '1';
            $message = $transaksi1;
        }else{
            $success = '0';
            $message = "No Transaksi Tidak Ditemukan";
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

}
