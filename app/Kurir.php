<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kurir extends Model
{
    protected $table = "kurir";
    protected $fillable = ['nama','no_hp','jenis_kendaraan', 'merek','no_polisi','foto', 'status_aktif'];

    public function Pengiriman()
    {
    	return $this->hasMany(Pengiriman::class);
    }
}
