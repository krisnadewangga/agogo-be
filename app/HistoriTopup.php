<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoriTopup extends Model
{
    protected $table = "histori_topup";
    protected $fillable = ["user_id","nominal","ditopup_oleh"];

    public function User()
    {
    	return $this->belongsTo(User::class);
    }
    
}
