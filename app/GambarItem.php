<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GambarItem extends Model
{
    protected $table = "gambar_item";
    protected $fillable = ['item_id','gambar','utama'];

    public function Item()
    {
    	return $this->belongsTo(Kategori::class);
    }
}
