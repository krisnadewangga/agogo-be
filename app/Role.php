<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    protected $table = "roles";
    protected $fillable = ["user_id","level_id"];

    public function User(){
    	return $this->belongsTo(User::class);
    }

    public function Level()
    {
    	return $this->belongsTo(Level::class);
    }
}
