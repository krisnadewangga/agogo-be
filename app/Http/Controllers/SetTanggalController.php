<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produksi;
use App\Item;
use App\Kas;
use Carbon\Carbon;
use Auth;

class SetTanggalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tanggal = Produksi::selectRaw('DATE(created_at) as tanggal')
                             ->distinct('created_at')
                             ->orderBy('tanggal','DESC')
                             ->get();
      
        $menu_active = "master|st|0";
        return view('set_tanggal.index', compact('tanggal','menu_active'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $tgl_skrang = Carbon::now()->format('Y-m-d');
      $cek = Produksi::whereDate('created_at',$tgl_skrang)->count();
        
      if($cek == 0){
         $cek_kas = Kas::where('status','0')->get();
         $belum_tutup_kas = $cek_kas->count();

         if($belum_tutup_kas > 0){
            $nama_kasir = "<ul>";
            foreach($cek_kas as $key){
              $nama_kasir .= '<li>'.$key->User->name.'</li>';
            }
            $nama_kasir .= "</ul>";
            
            return redirect()->back()->with('error','Ada Kasir Yang Belum Menutup Kas '.$nama_kasir);
         }

        $item = Item::select('id','stock as sisa_stock')->where('status_aktif','1')->get();
        
        $item->map(function($item){
          $item['produksi1'] = 0;
          $item['produksi2'] = 0;
          $item['produksi3'] = 0;
          $item['total_produksi'] = 0;
          $item['penjualan_toko'] = 0;
          $item['penjualan_pemesanan'] = 0;
          $item['total_penjualan'] = 0;
          $item['ket_rusak'] = 0;
          $item['ket_lain'] = 0;
          $item['total_lain'] = 0;
          $item['catatan'] = 'tidak ada catatan';
          $item['stock_awal'] = $item->sisa_stock;

          return $item;
        });
          

        foreach ($item as $key ) {
          if($key['sisa_stock'] < 0){
            $sisa_stock = 0;
            Item::where('id',$key['id'])->update(['stock' => '0']);
            
          }else{
            $sisa_stock = $key['sisa_stock'];
          }

          $array = ['item_id' => $key['id'],
                     'produksi1' => 0,
                     'produksi2' => 0,
                     'produksi3' => 0,
                     'total_produksi' => 0,
                     'penjualan_toko' => 0,
                     'penjualan_pemesanan' => 0,
                     'total_penjualan' => 0,
                     'ket_rusak' => 0,
                     'ket_lain' => 0,
                     'total_lain' => 0,
                     'catatan' => 'tidak ada catatan',
                     'stock_awal' => $sisa_stock,
                     'sisa_stock' => $sisa_stock
                   ];

          Produksi::create($array);
        }

        return redirect()->back()->with('success','Berhasil Set Tanggal Produksi '.Carbon::now()->format('d/m/Y'));
      }else{
        return redirect()->back()->with('error','Tanggal '.Carbon::now()->format('d/m/Y').' Sudah Di Set Sebelumnya');
      }
    }

}
