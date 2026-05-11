<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use App\User;
use App\Role;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // Temporarily simplified for debugging - remove role check
        $email = $request->email;
        $user = User::where('email', $email)->first();
        
        \Log::info('Login attempt', ['email' => $email, 'user_found' => isset($user->id)]);
        
        if(isset($user->id)){
            \Log::info('User found', ['user_id' => $user->id, 'status' => $user->status_aktif]);
            $request->merge(['status_aktif' => '1']);
            $creds = $this->credentials($request);
            \Log::info('Credentials', $creds);
            $attempt = $this->guard()->attempt($creds, $request->filled('remember'));
            \Log::info('Auth attempt result', ['result' => $attempt]);
            return $attempt;
        }else{
            \Log::warning('User not found', ['email' => $email]);
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {

        return $request->only($this->username(), 'password','status_aktif');
    }
}
