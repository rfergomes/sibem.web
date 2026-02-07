<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates for CRUD permissions
        // View is allowed for all authenticated users by default in the controller (or we can add a gate)

        // Create/Edit: Perfil <= 3 (Admin Local, Regional, Sistema)
        \Illuminate\Support\Facades\Gate::define('create', function ($user, $modelClass) {
            return $user->perfil_id <= 3;
        });

        \Illuminate\Support\Facades\Gate::define('update', function ($user, $model) {
            return $user->perfil_id <= 3;
        });

        // Delete: Perfil <= 2 (Admin Regional, Sistema)
        \Illuminate\Support\Facades\Gate::define('delete', function ($user, $model) {
            return $user->perfil_id <= 2;
        });
    }
}
