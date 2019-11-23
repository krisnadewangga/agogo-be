<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
   	protected $table = 'notifikasi';
    protected $fillable = ['id','pengirim_id','penerima_id','judul_id','judul','isi','jenis_notif','dibaca'];

    public function getCreatedAtAttribute()
	{
	    return \Carbon\Carbon::parse($this->attributes['created_at'])->diffForHumans();
	}
}
