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
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Local::class => \App\Policies\LocalPolicy::class,
        \App\Models\Igreja::class => \App\Policies\IgrejaPolicy::class,
        \App\Models\Setor::class => \App\Policies\SetorPolicy::class,
        \App\Models\Dependencia::class => \App\Policies\DependenciaPolicy::class,
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
