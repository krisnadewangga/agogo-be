<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    //
     protected $table = "refund";
    protected $fillable = ["invoice","transaksi_id","user_id","total"];
}
