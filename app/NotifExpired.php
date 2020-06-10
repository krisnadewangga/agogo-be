<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifExpired extends Model
{
    protected $table = 'notif_expired';
    protected $fillable = ['transaksi_id','email','waktu_kirim','item','status'];

    protected $dates = ['waktu_kirim'];

    public function Transaksi()
    {
    	return $this->belongsTo(Transaksi::class);
    }
}
