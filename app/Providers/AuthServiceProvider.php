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
            if( ($user->level_id == "1" || $user->level_id == '2' ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage-kurirs',function($user){
            if( ($user->level_id == "1" || $user->level_id == '2' ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage_items',function($user){
            if( ($user->level_id == "1" || $user->level_id == '2' ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });

        Gate::define('manage-konsu',function($user){
            if( ($user->level_id == "1" || $user->level_id == '2' ) && $user->status_aktif == "1"){
                return true;
            }
            return false;
        });
    }
}
