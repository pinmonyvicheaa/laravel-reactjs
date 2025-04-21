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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define a gate for admin role
        Gate::define('admin', function ($user) {
            return $user->role === 'admin'; // Check if the user's role is 'admin'
        });

        // Define a gate for customer role
        Gate::define('customer', function ($user) {
            return $user->role === 'customer'; // Check if the user's role is 'customer'
        });
    }
}