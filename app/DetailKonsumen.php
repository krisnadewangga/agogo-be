<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailKonsumen extends Model
{
    protected $table = "detail_konsumen";
    protected $fillable = ['user_id','alamat','lat','long','saldo','status_member','no_aktifasi','tgl_lahir','jenis_kelamin','kunci_transaksi'];
    
    protected $dates = ['tgl_lahir'];
    
    public function User()
    {
    	return $this->belongsTo(User::class);
    }
}
