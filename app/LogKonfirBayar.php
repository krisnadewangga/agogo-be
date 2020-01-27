<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogKonfirBayar extends Model
{
    protected $table = "log_konfir_bayar";
    protected $fillable = ['transaksi_id','input_by'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}
