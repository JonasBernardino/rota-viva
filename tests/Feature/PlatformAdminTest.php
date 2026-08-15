<?php

namespace Tests\Feature;

use App\Models\DominioMunicipio;
use App\Models\Municipio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_guest_is_redirected_to_login_when_accessing_platform_panel(): void
    {
        $this->get(route('platform.dashboard'))->assertRedirect(route('login'));
    }

    public function test_municipal_manager_cannot_access_platform_panel(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('platform.dashboard'))
            ->assertForbidden();
    }

    public function test_superadmin_can_access_platform_panel(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('platform.dashboard'))
            ->assertOk()
            ->assertSee('Painel da Plataforma')
            ->assertSee('Criar nova cidade');
    }

    public function test_superadmin_can_create_municipality_and_first_manager(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->post(route('platform.municipalities.store'), [
            'nome' => 'Cabedelo',
            'slug' => 'cabedelo',
            'codigo_ibge' => '2503201',
            'uf' => 'PB',
            'dominio' => 'cabedelo.rota-viva.test',
            'gestor_nome' => 'Gestora de Cabedelo',
            'gestor_email' => 'gestor@cabedelo.pb.gov.br',
            'gestor_senha' => 'senha-segura',
        ]);

        $response->assertRedirect(route('platform.dashboard'));
        $response->assertSessionHas('status');

        $municipality = Municipio::where('slug', 'cabedelo')->first();
        $this->assertNotNull($municipality);

        $this->assertTrue(DominioMunicipio::where('dominio', 'cabedelo.rota-viva.test')->exists());

        $manager = User::where('email', 'gestor@cabedelo.pb.gov.br')->first();
        $this->assertNotNull($manager);
        $this->assertSame($municipality->id, $manager->municipio_id);
        $this->assertTrue($manager->can_access_admin_panel);
        $this->assertFalse($manager->can_manage_platform);
    }
}
