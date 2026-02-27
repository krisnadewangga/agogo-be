<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetProduksi extends Model
{
    //
	protected $table = "target_produksi";

	protected $fillable = [
		'item_id',
		'target_produksi',
		'realisasi_produksi',
		'created_at',
		'updated_at',
		'target_date',
		];




	public function Item()
	{
		return $this->belongsTo(Item::class);
	}
}