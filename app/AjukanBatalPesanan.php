<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AjukanBatalPesanan extends Model
{
    protected $table = "ajukan_batal_pesanan";
    protected $fillable = ['transaksi_id','diajukan_oleh','disetujui_oleh','status'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}

