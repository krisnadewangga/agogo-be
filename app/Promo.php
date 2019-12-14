<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';
    protected $fillable = ['judul','gambar','berlaku_sampai','status','dibuat_oleh'];

    protected $dates = ['berlaku_sampai'];

}
