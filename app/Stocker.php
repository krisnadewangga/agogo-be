<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stocker extends Model
{
    protected $table = "stocker";
    protected $fillable = ['item_id','jumlah','input_by'];

    public function Item()
    {
    	return $this->belongsTo(Item::class);
    }
}
