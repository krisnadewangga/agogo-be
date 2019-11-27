<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmbilPesanan extends Model
{
    protected $table = "ambil_pesanan";
    protected $fillable = ['transaksi_id','diambil_oleh','input_by'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}
