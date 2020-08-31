<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kurir extends Model
{
    protected $table = "kurir";
    protected $fillable = ['user_id','jenis_kendaraan', 'merek','no_polisi'];

    public function User()
    {
    	return $this->belongsTo(User::class);
    }
    
    public function Pengiriman()
    {
    	return $this->hasMany(Pengiriman::class);
    }
}
