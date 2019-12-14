<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogBan extends Model
{
    protected $table = "log_ban";
    protected $fillable = ['user_id','status_ban','input_by'];

    public function User()
    {
        return belongsTo(User::class);
    }
}
