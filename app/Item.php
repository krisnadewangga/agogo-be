<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = "item";
    protected $fillable = ['kategori_id','nama_item','harga','margin','stock','v_rasa','deskripsi','diinput_by',
    					   'status_aktif'];

   
   public function Kategori()
   {
   		return $this->belongsTo(Kategori::class);
   }

   public function GambarItem()
   {
   		return $this->hasMany(GambarItem::class);
   }

   public function Stocker()
   {
   		return $this->hasMany(Stocker::class);
   }
}
