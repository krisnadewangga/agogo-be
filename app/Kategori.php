<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = "kategori";
    protected $fillable = ['kategori','gambar','status_aktif'];

    public function Item()
    {
    	return $this->hasMany(Item::class);
    }
}
