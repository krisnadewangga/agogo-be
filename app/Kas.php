<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    //
    protected $table = "kas";
    protected $fillable = ['user_id','saldo_awal','transaksi','saldo_akhir','tgl_hitung','diskon','total_refund'];



    public function User()
    {
        return belongsTo(User::class);
    }
}
