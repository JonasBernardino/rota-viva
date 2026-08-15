<?php

namespace App\Providers;

use App\Contracts\AiProvider;
use App\Contracts\AtrativoRepository;
use App\Models\User;
use App\Repositories\EloquentAtrativoRepository;
use App\Services\Ai\GeminiProvider;
use App\Services\Tenant\TenantManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, function () {
            return new TenantManager;
        });

        $this->app->bind(
            AiProvider::class,
            GeminiProvider::class
        );

        $this->app->bind(
            AtrativoRepository::class,
            EloquentAtrativoRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin-panel', function (User $user): bool {
            return (bool) ($user->can_access_admin_panel ?? false);
        });

        View::composer('*', function ($view): void {
            $tenantManager = $this->app->make(TenantManager::class);
            $view->with('currentTenant', $tenantManager->current());
        });
    }
}
