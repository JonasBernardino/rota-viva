<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Área do gestor');
    }
}
