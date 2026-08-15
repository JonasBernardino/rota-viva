<?php

namespace Tests\Feature\Tenant;

use App\Models\Atrativo;
use App\Models\Categoria;
use App\Models\Municipio;
use App\Services\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected TenantManager $tenantManager;

    protected Municipio $lucena;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantManager = app(TenantManager::class);

        // Seed demo data
        $this->seed();

        $this->lucena = Municipio::where('slug', 'lucena')->firstOrFail();
    }

    public function test_it_resolves_active_tenant_by_host_domain(): void
    {
        $response = $this->withServerVariables(['HTTP_HOST' => 'lucena.rota-viva.test'])
            ->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('currentTenant', fn (?Municipio $tenant) => $tenant?->slug === 'lucena');
    }

    public function test_it_resolves_tenant_by_x_tenant_header(): void
    {
        $response = $this->withHeader('X-Tenant', 'lucena')
            ->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('currentTenant', fn (?Municipio $tenant) => $tenant?->slug === 'lucena');
    }

    public function test_it_isolates_data_between_tenant_schemas(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('PostgreSQL schema isolation requires a pgsql database driver.');
        }

        // 1. Check Lucena has places from seeder
        $this->tenantManager->switchTo($this->lucena);
        $lucenaPlaceCount = Atrativo::count();
        $this->assertGreaterThan(0, $lucenaPlaceCount);
        $this->assertTrue(Atrativo::where('slug', 'igreja-nossa-senhora-da-guia')->exists());

        // 2. Create second municipality: Cabedelo
        $cabedelo = $this->tenantManager->createTenant([
            'nome' => 'Cabedelo',
            'slug' => 'cabedelo',
            'codigo_ibge' => '2503201',
            'uf' => 'PB',
            'nome_schema' => 'tenant_cabedelo',
            'status' => 'active',
            'fuso_horario' => 'America/Fortaleza',
        ], ['cabedelo.rotaviva.com.br']);

        // 3. Switch to Cabedelo schema
        $this->tenantManager->switchTo($cabedelo);
        $this->assertSame(0, Atrativo::count());
        $this->assertFalse(Atrativo::where('slug', 'igreja-nossa-senhora-da-guia')->exists());

        // 4. Insert place in Cabedelo
        $cat = Categoria::create([
            'nome' => 'Histórico Cabedelo',
            'slug' => 'historico-cabedelo',
        ]);

        Atrativo::create([
            'categoria_id' => $cat->id,
            'nome' => 'Fortaleza de Santa Catarina',
            'slug' => 'fortaleza-santa-catarina',
            'descricao' => 'Forte histórico em Cabedelo.',
            'duracao_minutos' => 60,
            'custo_medio' => 15.00,
            'is_ar_livre' => false,
            'adequado_criancas' => true,
            'latitude' => -7.0200000,
            'longitude' => -34.8300000,
            'is_disponivel' => true,
        ]);

        $this->assertSame(1, Atrativo::count());
        $this->assertTrue(Atrativo::where('slug', 'fortaleza-santa-catarina')->exists());

        // 5. Switch back to Lucena and verify Cabedelo place is NOT present
        $this->tenantManager->switchTo($this->lucena);
        $this->assertSame($lucenaPlaceCount, Atrativo::count());
        $this->assertFalse(Atrativo::where('slug', 'fortaleza-santa-catarina')->exists());

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
