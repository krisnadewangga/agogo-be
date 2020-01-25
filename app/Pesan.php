<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan';
      
    protected $fillable = ['user_id','pesan','dibaca','status','dibuat_oleh'];

    public function User()
    {
    	return $this->belongsTo(User::class);
    }

  
}
