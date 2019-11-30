<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ongkir extends Model
{
    protected $table = "ongkir";
    protected $fillable = ["biaya_ongkir","dibuat_oleh"];
    
}
