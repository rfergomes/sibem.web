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
        \Illuminate\Support\Facades\View::composer(['layouts.app', 'components.sidebar'], function ($view) {
            // Sidebar pending requests logic
            if ($view->getName() === 'components.sidebar') {
                $pendingAccessRequests = 0;
                if (auth()->check() && auth()->user()->perfil_id <= 2) { // Assuming 1=Admin, 2=Regional
                    $pendingAccessRequests = \App\Models\SolicitacaoAcesso::where('status', 'pending')->count();
                }
                $view->with('pendingAccessRequests', $pendingAccessRequests);
            }

            // Breadcrumbs logic for App Layout
            if ($view->getName() === 'layouts.app') {
                $breadcrumbService = new \App\Services\BreadcrumbService();
                $view->with('breadcrumbs', $breadcrumbService->generate());
            }
        });
    }
}
