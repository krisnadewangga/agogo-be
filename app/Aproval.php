<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aproval extends Model
{
    protected $table = "aproval";
    protected $fillable = ['user_id','rule'];

    protected $timestamp = false;
    
    public function User()
    {
    	return $this->belongsTo(User::class);
    }
}
