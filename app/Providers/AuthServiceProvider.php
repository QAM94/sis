<?php

namespace App\Providers;

use App\Policies\AuthPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Populate the $this->policies array
        /*foreach (config('filament.models') as $model) {
            $this->policies[$model] = AuthPolicy::class;
        }*/

        // Register the policies
        //$this->registerPolicies();

        // Optional: Super admin override
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
