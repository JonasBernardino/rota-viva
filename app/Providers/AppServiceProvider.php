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
use RuntimeException;
use App\Services\Ai\DeepSeekProvider;

class AppServiceProvider extends ServiceProvider
{
       public function register(): void
    {
        $this->app->bind(
            AiProvider::class,
            function () {
                return match (
                    config('services.ai.provider')
                ) {
                    'deepseek' =>
                        new DeepSeekProvider(
                            apiKey:
                                config(
                                    'services.ai.deepseek.api_key'
                                ),

                            model:
                                config(
                                    'services.ai.deepseek.model'
                                ),

                            baseUrl:
                                config(
                                    'services.ai.deepseek.base_url'
                                ),
                        ),

                    'gemini' =>
                        new GeminiProvider(
                            apiKey:
                                config(
                                    'services.ai.gemini.api_key'
                                ),

                            model:
                                config(
                                    'services.ai.gemini.model'
                                ),
                        ),

                    default =>
                        throw new RuntimeException(
                            sprintf(
                                'Provider de IA "%s" não suportado.',
                                config('services.ai.provider')
                            )
                        ),
                };
            }
        );

        $this->app->bind(
            PlaceRepository::class,
            EloquentPlaceRepository::class
        );
    }

    public function boot(TenantManager $tenantManager): void
    {
        Gate::define('access-admin-panel', fn (User $user): bool => (bool) $user->can_access_admin_panel);

        view()->composer('*', function ($view) use ($tenantManager): void {
            $view->with('currentTenant', $tenantManager->current());
        });
    }
}
