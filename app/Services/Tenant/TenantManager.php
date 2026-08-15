<?php

namespace App\Services\Tenant;

use App\Models\DominioMunicipio;
use App\Models\Municipio;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;

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

    /**
     * Check if a tenant is active in context.
     */
    public function hasTenant(): bool
    {
        return $this->currentTenant !== null;
    }

    /**
     * Switch database search_path to the given municipality's schema.
     */
    public function switchTo(Municipio $municipio): void
    {
        $this->validateSchemaName($municipio->nome_schema);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("SET search_path TO \"{$municipio->nome_schema}\", public");
        }

        $this->currentTenant = $municipio;
    }

    /**
     * Reset database search_path back to the public schema.
     */
    public function reset(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('SET search_path TO public');
        }

        $this->currentTenant = null;
    }

    /**
     * Create a new PostgreSQL schema for a tenant.
     */
    public function createSchema(string $schemaName): void
    {
        $this->validateSchemaName($schemaName);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schemaName}\"");
        }
    }

    /**
     * Drop a PostgreSQL schema.
     */
    public function dropSchema(string $schemaName): void
    {
        $this->validateSchemaName($schemaName);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("DROP SCHEMA IF EXISTS \"{$schemaName}\" CASCADE");
        }
    }

    /**
     * Run tenant migrations on the municipality's schema.
     */
    public function migrateTenant(Municipio $municipio): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $this->createSchema($municipio->nome_schema);
            DB::statement("SET search_path TO \"{$municipio->nome_schema}\", public");

            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);
            } finally {
                DB::statement('SET search_path TO public');
            }
        } else {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);
        }
    }

    /**
     * Create a complete tenant: record in public, PostgreSQL schema, and runs tenant migrations.
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

        $this->validateSchemaName($attributes['nome_schema']);

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

            $this->dropSchema($municipio->nome_schema);
            $this->migrateTenant($municipio);

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

        // Check if domain starts with a municipality slug (e.g. lucena.rotaviva.test or lucena.localhost)
        $subdomain = explode('.', $normalizedDomain)[0];
        if (! empty($subdomain) && $subdomain !== 'www' && $subdomain !== 'rotaviva' && $subdomain !== 'localhost' && $subdomain !== '127') {
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

    /**
     * Validate schema name to ensure safe SQL identifiers.
     */
    protected function validateSchemaName(string $schemaName): void
    {
        if (! preg_match('/^[a-zA-Z0-9_]{1,63}$/', $schemaName)) {
            throw new InvalidArgumentException("Invalid PostgreSQL schema name: [{$schemaName}]. Must be alphanumeric/underscores only and <= 63 chars.");
        }
    }
}
