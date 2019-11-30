<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatalPesanan extends Model
{
    protected $table = "batal_pesanan";
    protected $fillable = ['transaksi_id','input_by'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}
