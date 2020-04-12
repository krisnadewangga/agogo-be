<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preorders extends Model
{
    //

    protected $guarded = [];
    
    protected $dates = ['tgl_pesan','tgl_selesai'];
    
     public function Transaksi()
    {
        return $this->belongsTo(Transaksi::class,'id','transaksi_id');
    }
}
