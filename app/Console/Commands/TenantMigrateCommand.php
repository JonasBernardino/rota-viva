<?php

namespace App\Console\Commands;

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
    protected $description = 'Compatibility command: municipal tables are now migrated globally';

    /**
     * Execute the console command.
     */
    public function handle(TenantManager $tenantManager): int
    {
        $this->components->info('O Rota Viva agora usa multi-tenancy por coluna.');
        $this->components->info('As tabelas municipais compartilhadas são migradas pelo comando padrão: php artisan migrate.');

        return self::SUCCESS;
    }
}
