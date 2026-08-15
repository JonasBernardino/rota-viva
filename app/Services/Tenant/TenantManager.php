<?php

namespace App\Services\Tenant;

use App\Models\Municipality;
use App\Models\MunicipalityDomain;
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
    protected ?Municipality $currentTenant = null;

    /**
     * Get the currently active tenant.
     */
    public function current(): ?Municipality
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
    public function switchTo(Municipality $municipality): void
    {
        $this->validateSchemaName($municipality->schema_name);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("SET search_path TO \"{$municipality->schema_name}\", public");
        }

        $this->currentTenant = $municipality;
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
    public function migrateTenant(Municipality $municipality): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $this->createSchema($municipality->schema_name);
            DB::statement("SET search_path TO \"{$municipality->schema_name}\", public");

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
    public function createTenant(array $attributes, array $domains = []): Municipality
    {
        if (empty($attributes['uuid'])) {
            $attributes['uuid'] = (string) Str::uuid();
        }

        if (empty($attributes['slug'])) {
            $attributes['slug'] = Str::slug($attributes['name'] ?? 'tenant');
        }

        if (empty($attributes['schema_name'])) {
            $sanitizedSlug = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $attributes['slug']) ?: 'tenant');
            $attributes['schema_name'] = 'tenant_'.$sanitizedSlug;
        }

        $this->validateSchemaName($attributes['schema_name']);

        return DB::transaction(function () use ($attributes, $domains): Municipality {
            /** @var Municipality $municipality */
            $municipality = Municipality::create($attributes);

            if (empty($domains)) {
                $domains = [$municipality->slug.'.rotaviva.com.br'];
            }

            foreach ($domains as $index => $domain) {
                MunicipalityDomain::create([
                    'municipality_id' => $municipality->id,
                    'domain' => $domain,
                    'is_primary' => $index === 0,
                    'verified_at' => now(),
                ]);
            }

            $this->dropSchema($municipality->schema_name);
            $this->migrateTenant($municipality);

            return $municipality->load('domains');
        });
    }

    /**
     * Resolve municipality by domain name or fallback identifier.
     */
    public function resolveByDomain(string $domain): ?Municipality
    {
        $normalizedDomain = strtolower(trim($domain));

        // Strip port if present (e.g., localhost:8000 -> localhost)
        if (str_contains($normalizedDomain, ':')) {
            $normalizedDomain = explode(':', $normalizedDomain)[0];
        }

        try {
            if (! Schema::hasTable('municipality_domains')) {
                return null;
            }
        } catch (\Throwable) {
            return null;
        }

        /** @var MunicipalityDomain|null $domainRecord */
        $domainRecord = MunicipalityDomain::with('municipality')
            ->where('domain', $normalizedDomain)
            ->first();

        if ($domainRecord && $domainRecord->municipality && $domainRecord->municipality->isActive()) {
            return $domainRecord->municipality;
        }

        // Check if domain starts with a municipality slug (e.g. lucena.rotaviva.test or lucena.localhost)
        $subdomain = explode('.', $normalizedDomain)[0];
        if (! empty($subdomain) && $subdomain !== 'www' && $subdomain !== 'rotaviva' && $subdomain !== 'localhost' && $subdomain !== '127') {
            /** @var Municipality|null $municipality */
            $municipality = Municipality::where('slug', $subdomain)
                ->where('status', 'active')
                ->first();

            if ($municipality) {
                return $municipality;
            }
        }

        // Fallback for local development if only 1 active municipality exists
        if (app()->environment('local', 'testing') && ($normalizedDomain === 'localhost' || $normalizedDomain === '127.0.0.1')) {
            return Municipality::where('status', 'active')->first();
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
