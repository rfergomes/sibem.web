<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('components.sidebar', function ($view) {
            $pendingAccessRequests = 0;
            if (auth()->check() && auth()->user()->perfil_id <= 2) {
                $pendingAccessRequests = \App\Models\SolicitacaoAcesso::where('status', 'pending')->count();
            }
            $view->with('pendingAccessRequests', $pendingAccessRequests);
        });
    }
}
