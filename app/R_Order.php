<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class R_Order extends Model
{
    protected $table = "r_order";
    protected $fillable = ['transaksi_id','discount','add_fee','uang_dibayar','uang_kembali','status'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}
