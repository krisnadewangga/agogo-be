<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Transaksi extends Model
{
    protected $table = "transaksi";
    protected $fillable = [
                 'id',
                 'user_id',
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
    					   'status',
                 'jenis',
                 'jalur',
                 'kasir_id'];
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
      }else{
        $ket = "";
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
         }else if($attr == "4"){
           $ket = "Ajukan Pembatalan";
         }else{
           $ket = "";
         }
      }else{
         if($attr == "1"){
           $ket = "Menunggu Pengambilan Pesanan";
         }else if($attr == "5"){
           $ket = "Pesanan Telah Diambil";
         }else if($attr == "3"){
           $ket = "Pesanan Dibatalkan";
         }else if($attr == "4"){
           $ket = "Ajukan Pembatalan";
         }else {
           $ket = "";
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

    public function R_Order()
    {
      return $this->hasOne(R_Order::class);
    }

    public function Preorder()
    {
      return $this->hasOne(Preorders::class);
    }

}
