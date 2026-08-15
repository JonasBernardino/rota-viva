<?php

namespace App\Providers;

use App\Contracts\AiProvider;
use App\Contracts\PlaceRepository;
use App\Models\User;
use App\Repositories\EloquentPlaceRepository;
use App\Services\Ai\GeminiProvider;
use App\Services\Tenant\TenantManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class);

        $this->app->bind(
            AiProvider::class,
            fn () => new GeminiProvider(
                apiKey: (string) config('services.ai.gemini.api_key', ''),
                model: (string) config('services.ai.gemini.model', 'gemini-1.5-flash'),
            )
        );

        $this->app->bind(
            PlaceRepository::class,
            EloquentPlaceRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(TenantManager $tenantManager): void
    {
        Gate::define('access-admin-panel', fn (User $user): bool => (bool) $user->can_access_admin_panel);

        view()->composer('*', function ($view) use ($tenantManager): void {
            $view->with('currentTenant', $tenantManager->current());
        });
    }
}
