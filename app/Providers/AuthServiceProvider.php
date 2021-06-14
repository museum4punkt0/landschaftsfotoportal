<?php

namespace App\Providers;

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
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        // Intercepting gate to grant all abilities to super-admin
        Gate::before(function ($user, $ability) {
            if ($user->inGroup('super-admin')) {
                return true;
            }
        });
        
        Gate::define('show-admin', function ($user) {
            return $user->hasAccess(['show-admin']);
        });
        
        Gate::define('show-dashboard', function ($user) {
            return $user->hasAccess(['show-dashboard']);
        });
    }
}
