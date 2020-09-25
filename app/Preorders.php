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

    public function KasirDp()
    {
    	return $this->belongsTo(User::class,'pencatat_entri','id');
    }

    public function KasirLunas()
    {
    	return $this->belongsTo(User::class,'pencatat_pengambilan','id');
    }
}
