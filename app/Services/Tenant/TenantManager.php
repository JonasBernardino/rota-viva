<?php

namespace App\Services\Tenant;

use App\Models\DominioMunicipio;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TenantManager
{
    /**
     * The currently resolved municipality for this request / process.
     */
    protected ?Municipio $currentTenant = null;

    /**
     * Get the currently active tenant.
     */
    public function current(): ?Municipio
    {
        return $this->currentTenant;
    }

    public function currentOrFail(): Municipio
    {
        return $this->currentTenant
            ?? throw new ModelNotFoundException('Município não identificado para esta requisição.');
    }

    /**
     * Check if a tenant is active in context.
     */
    public function hasTenant(): bool
    {
        return $this->currentTenant !== null;
    }

    public function switchTo(Municipio $municipio): void
    {
        $this->currentTenant = $municipio;
    }

    public function reset(): void
    {
        $this->currentTenant = null;
    }

    /**
     * Backward-compatible no-op. Column-based tenancy does not create schemas.
     */
    public function createSchema(string $schemaName): void
    {
        // Column-based tenancy does not create per-municipality schemas.
    }

    /**
     * Backward-compatible no-op. Column-based tenancy does not drop schemas.
     */
    public function dropSchema(string $schemaName): void
    {
        // Column-based tenancy does not drop per-municipality schemas.
    }

    /**
     * Backward-compatible no-op. Shared municipal tables are migrated once.
     */
    public function migrateTenant(Municipio $municipio): void
    {
        // Shared municipal tables are migrated once by the normal migration pipeline.
    }

    /**
     * Create a municipality record and its known domains.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<int, string>  $domains
     */
    public function createTenant(array $attributes, array $domains = []): Municipio
    {
        if (empty($attributes['uuid'])) {
            $attributes['uuid'] = (string) Str::uuid();
        }

        if (empty($attributes['slug'])) {
            $attributes['slug'] = Str::slug($attributes['nome'] ?? 'tenant');
        }

        if (empty($attributes['nome_schema'])) {
            $sanitizedSlug = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $attributes['slug']) ?: 'tenant');
            $attributes['nome_schema'] = 'tenant_'.$sanitizedSlug;
        }

        return DB::transaction(function () use ($attributes, $domains): Municipio {
            /** @var Municipio $municipio */
            $municipio = Municipio::create($attributes);

            if (empty($domains)) {
                $domains = [$municipio->slug.'.rotaviva.com.br'];
            }

            foreach ($domains as $index => $domain) {
                DominioMunicipio::create([
                    'municipio_id' => $municipio->id,
                    'dominio' => $domain,
                    'is_principal' => $index === 0,
                    'verificado_em' => now(),
                ]);
            }

            return $municipio->load('dominios');
        });
    }

    /**
     * Resolve municipality by domain name or fallback identifier.
     */
    public function resolveByDomain(string $domain): ?Municipio
    {
        $normalizedDomain = strtolower(trim($domain));

        // Strip port if present (e.g., localhost:8000 -> localhost)
        if (str_contains($normalizedDomain, ':')) {
            $normalizedDomain = explode(':', $normalizedDomain)[0];
        }

        try {
            if (! Schema::hasTable('dominios_municipios')) {
                return null;
            }
        } catch (\Throwable) {
            return null;
        }

        /** @var DominioMunicipio|null $domainRecord */
        $domainRecord = DominioMunicipio::with('municipio')
            ->where('dominio', $normalizedDomain)
            ->first();

        if ($domainRecord && $domainRecord->municipio && $domainRecord->municipio->isActive()) {
            return $domainRecord->municipio;
        }

        $municipalitySlug = $this->slugFromLocalRotaVivaDomain($normalizedDomain);

        // Check if domain starts with a municipality slug (e.g. lucena.rota-viva.test or lucena.localhost)
        $subdomain = $municipalitySlug ?? explode('.', $normalizedDomain)[0];
        if (! empty($subdomain) && $subdomain !== 'www' && $subdomain !== 'rotaviva' && $subdomain !== 'rota-viva' && $subdomain !== 'localhost' && $subdomain !== '127') {
            /** @var Municipio|null $municipio */
            $municipio = Municipio::where('slug', $subdomain)
                ->where('status', 'active')
                ->first();

            if ($municipio) {
                return $municipio;
            }
        }

        // Fallback for local development if only 1 active municipality exists
        if (app()->environment('local', 'testing') && ($normalizedDomain === 'localhost' || $normalizedDomain === '127.0.0.1')) {
            return Municipio::where('status', 'active')->first();
        }

        return null;
    }

    private function slugFromLocalRotaVivaDomain(string $domain): ?string
    {
        $parts = explode('.', $domain);

        if (
            count($parts) === 4
            && $parts[0] === 'rota-viva'
            && $parts[3] === 'test'
        ) {
            return $parts[1];
        }

        return null;
    }
}
