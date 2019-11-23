<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = "transaksi";
    protected $fillable = ['user_id',
    					   'no_transaksi',
    					   'total_transaksi',
    					   'biaya_pengiriman',
    					   'jarak_tempuh',
    					   'total_biaya_pengiriman',
    					   'kode_voucher',
    					   'potongan',
    					   'total_bayar',
    					   'alamat_lain',
    					   'lat',
    					   'long',
                 'detail_alamat',
    					   'metode_pembayaran',
    					   'transaksi_member',
                 'banyak_item',
                 'catatan',
                 'durasi_kirim',
                 'waktu_kirim',
    					   'status'];

  	protected $appends = array('ket_metodepembayaran');

    public function getKetMetodepembayaranAttribute()
    {
      $attr = $this->metode_pembayaran;
      
      if($attr == "1"){
         $ket = "TopUp";
      }else if($attr == "2"){
         $ket = "COD";
      }else if($attr == "3"){
         $ket = "Bayar Langsung";
      }

      return $ket;
    }

  	public function User()
  	{
  		return $this->belongsTo(User::class);
  	}
  	
  	public function Kurir()
  	{
  		return $this->belongsTo(Kurir::class);
  	}

    public function ItemTransaksi()
    {
      return $this->hasMany(ItemTransaksi::class);
    }

    public function Pengiriman()
    {
      return $this->hasOne(Pengiriman::class);
    }

}
