<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
          return [
            'id'            => $this->id,
            'code'          => $this->code,
            'name'          => $this->name,
            'stock'         => $this->stock,
            'price'         => $this->price,
            'category_id'   => $this->category_id,
            'photo'         => 'http://101.255.125.227:82/uploads/product/' . $this->photo,
            // 'photo'         => 'http://10.254.128.66:82/uploads/product/' . $this->photo,            
            // 'cat_name'      => $role
        ];
    }
}
