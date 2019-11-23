<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemTransaksi extends Model
{
    protected $table = "item_transaksi";
    protected $fillable = ['transaksi_id','item_id','jumlah','harga','margin','diskon','harga_diskon','total'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }

    public function Item()
    {
    	return $this->belongsTo(Item::class);
    }
}
