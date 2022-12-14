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
        // This one doesn't follow the naming schema
        'App\Selectlist' => 'App\Policies\ListPolicy',
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

        // Access denied for all but super-admins (using intercepting gate)
        Gate::define('show-super-admin', function ($user) {
            return false;
        });

        Gate::define('show-admin', function ($user) {
            return $user->hasAccess(['show-admin']);
        });

        Gate::define('show-dashboard', function ($user) {
            return $user->hasAccess(['show-dashboard']);
        });

        Gate::define('import-csv', function ($user) {
            return $user->hasAccess(['import-csv']);
        });

        Gate::define('import-items', function ($user) {
            return $user->hasAccess(['import-items']);
        });

        Gate::define('import-taxa', function ($user) {
            return $user->hasAccess(['import-taxa']);
        });
    }
}
