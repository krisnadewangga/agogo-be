<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    
    protected $table = "pengiriman";
    protected $fillable = ['kurir_id','transaksi_id','dikirimkan','diterima','diterima_oleh'];

    public function Kurir()
    {
    	return $this->belongsTo(Kurir::class);
    }

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }

}
