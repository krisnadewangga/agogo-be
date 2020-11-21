<?php

namespace App\Providers;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        
        Gate::define('add_users', function($user){
            $roles = $user->Roles->pluck('level_id')->toArray();
            if( (in_array('1', $roles) || in_array('2', $roles)  ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage-kurirs',function($user){
            $roles = $user->Roles->pluck('level_id')->toArray();
            if( (in_array('1', $roles) || in_array('2', $roles)  ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage_items',function($user){
            $roles = $user->Roles->pluck('level_id')->toArray();
            if( (in_array('1', $roles) || in_array('2', $roles)  ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage-konsu',function($user){
            $roles = $user->Roles->pluck('level_id')->toArray();
            if( (in_array('1', $roles) || in_array('2', $roles)  ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage-versi',function($user){
            $roles = $user->Roles->pluck('level_id')->toArray();
            if( (in_array('1', $roles) || in_array('2', $roles)  ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });
    }
}
