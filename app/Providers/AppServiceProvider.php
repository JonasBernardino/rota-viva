<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\AiProvider;
use App\Services\Ai\GeminiProvider;
use App\Contracts\PlaceRepository;
use App\Repositories\EloquentPlaceRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AiProvider::class,
            function () {
                return new GeminiProvider(
                    apiKey: config(
                        'services.ai.gemini.api_key'
                    ),

                    model: config(
                        'services.ai.gemini.model'
                    ),
                );
            }
        );

        $this->app->bind(
            PlaceRepository::class,
            EloquentPlaceRepository::class
        );
    }
    public function boot(): void
    {
        Gate::define('access-admin-panel', fn(User $user): bool => $user->can_access_admin_panel);
    }
}
