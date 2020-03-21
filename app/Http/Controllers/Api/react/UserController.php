<?php

namespace App\Http\Controllers\Api\react;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Level;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class UserController extends Controller
{
    //

    public function getUser(){
   
    $user = User::join('roles','roles.user_id','=','users.id')
                 ->where('roles.level_id',3)
                 ->orWhere('roles.level_id',4)
                 ->orWhere('roles.level_id',5)
                 ->select('users.id','name','email','foto as photo')->distinct('id')->get();

    $user->map(function($user){ 
        $sel_role = Role::where(['user_id' => $user->id])->pluck('id');
        //$roles = explode(",", $sel_role->roles);
        $user['username'] = $user->name;
        $user['role'] = $sel_role;
        return $user;
    });
    return response()->json(['data' => $user]);
   
  }


   public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['name', 'password']);

        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $role_name = $user->roles()->pluck('level_id');
        return response()->json([
            'username'      => $user->name,
            // 'role'          => $role_name[0],
            'role'          => $role_name,
            'access_token'  => $tokenResult->accessToken,
            'token_type'    => 'Bearer',
            'expires_at'    => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

}
