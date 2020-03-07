<?php

namespace App;
use App\GambarItem;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $table = "item";
  protected $fillable = ['kategori_id','nama_item','harga','margin','stock','v_rasa','deskripsi','diinput_by',
    					   'status_aktif','code'];

  protected $appends = array('GambarUtama');

  public function getGambarUtamaAttribute()
  {
     $gambarItem = GambarItem::where([ ['utama','=','1'],['item_id','=',$this->id] ])->select('gambar')->first();
     return $gambarItem->gambar;
  }

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

   public function ItemTransaksi()
   {
      return $this->hasMany(ItemTransaksi::class);
   }
}
