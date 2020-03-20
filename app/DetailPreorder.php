<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPreorder extends Model
{
    // 
    //protected $guarded = [];
    protected $table = "detail_preorder";
    protected $hidden = ['updated_at'];
   	    protected $fillable = ['preorder_id','item_id','qty','harga'];

    public function preorder()
    {
        return $this->belongsTo(Preorders::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
