<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    //
	protected $table = "produksi";

	protected $fillable = [
	
		'item_id',
		'produksi1',
		'produksi2',
		'produksi3',
		'total_produksi',
		'penjualan_toko',
		'penjualan_pemesanan',
		'total_penjualan',
		'ket_rusak',
		'ket_lain',
		'total_lain',
		'catatan',
		'stock_awal',
		'sisa_stock'];


	}
