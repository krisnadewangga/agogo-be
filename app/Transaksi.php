<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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
                 'tgl_bayar',
    					   'status'];
    protected $dates = ['waktu_kirim','tgl_bayar','tgl_bayar'];

  	protected $appends = array('ket_metodepembayaran','ket_status_transaksi');

    public function getKetMetodepembayaranAttribute()
    {
      $attr = $this->metode_pembayaran;
      
      if($attr == "1"){
         $ket = "TopUp";
      }else if($attr == "2"){
         $ket = "Bank Transfer";
      }else if($attr == "3"){
         $ket = "Bayar Di Toko";
      }

      return $ket;
    }

    public function getKetStatusTransaksiAttribute()
    {
      $attr = $this->status;
      $mp = $this->metode_pembayaran;

      if($mp == "1" || $mp == "2"){
         if($attr == "1"){
           $ket = "Mempersiapkan Pesanan";
         }else if($attr == "2"){
           $ket = "Pengiriman Pesanan";
         }else if($attr == "5"){
           $ket = "Pesanan Diterima";
         }else if($attr == "3"){
           $ket = "Pesanan Dibatalkan";
         }else if($attr == "6"){
           $ket = "Menunggu Pembayaran";
         }
      }else{
         if($attr == "1"){
           $ket = "Menunggu Pengambilan Pesanan";
         }else if($attr == "5"){
           $ket = "Pesanan Telah Diambil";
         }else if($attr == "3"){
           $ket = "Pesanan Dibatalkan";
         }
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

    public function AmbilPesanan()
    {
      return $this->hasOne(AmbilPesanan::class);
    }

    public function BatalPesanan()
    {
      return $this->hasOne(BatalPesanan::class);
    }

    public function LogKonfirBayar()
    {
      return $this->hasOne(LogKonfirBayar::class);
    }

    public function AjukanBatalPesanan()
    {
      return $this->hasOne(AjukanBatalPesanan::class);
    }

}
