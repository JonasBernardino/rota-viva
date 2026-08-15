<?php

namespace Tests\Feature\Tenant;

use App\Models\Category;
use App\Models\Municipality;
use App\Models\Place;
use App\Services\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected TenantManager $tenantManager;

    protected Municipality $lucena;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantManager::class);

        // Seed demo data
        $this->seed();

        $this->lucena = Municipality::where('slug', 'lucena')->firstOrFail();
    }

    public function test_it_resolves_active_tenant_by_host_domain(): void
    {
        $response = $this->withServerVariables(['HTTP_HOST' => 'lucena.rota-viva.test'])
            ->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('currentTenant', fn (?Municipality $tenant) => $tenant?->slug === 'lucena');
    }

    public function test_it_resolves_tenant_by_x_tenant_header(): void
    {
        $response = $this->withHeader('X-Tenant', 'lucena')
            ->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('currentTenant', fn (?Municipality $tenant) => $tenant?->slug === 'lucena');
    }

    public function test_it_isolates_data_between_tenant_schemas(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('PostgreSQL schema isolation requires a pgsql database driver.');
        }

        // 1. Check Lucena has places from seeder
        $this->tenantManager->switchTo($this->lucena);
        $lucenaPlaceCount = Place::count();
        $this->assertGreaterThan(0, $lucenaPlaceCount);
        $this->assertTrue(Place::where('slug', 'igreja-nossa-senhora-da-guia')->exists());

        // 2. Create second municipality: Cabedelo
        $cabedelo = $this->tenantManager->createTenant([
            'name' => 'Cabedelo',
            'slug' => 'cabedelo',
            'ibge_code' => '2503201',
            'state' => 'PB',
            'schema_name' => 'tenant_cabedelo',
            'status' => 'active',
            'timezone' => 'America/Fortaleza',
        ], ['cabedelo.rotaviva.com.br']);

        // 3. Switch to Cabedelo schema
        $this->tenantManager->switchTo($cabedelo);
        $this->assertSame(0, Place::count());
        $this->assertFalse(Place::where('slug', 'igreja-nossa-senhora-da-guia')->exists());

        // 4. Insert place in Cabedelo
        $cat = Category::create([
            'name' => 'Histórico Cabedelo',
            'slug' => 'historico-cabedelo',
        ]);

        Place::create([
            'category_id' => $cat->id,
            'name' => 'Fortaleza de Santa Catarina',
            'slug' => 'fortaleza-santa-catarina',
            'description' => 'Forte histórico em Cabedelo.',
            'is_indoor' => false,
            'is_free' => false,
            'cost' => 15.00,
        ]);

        $this->assertSame(1, Place::count());
        $this->assertTrue(Place::where('slug', 'fortaleza-santa-catarina')->exists());

        // 5. Switch back to Lucena and verify Cabedelo place is NOT present
        $this->tenantManager->switchTo($this->lucena);
        $this->assertSame($lucenaPlaceCount, Place::count());
        $this->assertFalse(Place::where('slug', 'fortaleza-santa-catarina')->exists());

        // Cleanup second schema
        $this->tenantManager->dropSchema('tenant_cabedelo');
    }

    public function test_it_resets_search_path_after_request_terminates(): void
    {
        $this->withHeader('X-Tenant', 'lucena')->get(route('home'));

        // Reset tenant
        $this->tenantManager->reset();

        $this->assertFalse($this->tenantManager->hasTenant());

        if (DB::connection()->getDriverName() === 'pgsql') {
            $searchPath = DB::selectOne('SHOW search_path')->search_path ?? '';
            $this->assertStringContainsString('public', $searchPath);
        }
    }
}
