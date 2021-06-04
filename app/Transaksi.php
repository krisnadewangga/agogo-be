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
                 'kasir_id',
                 'for_ps','waktu_kirim_tf','top_up'];

    protected $dates = ['waktu_kirim','tgl_bayar','waktu_kirim_tf'];

  	protected $appends = ['ket_metodepembayaran','ket_status_transaksi','ket_kode_status'];

    public function getKetMetodepembayaranAttribute()
    {
      $attr = $this->metode_pembayaran;
      
      if($attr == "1"){
         $ket = "saldo";
      }else if($attr == "2"){
         $ket = "Transfer";
      }else if($attr == "3"){
         $ket = "Toko";
      }else{
        $ket = "COD";
      }

      return $ket;
    }

    public function getKetStatusTransaksiAttribute()
    {
      $attr = $this->status;
      $mp = $this->metode_pembayaran;

      if($mp == "1" || $mp == "2"){
         if($attr == "1"){
           $ket = "Dikemas";
         }else if($attr == "2"){
           $ket = "Dikirim";
         }else if($attr == "5"){
           $ket = "Terima";
         }else if($attr == "3"){
           $ket = "Dibatalkan";
         }else if($attr == "6"){
           $ket = "Menunggu Transfer";
         }else if($attr == "4"){
           $ket = "Pengajuan Pembatalan";
         }else{
           $ket = "";
         }
      }else{
         if($attr == "1"){
           $ket = "Dikemas";
         }else if($attr == "5"){
           $ket = "Terima";
         }else if($attr == "3"){
           $ket = "Dibatalkan";
         }else if($attr == "4"){
           $ket = "Pengajuan Pembatalan";
         }else {
           $ket = "";
         }
      }

      return $ket;
    }

    public function getKetKodeStatusAttribute()
    {
      $attr = $this->status;
      $mp = $this->metode_pembayaran;
      if($mp == "1" || $mp == "2"){
         if($attr == "1"){
           $ket = "#3728ff";
         }else if($attr == "2"){
           $ket = "#9a29ad";
         }else if($attr == "5"){
           $ket = "#64cd2a";
         }else if($attr == "3"){
           $ket = "#f83435";
         }else if($attr == "6"){
           $ket = "#ffed35";
         }else if($attr == "4"){
           $ket = "#f83435";
         }else{
           $ket = "#ff9500";
         }
      }else{
         if($attr == "1"){
           $ket = "#3728ff";
         }else if($attr == "5"){
           $ket = "#64cd2a";
         }else if($attr == "3"){
           $ket = "#f83435";
         }else if($attr == "4"){
           $ket = "#f83435";
         }else {
           $ket = "#ff9500";
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
      return $this->hasOne(Preorders::class,'transaksi_id','id');
    }

    public function NotifExpired()
    {
      return $this->hasOne(Transaksi::class);
    }

    public function KasirM()
    {
      return $this->belongsTo(User::class,'kasir_id','id');
    }
    
}
