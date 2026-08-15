<?php

namespace Tests\Feature;

use App\Models\Municipio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_presents_the_route_creation_experience(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Como você quer')
            ->assertSee('Criar minha rota')
            ->assertSee('Descubra no seu ritmo')
            ->assertSee('Lugares que')
            ->assertSee('Uma rota que')
            ->assertSee('Informação para visitar com confiança')
            ->assertSee('Cada rota também')
            ->assertSee('Sua próxima');
    }

    public function test_guest_does_not_see_the_manager_area_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Área do gestor');
    }

    public function test_authenticated_user_without_permission_does_not_see_the_manager_area_link(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('Área do gestor');
    }

    public function test_manager_sees_the_manager_area_link(): void
    {
        $municipality = Municipio::create([
            'uuid' => (string) Str::uuid(),
            'nome' => 'Lucena',
            'slug' => 'lucena',
            'codigo_ibge' => '2508606',
            'uf' => 'PB',
            'nome_schema' => 'tenant_lucena',
            'status' => 'active',
            'fuso_horario' => 'America/Fortaleza',
        ]);

        $municipality->dominios()->create([
            'dominio' => 'lucena.rota-viva.test',
            'is_principal' => true,
            'verificado_em' => now(),
        ]);

        $manager = User::factory()->managerFor($municipality)->create();

        $this->actingAs($manager)
            ->get('http://lucena.rota-viva.test')
            ->assertOk()
            ->assertSee('Área do gestor');
    }
}
