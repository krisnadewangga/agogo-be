<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preorders extends Model
{
    //

    protected $guarded = [];
    

     public function Transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
