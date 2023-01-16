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

        /* define a admin user role */
        Gate::define('admin', function ($user) {
            return $user->role == 'admin';
        });

        /* define a librarian user role */
        Gate::define('librarian', function ($user) {
            return $user->role == 'librarian';
        });

        /* define a user role */
        Gate::define('user', function ($user) {
            return $user->role == 'user';
        });
    }
}
