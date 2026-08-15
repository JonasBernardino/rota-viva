<?php

namespace App\Console\Commands;

use App\Models\Municipio;
use App\Services\Tenant\TenantManager;
use Illuminate\Console\Command;

class TenantMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate {tenant? : The slug or UUID of the municipality}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations on tenant PostgreSQL schemas';

    /**
     * Execute the console command.
     */
    public function handle(TenantManager $tenantManager): int
    {
        $tenantIdentifier = $this->argument('tenant');

        if ($tenantIdentifier) {
            $municipalities = Municipio::where('slug', $tenantIdentifier)
                ->orWhere('uuid', $tenantIdentifier)
                ->get();
        } else {
            $municipalities = Municipio::all();
        }

        if ($municipalities->isEmpty()) {
            $this->warn('No municipalities found to migrate.');

            return self::SUCCESS;
        }

        foreach ($municipalities as $municipality) {
            $this->info("Migrating schema [{$municipality->nome_schema}] for municipality [{$municipality->nome}]...");

            try {
                $tenantManager->migrateTenant($municipality);
                $this->info("✓ Successfully migrated [{$municipality->nome}].");
            } catch (\Throwable $e) {
                $this->error("✗ Failed to migrate [{$municipality->nome}]: {$e->getMessage()}");

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
