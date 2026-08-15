<?php

namespace App\Providers;

use App\Contracts\AiProvider;
use App\Contracts\AtrativoRepository;
use App\Models\User;
use App\Repositories\EloquentAtrativoRepository;
use App\Services\Ai\DeepSeekProvider;
use App\Services\Ai\GeminiProvider;
use App\Services\Ai\OllamaProvider;
use App\Services\Tenant\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            TenantManager::class,
            function () {
                return new TenantManager;
            }
        );

        $this->app->bind(
            AiProvider::class,
            function (): AiProvider {
                return match (
                    config(
                        'services.ai.provider',
                        'deepseek'
                    )
                ) {
                    'deepseek' => new DeepSeekProvider(
                        apiKey: (string) config(
                            'services.ai.deepseek.api_key'
                        ),

                        model: (string) config(
                            'services.ai.deepseek.model',
                            'deepseek-v4-flash'
                        ),

                        baseUrl: (string) config(
                            'services.ai.deepseek.base_url',
                            'https://api.deepseek.com'
                        ),
                    ),

                    'gemini' => new GeminiProvider(
                        apiKey: (string) config(
                            'services.ai.gemini.api_key'
                        ),

                        model: (string) config(
                            'services.ai.gemini.model',
                            'gemini-3-flash-preview'
                        ),
                    ),

                    'ollama' => new OllamaProvider(
                        baseUrl: (string) config(
                            'services.ai.ollama.base_url',
                            'http://127.0.0.1:11434'
                        ),

                        model: (string) config(
                            'services.ai.ollama.model',
                            'qwen2.5-coder'
                        ),

                        timeoutSeconds: (int) config(
                            'services.ai.ollama.timeout',
                            8
                        ),
                    ),

                    default => throw new InvalidArgumentException(
                        'AI_PROVIDER inválido. Use deepseek, gemini ou ollama.'
                    ),
                };
            }
        );

        $this->app->bind(
            AtrativoRepository::class,
            EloquentAtrativoRepository::class
        );
    }

    public function boot(): void
    {
        Gate::define('access-admin-panel', function (User $user): bool {
            if (! (bool) ($user->can_access_admin_panel ?? false)) {
                return false;
            }

            $municipality = app(TenantManager::class)->current();

            return $municipality !== null
                && (int) $user->municipio_id === (int) $municipality->id;
        });

        Gate::define('manage-platform', function (User $user): bool {
            return (bool) ($user->can_manage_platform ?? false);
        });

        View::composer('*', function ($view): void {
            $tenantManager = $this->app->make(TenantManager::class);

            $view->with('currentTenant', $tenantManager->current());

            $municipalityOptions = collect();

            try {
                $municipalitiesTable = DB::connection()->getDriverName() === 'pgsql'
                    ? 'public.municipios'
                    : 'municipios';

                if (Schema::hasTable('municipios') || DB::connection()->getDriverName() === 'pgsql') {
                    $municipalityOptions = DB::table($municipalitiesTable)
                        ->where('status', 'active')
                        ->orderBy('nome')
                        ->get(['id', 'nome', 'slug']);
                }
            } catch (\Throwable) {
                $municipalityOptions = collect();
            }

            $view->with('municipalityOptions', $municipalityOptions);
        });
    }
}
