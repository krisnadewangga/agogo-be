<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['otp', 'user_id'];

    public function User()
    {
    	return $this->belongsTo(User::class);
    }
}
