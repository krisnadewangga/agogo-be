<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    protected $table = 'opname';
    protected $fillable = ['item_id','stock_masuk','stock_akhir','stock_toko','tanggal'];

    protected $dates = ['tanggal'];
    
    public function Item()
    {
    	return $this->belongsTo(Item::class);
    }
}
