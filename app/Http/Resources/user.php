<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Role;
class user extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        $roles = User::get();
        //$get_role_name = $this->getRoleNames();
        $get_role_name = $this->roles->pluck('level_id');
        foreach ($roles as $role) {
            $this->roles->contains($role->id);
        }
        $role_name = $get_role_name;

        return [
            'id'        => $this->id,
            'username'  => $this->name,
            'email'     => $this->email,
            'name'      => $this->name,
            'photo'         => 'https://www.pngkey.com/png/detail/72-729716_user-avatar-png-graphic-free-download-icon.png',                 
            // 'photo'         => 'http://10.254.128.66:82/uploads/profile/' . $this->photo,            
            'role'      => $role_name,

            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];

        // return response($response)->header('Cache-Control', 'no-cache, must-revalidate')
        // //                           ->header('access-control-allow-header','Content-Type, Authorization');

        // //                           $response = array (
        // //                             'id'        => $this->id,
        // //                             'username'  => $this->username,
        // //                             'email'     => $this->email,
        // //                             'role'      => $role_name,
        // //                             // 'created_at' => $this->created_at,
        // //                             // 'updated_at' => $this->updated_at,
        // //                         );
                        
        // //                         return response($response)->header('Cache-Control', 'no-cache, must-revalidate')
        // //                                                   ->header('access-control-allow-header','Content-Type, Authorization');
    }
}
