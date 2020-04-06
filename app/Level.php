<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'level';
    protected $fillable = ['level','status_aktif'];

    public function User()
    {
    	return $this->hasMany(User::class);
    }

    public function Roles()
    {
        return $this->hasMany(Role::class);
    }
}
